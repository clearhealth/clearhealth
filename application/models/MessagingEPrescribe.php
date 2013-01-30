<?php
/*****************************************************************************
*       MessagingEPrescribe.php
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


class MessagingEPrescribe extends WebVista_Model_ORM {

	protected $messagingId;
	protected $auditId;
	protected $audit;
	protected $pharmacyId;
	protected $pharmacy;
	protected $resend;
	protected $retries;
	//protected $messageType; // NewRx, RefillRequest, RefillResponse, Status
	protected $refills; // format: quantity qualifier
	protected $medicationId;
	protected $medication;

	protected $_table = 'messagingEPrescribes';
	protected $_primaryKeys = array('messagingId');
	protected $_cascadePersist = false;
	protected static $_messageTypes = array(
		'newRx'=>'NewRx',
		'refillRequest'=>'RefillRequest',
		'refillResponse'=>'RefillResponse',
		'status'=>'Status',
		'directoryDownload'=>'DirectoryDownload',
		'addPrescriber'=>'AddPrescriber',
	);

	public function __construct() {
		parent::__construct();
		$this->audit = new Audit();
		$this->audit->_cascadePersist = false;
		$this->pharmacy = new Pharmacy();
		$this->pharmacy->_cascadePersist = false;
		$this->medication = new Medication();
		$this->medication->_cascadePersist = false;
	}

	public function setMessageType($value) {
		if (!in_array($value,self::$_messageTypes)) {
			$value = 'NewRx';
		}
		$this->messageType = $value;
	}

	public function setAuditId($value) {
		$this->auditId = (int)$value;
		$this->audit->auditId = $this->auditId;
	}

	public function setPharmacyId($value) {
		$this->pharmacyId = $value;
		$this->pharmacy->pharmacyId = $this->pharmacyId;
	}

	public function setMedicationId($value) {
		$this->medicationId = (int)$value;
		$this->medication->medicationId = $this->medicationId;
	}

	public function getMessagingEPrescribeId() {
		return $this->messagingId;
	}

}
