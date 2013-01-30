<?php
/*****************************************************************************
*       HealthStatusHandlerPatient.php
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


class HealthStatusHandlerPatient extends WebVista_Model_ORM {

	protected $healthStatusHandlerId;
	protected $healthStatusHandler;
	protected $personId;
	protected $person;

	protected $_table = 'healthStatusHandlerPatients';
	protected $_primaryKeys = array('healthStatusHandlerId','personId');

	protected $_cascadePersist = false;

	public function __construct() {
		parent::__construct();
		$this->healthStatusHandler = new HealthStatusHandler();
		$this->healthStatusHandler->_cascadePersist = $this->_cascadePersist;
		$this->person = new Patient();
		$this->person->_cascadePersist = $this->_cascadePersist;
	}

	public function persist() {
		if ($this->_persistMode == WebVista_Model_ORM::DELETE) {
			$db = Zend_Registry::get("dbAdapter");
			$db->delete($this->_table,'healthStatusHandlerId='.$this->healthStatusHandlerId.' AND personId='.$this->personId);
			return;
		}
		parent::persist();
	}

	public function getIterator($dbSelect = null) {
		if ($dbSelect === null) {
			return $this->_getIteratorBy(1,1);
		}
		return parent::getIterator($dbSelect);
	}

	protected function _getIteratorBy($id,$value) {
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()
			       ->from(array('hp'=>$this->_table))
			       ->join(array('h'=>'healthStatusHandlers'),'h.healthStatusHandlerId = hp.healthStatusHandlerId')
			       ->join(array('p'=>'person'),'p.person_id = hp.personId')
			       ->where("$id = ?",$value);
		return $this->getIterator($dbSelect);
	}

	public function getIteratorByHandlerId($healthStatusHandlerId = null) {
		if ($healthStatusHandlerId === null) {
			$healthStatusHandlerId = $this->healthStatusHandlerId;
		}
		return $this->_getIteratorBy('hp.healthStatusHandlerId',(int)$healthStatusHandlerId);
	}

	public function getIteratorByPatientId($patientId = null) {
		if ($patientId === null) {
			$patientId = $this->personId;
		}
		return $this->_getIteratorBy('hp.personId',(int)$patientId);
	}

	public static function isPatientSubscribed($healthStatusHandlerId,$patientId) {
		$ok = false;
		$healthStatusHandler = new self();
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()
			       ->from($healthStatusHandler->_table)
			       ->where('healthStatusHandlerId = ?',(int)$healthStatusHandlerId)
			       ->where('personId = ?',(int)$patientId);
		$rows = $db->fetchAll($dbSelect);
		if (count($rows) > 0) {
			$ok = true;
		}
		return $ok;
	}

	public function getHealthStatusHandlerPatientId() {
		return $this->healthStatusHandlerId;
	}

}
