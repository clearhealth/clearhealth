<?php
/*****************************************************************************
*       GenericData.php
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


class GenericData extends WebVista_Model_ORM implements NSDRMethods {
	protected $genericDataId;
	protected $objectClass;
	protected $objectId;
	protected $dateTime;
	protected $name;
	protected $value;
	protected $revisionId;

	protected $_table = "genericData";
	protected $_primaryKeys = array("genericDataId");

	public function persist() {
		if ($this->_persistMode == WebVista_Model_ORM::DELETE) return parent::persist();
		$db = Zend_Registry::get('dbAdapter');
		$genericDataId = (int)$this->genericDataId;
		$data = $this->toArray();
		if ($genericDataId > 0) {
			$ret = $db->update($this->_table,$data,'genericDataId = '.$genericDataId);
		}
		else {
			$this->genericDataId = WebVista_Model_ORM::nextSequenceId();
			$data['genericDataId'] = $this->genericDataId;
			$ret = $db->insert($this->_table,$data);
		}
		if ($this->shouldAudit()) {
			WebVista_Model_ORM::audit($this);
		}
		return $this;
	}

	public function getIteratorByFilters($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->group('revisionId')
				->order('revisionId DESC')
				->order('dateTime DESC');
		foreach ($filters as $name=>$value) {
			$sqlSelect->where($name.' = ?',$value);
		}
		return $this->getIterator($sqlSelect);
	}

	public function doesRowExist($autoPopulate = false) {
		$ret = false;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('objectClass = ?',$this->objectClass)
				->where('objectId = ?',$this->objectId)
				->where('`name` = ?',$this->name)
				->order('revisionId DESC')
				->limit(1);
		if ($row = $db->fetchRow($sqlSelect)) {
			if ($autoPopulate) {
				$this->populateWithArray($row);
			}
			$ret = true;
		}
		return $ret;
	}

	public function loadValue() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('objectClass = ?',$this->objectClass)
				->where('objectId = ?',$this->objectId)
				->where('`name` = ?',$this->name)
				->where('revisionId = ?',(int)$this->revisionId);
		return $this->populateWithSql($sqlSelect->__toString());
	}

	public function deleteByRevisionId($revisionId = null) {
		if ($revisionId === null) {
			$revisionId = $this->revisionId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$db->delete($this->_table,'revisionId = '.(int)$revisionId);
	}

	public static function createRevision($objectClass,$objectId,$revisionId=0) {
		$db = Zend_Registry::get('dbAdapter');
		if (!$revisionId > 0) {
			$revisionId = self::getMostRecentRevisionId($objectClass,$objectId);
		}
		$gd = new self();
		$sqlSelect = $db->select()
				->from($gd->_table)
				->where('objectClass = ?',$objectClass)
				->where('objectId = ?',$objectId)
				->where('revisionId = ?',$revisionId);
		//trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		if ($rows = $db->fetchAll($sqlSelect)) {
			$newRevisionId = WebVista_Model_ORM::nextSequenceId();
			foreach ($rows as $row) {
				$gd = new self();
				$gd->populateWithArray($row);
				$gd->genericDataId = 0;
				$gd->revisionId = $newRevisionId;
				//trigger_error(print_r($gd->toArray(),true),E_USER_NOTICE);
				$gd->persist();
			}
		}
	}

	public static function getMostRecentRevisionId($objectClass,$objectId) {
		$db = Zend_Registry::get('dbAdapter');
		$gd = new self();
		$sqlSelect = $db->select()
				->from($gd->_table,array('revisionId'))
				->where('objectClass = ?',$objectClass)
				->where('objectId = ?',$objectId)
				->order('revisionId DESC')
				->limit(1);
		//trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		$revisionId = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$revisionId = $row['revisionId'];
		}
		//trigger_error('recentRevisionId:'.$revisionId,E_USER_NOTICE);
		return $revisionId;
	}

	public static function getObjectIdByRevisionId($revisionId) {
		$db = Zend_Registry::get('dbAdapter');
		$gd = new self();
		$sqlSelect = $db->select()
				->from($gd->_table,array('objectId'))
				->where('revisionId = ?',(int)$revisionId)
				->limit(1);
		//trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		$objectId = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$objectId = $row['objectId'];
		}
		//trigger_error('objectId:'.$objectId,E_USER_NOTICE);
		return $objectId;
	}

	public function nsdrPersist($tthis,$context,$data) {
		$context = (int)$context;
		$attributes = $tthis->_attributes;
		$nsdrNamespace = $tthis->_nsdrNamespace;
		$aliasedNamespace = $tthis->_aliasedNamespace;
		if ($context == '*') {
			if (isset($attributes['isDefaultContext']) && $attributes['isDefaultContext']) { // get genericData
				$objectClass = 'ClinicalNote';
				$clinicalNoteId = 0;
				if (isset($attributes['clinicalNoteId'])) {
					$clinicalNoteId = (int)$attributes['clinicalNoteId'];
				}
				$revisionId = 0;
				if (isset($attributes['revisionId'])) {
					$revisionId = (int)$attributes['revisionId'];
				}
				$gd = new self();
				$gd->objectClass = $objectClass;
				$gd->objectId = $clinicalNoteId;
				$gd->name = $nsdrNamespace;
				$gd->revisionId = $revisionId;
				$gd->loadValue();

				$gd->dateTime = date('Y-m-d H:i:s');
				if (is_array($data)) {
					$data = array_shift($data);
				}
				$gd->value = $data;
				return $gd->persist();
			}
			else { // all
				$ret = false;
				if (isset($data[0])) {
					$ret = true;
					foreach ($data as $row) {
						$gd = new self();
						$gd->populateWithArray($row);
						$gd->persist();
					}
				}
				return $ret;
			}
		}
		$gd = new self();
		$gd->genericDataId = $context;
		$gd->populate();
		$gd->populateWithArray($data);
		return $gd->persist();
	}

	public function nsdrPopulate($tthis,$context,$data) {
		$context = (int)$context;
		$attributes = $tthis->_attributes;
		$nsdrNamespace = $tthis->_nsdrNamespace;
		$aliasedNamespace = $tthis->_aliasedNamespace;
		if ($context == '*') {
			if (isset($attributes['isDefaultContext']) && $attributes['isDefaultContext']) { // get genericData
				$objectClass = 'ClinicalNote';
				$clinicalNoteId = 0;
				if (isset($attributes['clinicalNoteId'])) {
					$clinicalNoteId = (int)$attributes['clinicalNoteId'];
				}
				$revisionId = 0;
				if (isset($attributes['revisionId'])) {
					$revisionId = (int)$attributes['revisionId'];
				}
				$gd = new self();
				$gd->objectClass = $objectClass;
				$gd->objectId = $clinicalNoteId;
				$gd->name = $nsdrNamespace;
				$gd->revisionId = $revisionId;
				$gd->loadValue();
				return $gd->value;
			}
			else { // all
				$ret = array();
				$gd = new self();
				$gdIterator = $gd->getIterator();
				foreach ($gdIterator as $g) {
					$ret[] = $g->toArray();
				}
				return $ret;
			}
		}
		$gd = new self();
		$gd->genericDataId = $context;
		$gd->populate();
		return $gd->toArray();
	}

	public function nsdrMostRecent($tthis,$context,$data) {
	}

	public static function upgradeClinicalNote() {
		/* NOTE: take output file and
		# cat upnote.sql | sort -n | uniq > upnote2.sql
		For it to run quickly all the fields used in the join need to be indexed, otherwise 5 seconds per row give or take */
		set_time_limit(0);
		$db = Zend_Registry::get('dbAdapter');
		$genericData = new self();
		$sql = "
		SELECT genericData.name, clinicalNoteDefinitions.clinicalNoteTemplateId
			FROM `genericData`
			INNER JOIN clinicalNotes ON clinicalNotes.clinicalNoteId = genericData.objectId
			INNER JOIN clinicalNoteDefinitions on clinicalNoteDefinitions.clinicalNoteDefinitionId = clinicalNotes.clinicalNoteDefinitionId
			GROUP BY genericData.name, clinicalNoteDefinitions.clinicalNoteTemplateId";

		$res = $db->query($sql);

		while (($row = $res->fetch()) !==false) {
			$cn = new ClinicalNoteTemplate();
			$cn->clinicalNoteTemplateId = (int)$row['clinicalNoteTemplateId'];
			if (!$cn->populate()) {
				$error = 'Error populating clinical note '.$cn->clinicalNoteId;
				trigger_error($error);
				continue;
			}
			try {
				$xml = new SimpleXMLElement($cn->template);
				foreach ($xml as $question) {
					foreach($question as $key => $item) {
						if ($key != 'dataPoint') continue;
						$namespace = (string)$item->attributes()->namespace;
						$html = preg_replace('/[-\.]/','_',$namespace);
						$output=  'UPDATE '.$genericData->_table.' INNER JOIN clinicalNotes ON clinicalNotes.clinicalNoteId = genericData.objectId INNER JOIN clinicalNoteDefinitions on clinicalNoteDefinitions.clinicalNoteDefinitionId = clinicalNotes.clinicalNoteDefinitionId SET `name`='.$db->quote($namespace).' WHERE `name`='.$db->quote($html).' AND objectClass=\'ClinicalNote\' AND clinicalNoteDefinitions.clinicalNoteTemplateId='.$cn->clinicalNoteTemplateId . ";\n";
						file_put_contents('/tmp/upnote.sql',$output,FILE_APPEND);
						echo ".";
					}
				}
			}
			catch (Exception $e) {
				$error = 'Error parsing template for clinical note '.$cn->clinicalNoteId.' template '.$templateId;
				trigger_error($error);
				continue;
			}
		}
	}

}
