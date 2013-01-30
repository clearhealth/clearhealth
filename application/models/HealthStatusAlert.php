<?php
/*****************************************************************************
*       HealthStatusAlert.php
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


class HealthStatusAlert extends WebVista_Model_ORM {

	protected $healthStatusAlertId;
	protected $message;
	protected $status; // active, fulfilled, ignored, inactived
	protected $personId;
	protected $healthStatusHandlerId; // 0 means manually added; greater than 0 means added by process daemon
	protected $dateDue;
	protected $lastOccurence;
	protected $priority;
	protected $_table = 'healthStatusAlerts';
	protected $_primaryKeys = array('healthStatusAlertId');

	const ENUM_PARENT_NAME = 'HSA Preferences';

	protected function _getIteratorBy($id,$value) {
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where("$id = ?",$value);
		return $this->getIterator($dbSelect);
	}

	public function getIteratorByPatientId($patientId = null) {
		if ($patientId === null) {
			$patientId = $this->personId;
		}
		return $this->_getIteratorBy('personId',(int)$patientId);
	}

	public function getIteratorByStatus($status = null) {
		if ($status === null) {
			$status = $this->status;
		}
		return $this->_getIteratorBy('status',$status);
	}

	public function getIteratorByStatusWithPatientId($status = null,$patientId = null) {
		if ($status === null) {
			$status = $this->status;
		}
		if ($patientId === null) {
			$patientId = $this->personId;
		}
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('status = ?',$status)
			       ->where('personId = ?',(int)$patientId);
		return $this->getIterator($dbSelect);
	}

	public function getIteratorByHandlerPatientId($handlerId,$patientId) {
		$dbSelect = $this->_getSQLByHandlerPatientId($handlerId,$patientId);
		return $this->getIterator($dbSelect);
	}

	public function populateByHandlerPatientId($handlerId,$patientId) {
		$dbSelect = $this->_getSQLByHandlerPatientId($handlerId,$patientId);
		$retval = $this->populateWithSql($dbSelect->__toString());
		$this->postPopulate();
		return $retval;
	}

	protected function _getSQLByHandlerPatientId($handlerId,$patientId) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('healthStatusHandlerId = ?',(int)$handlerId)
			       ->where('personId = ?',(int)$patientId);
		return $dbSelect;
	}

}
