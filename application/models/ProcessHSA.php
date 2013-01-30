<?php
/*****************************************************************************
*       ProcessHSA.php
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


class ProcessHSA extends ProcessAbstract {

	protected $_handlers = array();
	protected $_currentDate = null;
	protected $_currentTime = null;
	protected $_timeTrigger = '12:00 AM';
	protected $_lastDateTimeTriggered = null;

	public function __construct($timeTrigger = null) {
		if ($timeTrigger === null) {
			try {
				$config = Zend_Registry::get('config');
				// check HSA's that occurs once per day at an off-hour time present in the app.ini file.
				if (isset($config->HSA) && isset($config->HSA->timeTrigger)) {
					$timeTrigger = $config->HSA->timeTrigger;
				}
			} catch (Exception $e) {
			}
		}
		if ($timeTrigger !== null) {
			$this->_timeTrigger = $timeTrigger;
		}
	}

	public function getCurrentDate() {
		if ($this->_currentDate === null) {
			return date('m/d/Y');
		}
		return $this->_currentDate;
	}

	public function setCurrentDate($currentDate) {
		$this->_currentDate = $currentDate;
	}

	public function getCurrentTime() {
		if ($this->_currentTime === null) {
			return date('h:i A');
		}
		return $this->_currentTime;
	}

	public function setCurrentTime($currentTime) {
		$this->_currentTime = $currentTime;
	}

	public function getTimeTrigger() {
		return $this->_timeTrigger;
	}

	public function setTimeTrigger($timeTrigger) {
		$this->_timeTrigger = $timeTrigger;
	}

	protected function _populateHandlers() {
		$this->_handlers = array();
		$handler = new HealthStatusHandler();
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($handler->_table)
			       ->where('active = 1');
		$handlerIterator = $handler->getIterator($dbSelect);
		foreach ($handlerIterator as $item) {
			$this->_evaluateCodes($item);
			$this->_handlers[] = $item;
		}
	}

	/**
	 * Process condition and do action
	 * @param Audit $audit Audit ORM
	 * @param Handler $handler Handler ORM
	 * @return boolean Return TRUE if successful, FALSE otherwise
	 */
	public function process(Audit $audit) {
		$this->_populateHandlers();

		$ret = true;
		foreach ($this->_handlers as $handler) {
			$result = $this->_doProcess($handler,$audit);
			if ($result === false) {
				// log processing errors
				$processingError = new ProcessingError();
				$processingError->_shouldAudit = false;
				$processingError->auditId = $audit->auditId;
				$processingError->handlerId = $handler->handlerId;
				$processingError->persist();
			}
			$ret &= $result;
		}

		return $ret;
	}

	protected function _doProcess(HealthStatusHandler $handler,Audit $audit) {
		$handlerName = Handler::normalizeHandlerName($handler->name);
		$classHandlerObject = $handlerName.'HealthStatusHandlerObject';
		if (!parent::isParentOf($classHandlerObject,'HealthStatusHandlerObjectAbstract')) {
			return false;
		}
		$retMatchAudit = call_user_func_array(array($classHandlerObject,'matchAudit'),array($handler,$audit));
		if ($retMatchAudit === true) {
			$objectClass = $audit->objectClass;
			$obj = new $objectClass();
			foreach ($obj->_primaryKeys as $key) {
				$obj->$key = $audit->objectId;
			}
			$obj->populate();
			$patientId = $obj->personId;
			$retFulfill = call_user_func_array(array($classHandlerObject,'fulfill'),array($handler,$patientId));
		}
		return true;
	}

	public function extraProcess() {
		// invoke additional daily process
		$timeTrigger = $this->_timeTrigger;
		$currentDate = $this->getCurrentDate();
		$currentTime = $this->getCurrentTime();
		if ($this->_lastDateTimeTriggered === null) {
			$this->_lastDateTimeTriggered = date('m/d/Y').' '.$timeTrigger;
		}
		if (strtotime($currentDate.' '.$currentTime) >= strtotime($this->_lastDateTimeTriggered)) {
			$this->_lastDateTimeTriggered = $currentDate.' '.$currentTime;
			return $this->_doDailyProcess();
		}
		return true;
	}

	protected function _doDailyProcess() {
		try {
			$cacheCodeObjects = Zend_Registry::get('cacheCodeObjects');
		} catch (Exception $e) {
			$cacheCodeObjects = array();
		}
		$handlerPatient = new HealthStatusHandlerPatient();
		$handlerPatientIterator = $handlerPatient->getIterator();
		foreach ($handlerPatientIterator as $row) {
			$handler = $row->healthStatusHandler;
			$patient = $row->person;
			$patientId = $patient->personId;

			$handlerObject = $handler->handlerObject;
			if (!strlen($handlerObject) > 0) {
				$handlerObject = $handler->generateDefaultHandlerObject();
			}
			$md5 = md5($handlerObject);
			if (!in_array($md5,$cacheCodeObjects)) {
				$cacheCodeObjects[] = $md5;
				eval($handlerObject); // TODO: needs to be validated
			}

			$datasource = $handler->datasource;
			if (!strlen($datasource) > 0) {
				$datasource = $handler->generateDefaultDatasource();
			}
			$md5 = md5($datasource);
			if (!in_array($md5,$cacheCodeObjects)) {
				$cacheCodeObjects[] = $md5;
				eval($datasource); // TODO: needs to be validated
			}

			$handlerName = Handler::normalizeHandlerName($handler->name);
			$classHandlerObject = $handlerName.'HealthStatusHandlerObject';
			if (!parent::isParentOf($classHandlerObject,'HealthStatusHandlerObjectAbstract')) {
				trigger_error($classHandlerObject.' is not an instance of HealthStatusHandlerObjectAbstract',E_USER_NOTICE);
				continue;
			}
			$retPatientMatch = call_user_func_array(array($classHandlerObject,'patientMatch'),array($handler,$patientId));
			if ($retPatientMatch !== false) {
				$classHealthStatusDatasource = $handlerName.'HealthStatusDatasource';
				if (!parent::isParentOf($classHealthStatusDatasource,'HealthStatusDatasourceAbstract')) {
					trigger_error($classHealthStatusDatasource.' is not an instance of HealthStatusDatasourceAbstract',E_USER_NOTICE);
					continue;
				}
				try {
					$retSourcedata = call_user_func_array(array($classHealthStatusDatasource,'sourceData'),array($patientId,$retPatientMatch));
				}
				catch (Exception $e) {
					trigger_error('Exception error ('.$e->getCode().'): '.$e->getMessage(),E_USER_NOTICE);
					continue;
				}
				if (!strlen($handler->template) > 0) {
					$handler->template = $handler->generateDefaultTemplate();
				}
				$message = TemplateXSLT::render($retSourcedata,$handler->template);

				$healthStatusAlert = new HealthStatusAlert();
				$healthStatusAlert->message = $message;
				$healthStatusAlert->status = 'active';
				$healthStatusAlert->personId = $patientId;
				$healthStatusAlert->healthStatusHandlerId = $handler->healthStatusHandlerId;
				$healthStatusAlert->dateDue = date('Y-m-d H:i:s',strtotime($handler->timeframe));
				$healthStatusAlert->persist();
			}
			else {
				$retFulfill = call_user_func_array(array($classHandlerObject,'fulfill'),array($handler,$patientId));
			}
		}
		Zend_Registry::set('cacheCodeObjects',$cacheCodeObjects);
	}

}
