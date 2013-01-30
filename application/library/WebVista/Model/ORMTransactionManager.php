<?php
/*****************************************************************************
*       ORMTransactionManager.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class WebVista_Model_ORMTransactionManager {

	protected $auditLogQueries = array();
	protected $queries = array();
	protected $ORMs = array();

	public function addQuery($query) {
		if (!is_array($query)) {
			$query = array($query);
		}
		foreach ($query as $sql) {
			$this->queries[] = $sql;
		}
	}

	public function setQueries(Array $queries) {
		$this->queries = $queries;
	}

	public function getQueries() {
		return $this->queries;
	}

	public function clearQueries() {
		$this->setQueries(array());
	}

	public function addORM(ORM $orm) {
		$this->ORMs[] = $orm;
		$ormClass = get_class($orm);
		if ($orm->shouldAudit() && ($ormClass == 'Audit' || $ormClass == 'AuditValue')) {
			$this->auditLogQueries[] = $orm->toSQL();
			return $this;
		}
		$this->queries[] = $orm->toSQL();

		//$orm->postPersist();
		if ($orm instanceof Document && $orm->signatureNeeded()) {
			$this->createSignatureEntry($orm);
		}
		if ($orm->shouldAudit() && $ormClass != 'Audit' && $ormClass != 'AuditValue') {
			$this->audit($orm);
		}
		return $this;
	}

	public function createSignatureEntry(Document $object) {
		$db = Zend_Registry::get('dbAdapter');
		if ($object->eSignatureId > 0) {
			return false;
		}
		$esig = new ESignature();
		$sqlSelect = $db->select()
				->from('eSignatures')
				->where('eSignatures.objectId = ' . (int)$object->documentId)
				->order('eSignatureId DESC');
		if (($row = $db->query($sqlSelect)->fetch()) !== false) {
			if ($row['signature'] == '') {
				//open signature record exists so do not create another
				return false;
			}
			$esig->editedSummary = $object->getSummary();
		}
		else {
			$esig->unsignedSummary = $object->getSummary();
		}
		$esig->objectId = $object->getDocumentId();
		$esig->objectClass = get_class($object);
		$esig->signingUserId = (int)Zend_Auth::getInstance()->getIdentity()->userId;
		$esig->dateTime = date('Y-m-d H:i:s');
		$this->addORM($esig);
	}

	public function audit(ORM $obj) {
		$audit = new Audit();
		$audit->objectClass = get_class($obj);
		$classObjectIdKey = lcfirst($audit->objectClass);
		$objectIdKey = $classObjectIdKey . 'Id';
		$objectLegacyIdKey = strtolower(preg_replace('/([A-Z]{1})/','_\1',$classObjectIdKey)) . '_id';
		//if (!isset($obj->$objectIdKey) && !isset($obj->$objectLegacyIdKey)) { // Problem with WebVista_Model_ORM::__get()
		if ($obj->$objectIdKey === null && $obj->$objectLegacyIdKey === null) {
			trigger_error("objIdKey not found: $objectIdKey for " . get_class($obj),E_USER_NOTICE);
			return false;
		}
		$audit->objectId = $obj->$objectIdKey;
		$audit->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$audit->type = $obj->_persistMode;
		$audit->dateTime = date('Y-m-d H:i:s');
		$this->addORM($audit);
		if ($obj instanceof ORM) {
			foreach ($obj->ORMFields() as $field) {
				$auditValue = new AuditValue();
				$auditValue->auditId = $audit->auditId;
				$auditValue->key = $field;
				if (is_object($obj->$field)) {
					$auditValue->value = get_class($obj->$field);
				}
				else {
					$auditValue->value = (string)$obj->$field;
				}
				$this->addORM($auditValue);
			}
		}
	}

	public function persist($preQueries=null,$postQueries=null) {
		$ret = true;
		$db = Zend_Registry::get('dbAdapter');
		$db->beginTransaction();
		try {
			if ($preQueries !== null) {
				if (!is_array($preQueries)) {
					$preQueries = array($preQueries);
				}
				foreach ($preQueries as $sql) {
					$db->query($sql);
				}
			}
			foreach ($this->queries as $sql) {
				//$db->query($sql);
				$stmt = $db->getConnection()->exec($sql);
			}
			if ($postQueries !== null) {
				if (!is_array($postQueries)) {
					$postQueries = array($postQueries);
				}
				foreach ($postQueries as $sql) {
					$db->query($sql);
				}
			}
			if (isset($this->auditLogQueries[0])) {
				AuditLog::appendSql($this->auditLogQueries);
			}
			$db->commit();
		}
		catch (Exception $e) {
			$ret = false;
			$db->rollBack();
			trigger_error($e->getMessage(),E_USER_NOTICE);
		}
		return $ret;
	}

}
