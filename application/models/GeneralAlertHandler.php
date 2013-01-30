<?php
/*****************************************************************************
*       GeneralAlertHandler.php
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


class GeneralAlertHandler extends WebVista_Model_ORM {

	protected $generalAlertHandlerId;
	protected $guid;
	protected $name;
	protected $condition;
	protected $handlerObject;
	protected $active;
	protected $datasource;
	protected $template;

	protected $_table = 'generalAlertHandlers';
	protected $_primaryKeys = array('generalAlertHandlerId');

	/**
	 * Generate General Alerts default object for condition
	 * @return string PHP code for handlerObject
	 */
	public function generateDefaultHandlerObject() {
		$audit = new Audit();
		$audit->auditId = $this->condition;
		$audit->populate();

		$handlerName = Handler::normalizeHandlerName($this->name);

		$generalAlertHandler = '';
		$objectClass = $audit->objectClass;
		if (strlen($objectClass) > 0 && class_exists($objectClass)) {
			$tmp = new $objectClass();

			$generalAlertHandler .= <<<EOL
		if (\$auditOrm->objectClass == '{$objectClass}' && \$auditOrm->type == '{$audit->type}') {
			return true;
		}
EOL;

		}

		$handlerObject = <<<EOL

class {$handlerName}GeneralAlertHandlerObject extends GeneralAlertHandlerObjectAbstract {
	//abstract requires at least this method
	public static function matchAudit(Audit \$auditOrm) {
{$generalAlertHandler}
		return false;
	}

}

EOL;
		return $handlerObject;
	}

	public function generateDefaultDatasource() {
		$handlerName = Handler::normalizeHandlerName($this->name);

		$datasource = <<<EOL

class {$handlerName}GeneralAlertDatasource extends GeneralAlertDatasourceAbstract {
	//abstract requires at least this method
	public static function sourceData(Audit \$audit) {
		\$objectClass = \$audit->objectClass;
		\$obj = new \$objectClass();
		foreach (\$obj->_primaryKeys as \$key) {
			\$obj->\$key = \$audit->objectId;
		}
		\$obj->populate();
		return array(0 => \$obj->toArray());
	}
}

EOL;
		return $datasource;
	}

	public function generateDefaultTemplate() {
		$template = <<<EOL
<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
</xsl:stylesheet>
EOL;
		return $template;
	}

	public static function generateTestData() {
		$clinicalNoteObjects = self::generateClinicalNoteHandler();
		$loggedOutObjects = self::generateUserLoggedOut();
		return array_merge($clinicalNoteObjects,$loggedOutObjects);
	}

	protected static function _generatePatient() {
		$objects = array();

		$person = new Person();
		$person->last_name = 'ClearHealth';
		$person->first_name = 'Test';
		$person->middle_name = 'I';
		$person->active = 1;
		$person->persist();
		$objects['person'] = $person;

		$patient = new Patient();
		$patient->person->_cascadePersist = false; // to avoid persist() calls on person
		$patient->person_id = $person->person_id;
		$patient->recordNumber = 1000;
		$patient->persist();
		$objects['patient'] = $patient;
		return $objects;
	}

	public static function generateClinicalNoteHandler() {
		$objects = self::_generatePatient();

		$audit = new Audit();
		$audit->_ormPersist = true;
		$audit->objectClass = 'ClinicalNote';
		$audit->objectId = $objects['patient']->person_id;
		$audit->dateTime = date('Y-m-d H:i:s');
		$audit->type = WebVista_Model_ORM::REPLACE;
		$audit->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$audit->persist();
		$objects['audit'] = $audit;

		$handler = new GeneralAlertHandler();
		$handler->name = 'Clinical Notes Handler '.NSDR::create_guid();
		$handler->active = 1;
		$handler->condition = $audit->auditId;
		$handler->handlerObject = $handler->generateDefaultHandlerObject();

		$handlerName = Handler::normalizeHandlerName($handler->name);
		$handler->datasource = <<<EOL

class {$handlerName}GeneralAlertDatasource extends GeneralAlertDatasourceAbstract {
	//abstract requires at least this method
	public static function sourceData(Audit \$audit) {
		\$eSignIterator = new ESignatureIterator();
		\$eSignIterator->setFilter(\$audit->userId,'signList');
		\$ret = array();
		foreach (\$eSignIterator as \$eSign) {
			\$objectClass = \$eSign->objectClass;
			\$obj = new \$objectClass();
			foreach (\$obj->_primaryKeys as \$key) {
				\$obj->\$key = \$eSign->objectId;
			}
			\$obj->populate();
			\$personId = \$obj->personId;
			\$patient = new Patient();
			\$patient->personId = \$personId;
			\$patient->populate();
			\$teamId = \$patient->teamId;

			\$row = array();
			\$row['teamId'] = \$teamId;
			\$row['signingUserId'] = \$eSign->signingUserId;
			\$row['objectId'] = \$eSign->objectId;
			\$row['objectClass'] = \$eSign->objectClass;
			\$ret[] = \$row;
		}
		return \$ret;
	}
}

EOL;
		$handler->template = $handler->generateDefaultTemplate();
		$handler->persist();
		$objects['generalAlertHandler'] = $handler;

		return $objects;
	}

	public static function generateUserLoggedOut() {
		$objects = self::_generatePatient();

		$handler = new GeneralAlertHandler();
		$handler->name = 'Logout Handler '.NSDR::create_guid();
		$handler->active = 1;
		$handler->condition = 0;
		$handlerName = Handler::normalizeHandlerName($handler->name);

		$handler->handlerObject = <<<EOL

class {$handlerName}GeneralAlertHandlerObject extends GeneralAlertHandlerObjectAbstract {
	//abstract requires at least this method
	public static function matchAudit(Audit \$auditOrm) {
		if (\$auditOrm->objectClass == 'Logout' && \$auditOrm->type == '1') {
			return true;
		}
		return false;
	}

}

EOL;
		$handler->datasource = <<<EOL

class {$handlerName}GeneralAlertDatasource extends GeneralAlertDatasourceAbstract {
	//abstract requires at least this method
	public static function sourceData(Audit \$audit) {
		\$eSignIterator = new ESignatureIterator();
		\$eSignIterator->setFilter(\$audit->userId,'signList');
		\$ret = array();
		foreach (\$eSignIterator as \$eSign) {
			\$objectClass = \$eSign->objectClass;
			\$obj = new \$objectClass();
			foreach (\$obj->_primaryKeys as \$key) {
				\$obj->\$key = \$eSign->objectId;
			}
			\$obj->populate();
			\$personId = \$obj->personId;
			\$patient = new Patient();
			\$patient->personId = \$personId;
			\$patient->populate();
			\$teamId = \$patient->teamId;

			\$row = array();
			\$row['teamId'] = \$teamId;
			\$row['signingUserId'] = \$eSign->signingUserId;
			\$row['objectId'] = \$eSign->objectId;
			\$row['objectClass'] = \$eSign->objectClass;
			\$ret[] = \$row;
		}
		return \$ret;
	}
}

EOL;
		$handler->template = $handler->generateDefaultTemplate();
		$handler->persist();
		$objects['generalAlertHandler'] = $handler;

		return $objects;
	}

}
