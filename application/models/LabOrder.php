<?php
/*****************************************************************************
*       LabOrder.php
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


class LabOrder extends WebVista_Model_ORM implements Document {
	protected $lab_order_id;
	protected $patient_id;
	protected $patient;
	protected $type;
	protected $status;
	protected $ordering_provider;
	protected $manual_service;
	protected $manual_order_date;
	protected $encounter_id;
	protected $external_id;
	protected $person_id;
	protected $orderDescription;
	protected $eSignatureId;
	protected $_table = "lab_order";
	protected $_primaryKeys = array("lab_order_id");
	protected $_legacyORMNaming = true;
	protected $_cascadePersist = false;

	public function __construct() {
		parent::__construct();
		$this->patient = new Patient();
		$this->patient->_cascadePersist = false;
	}

	public function getIteratorByPersonId($personId = null) {
		if ($personId === null) {
			$personId = $this->personId;
		}
		$personId = (int)$personId;
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
				->from($this->_table)
				->where('person_id = '.$personId.' OR patient_id = '.$personId);
		return parent::getIterator($dbSelect);
	}

	public function getSummary() {
                return $this->orderDescription;
	}

	public function getDocumentId() {
		return $this->labOrderId;
	}
	public function setDocumentId($id) {
		$this->labOrderId = (int)$id;
	}

	public function getContent() {
		return '';
	}

	public static function getPrettyName() {
		return 'Lab Results';
	}

	public static function getControllerName() {
		return 'LabResultsController';
	}
	
	public function setSigned($eSignatureId) {
		$this->eSignatureId = (int)$eSignatureId;
		$this->persist();
	}

	public function hasSigningEntry() {
		$ret = false;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from(array('lo'=>$this->_table))
				->join(array('esig'=>'eSignatures'),'esig.objectId = lo.lab_order_id')
				->where('esig.objectClass = ?',get_class($this))
				->where('lo.lab_order_id = ?',$this->labOrderId);
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		return $ret;
	}

	public function getPerson_id() {
		return $this->getPersonId();
	}

	public function getPersonId() {
		$personId = $this->person_id;
		if (!$personId > 0) $personId = $this->patient_id;
		return $personId;
	}

}
