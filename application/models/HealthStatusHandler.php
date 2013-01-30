<?php
/*****************************************************************************
*       HealthStatusHandler.php
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


class HealthStatusHandler extends WebVista_Model_ORM {

	protected $healthStatusHandlerId;
	protected $guid;
	protected $name;
	protected $condition;
	protected $handlerObject;
	protected $active;
	protected $timeframe;
	protected $datasource;
	protected $template;

	protected $_table = 'healthStatusHandlers';
	protected $_primaryKeys = array('healthStatusHandlerId');

	/**
	 * Generate Health Status Alerts default object for condition
	 * @return string PHP code for handlerObject
	 */
	public function generateDefaultHandlerObject() {
		$audit = new Audit();
		$audit->auditId = $this->condition;
		$audit->populate();

		$handlerName = Handler::normalizeHandlerName($this->name);

		$healthStatusHandler = '';
		$objectClass = $audit->objectClass;
		if (strlen($objectClass) > 0 && class_exists($objectClass)) {
			$tmp = new $objectClass();

			$healthStatusHandler .= <<<EOL
		if (\$auditOrm->objectClass == '{$objectClass}' && \$auditOrm->type == '{$audit->type}') {
			return true;
		}
EOL;
		}

		$handlerObject = <<<EOL

class {$handlerName}HealthStatusHandlerObject extends HealthStatusHandlerObjectAbstract {
	//abstract requires at least this method
	public static function matchAudit(HealthStatusHandler \$handler,Audit \$auditOrm) {
{$healthStatusHandler}
		return false;
	}

	public static function fulfill(HealthStatusHandler \$handler,\$patientId) {
	}

	public static function patientMatch(HealthStatusHandler \$handler,\$patientId) {
	}
}

EOL;
		return $handlerObject;
	}

	public function generateDefaultDatasource() {
		$handlerName = Handler::normalizeHandlerName($this->name);

		$datasource = <<<EOL

class {$handlerName}HealthStatusDatasource extends HealthStatusDatasourceAbstract {
	//abstract requires at least this method
	public static function sourceData(\$patientId,\$data) {
		\$patient = new Patient();
		\$patient->personId = \$patientId;
		\$patient->populate();
		// test if patientId has record
		if (!strlen(\$patient->recordNumber) > 0) {
			\$exceptionCode = \$patientId; // temporarily use patientId as exception code
			throw new Exception(__('Patient does not exists'),\$exceptionCode);
		}
		\$patientArray = \$patient->toArray();
		if (is_object(\$data) && method_exists(\$data,'toArray')) {
			return array(0 => array_merge(\$patient->toArray(),\$data->toArray()));
		}
		elseif (is_array(\$data)) {
			return array(0 => array_merge(\$patient->toArray(),\$data->toArray()));
		}
		return array(0 => \$patient->toArray());
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
		return self::generateTestTetanus();
	}

	public static function generateTestTetanus() {
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

		$medication = new Medication();
		$medication->_shouldAudit = false; // do not audit
		$medication->hipaaNDC = 'hipaaNDC';
		$medication->personId = $patient->person_id;
		$medication->persist();
		$objects['medication'] = $medication;

		$audit = new Audit();
		$audit->_ormPersist = true;
		$audit->objectClass = get_class($medication);
		$audit->objectId = $medication->medicationId;
		$audit->dateTime = date('Y-m-d H:i:s');
		$audit->type = WebVista_Model_ORM::REPLACE;
		$audit->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$audit->persist();
		$objects['audit'] = $audit;

		$handler = new HealthStatusHandler();
		$handler->name = 'Tetanus Shots Handler '.NSDR::create_guid();
		$handler->active = 1;
		$handler->timeframe = '+1 month';
		//$handler->condition = $audit->auditId;

		$handlerName = Handler::normalizeHandlerName($handler->name);
		$handler->handlerObject = <<<EOL

class {$handlerName}HealthStatusHandlerObject extends HealthStatusHandlerObjectAbstract {
	//abstract requires at least this method
	public static function matchAudit(HealthStatusHandler \$handler,Audit \$auditOrm) {
		// check if the patientId of the item referenced by the audit is subscribed to the handler, if not return false (no match)
		\$objectClass = \$auditOrm->objectClass;
		\$obj = new \$objectClass();
		foreach (\$obj->_primaryKeys as \$key) {
			\$obj->\$key = \$auditOrm->objectId;
		}
		\$obj->populate();
		\$patientId = \$obj->personId;
		if (!HealthStatusHandlerPatient::isPatientSubscribed(\$handler->healthStatusHandlerId,\$patientId)) {
			return false;
		}
		if (\$auditOrm->objectClass == '{$audit->objectClass}' && \$auditOrm->type == '{$audit->type}') {
			return true;
		}
		return false;
	}

	public static function fulfill(HealthStatusHandler \$handler,\$patientId) {
		// fulfill sees if current patient has any open alerts linked to this handler
		\$alert = new HealthStatusAlert();
		\$alert->populateByHandlerPatientId(\$handler->healthStatusHandlerId,\$patientId);
		// if there are open alerts then calls patientMatch again
		if (strlen(\$alert->status) > 0) {
			// if patientMatch returns FALSE then marks alerts as fulfilled if patientMatch return non-false alerts stay as is
			// sees if any alerts exist for the patient that are for this handler and marks then as fulfilled if the same condition in patientMatch is reversed
			if (self::patientMatch(\$handler,\$patientId) === false) {
				\$alert->status = 'fulfilled';
				\$alert->persist();
			}
		}
	}

	public static function patientMatch(HealthStatusHandler \$handler,\$patientId) {
 		// check if the patient does not have any record of a tetanus immunization (preferably by using NSDR)
		// if it has, add the timeframe to the date of that immunization and check if that date is greater than today, if so then return true
		// \$immunization = NSDR::populate(\$patientId.'::com.clearhealth.immunization');
		// temporarily superseded NSDR
		\$alert = new HealthStatusAlert();
		\$alert->populateByHandlerPatientId(\$handler->healthStatusHandlerId,\$patientId);
		if (!strlen(\$alert->status) > 0) {
			// no existing alert, return true
			return true;
		}
		// would test to see if the date of a given patients last tetanus shot plus the timeframe is less than today
		// if (strtotime(\$handler->timeframe,strtotime(\$alert->dateTime)) < strtotime(date('m/d/Y h:i A',strtotime('+1 month')))) {
		if (\$alert->status == 'active') {
			if (strtotime(\$alert->dateDue) < strtotime(date('m/d/Y h:i A',strtotime('+5 weeks')))) {
				//self::fulfill(\$handler,\$patientId);
				return false;
			}
			// patientMatch checks if patient 1234 has NOT had a tetanus when date of last tetanus + timeframe < today and generates an alert
			return true;
		}
		/* \$alert->lastOccurence
		if (\$alert->status == 'active' || \$alert->status == 'fulfilled' || \$alert->status == 'ignored') {
			// would not match if patient already has an active, fulfilled or ignored alert
			return false;
		}
		*/
		return true;
	}
}

EOL;
		$handler->datasource = $handler->generateDefaultDatasource();
		$handler->template = $handler->generateDefaultTemplate();
		$handler->persist();
		$objects['healthStatusHandler'] = $handler;

		// subscribe patient to handler
		$handlerPatient = new HealthStatusHandlerPatient();
		$handlerPatient->healthStatusHandlerId = $handler->healthStatusHandlerId;
		$handlerPatient->personId = $patient->personId;
		$handlerPatient->persist();
		$objects['healthStatusHandlerPatient'] = $handler;

		return $objects;
	}

}
