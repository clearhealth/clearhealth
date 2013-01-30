<?php
/*****************************************************************************
*       MedicationRefillRequest.php
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


class MedicationRefillRequest extends WebVista_Model_ORM {

	protected $messageId;
	protected $medicationId;
	protected $medication;
	protected $action;
	protected $status;
	protected $dateStart;
	protected $details;
	protected $dateTime;
	protected $refillResponse;

	protected $_table = 'medicationRefillRequests';
	protected $_primaryKeys = array('messageId');
	protected $_cascadePersist = false;

	function __construct() {
		parent::__construct();
		$this->medication = new Medication();
		$this->medication->_cascadePersist = false;
		$this->refillResponse = new MedicationRefillResponse();
		$this->refillResponse->_cascadePersist = false;
	}

	public function populate() {
		$sql = "SELECT * from " . $this->_table . " WHERE 1 ";
		$doPopulate = false;
		foreach($this->_primaryKeys as $key) {
			$doPopulate = true;
			$sql .= " and $key = '" . preg_replace('/[^0-9a-z_A-Z-\.]/','',$this->$key) . "'";
		}
		if ($doPopulate == false) return false;
		$retval = false;
		$retval = $this->populateWithSql($sql);
		$this->postPopulate();
		return $retval;
	}

	public function getMedicationRefillRequestId() {
		return $this->messageId;
	}

	public function setMedicationRefillRequestId($id) {
		$this->setMessageId($id);
	}

	public function setMessageId($id) {
		$this->messageId = $id;
		$this->refillResponse->messageId = $this->messageId;
	}

	public function setMedicationId($id) {
		$this->medicationId = (int)$id;
		$this->medication->medicationId = $this->medicationId;
	}

	public function getIteratorByPersonId($personId) {
		$db = Zend_Registry::get("dbAdapter");
		$sqlSelect = $db->select()
				->from(array('r'=>$this->_table))
				->joinLeft(array('m'=>'medications'),'m.medicationId = r.medicationId')
				->joinLeft(array('msg'=>'messaging'),'msg.messagingId = r.messageId',array('personId','rawMessage'))
				->where('msg.personId = ?',(int)$personId)
				->order('r.dateTime DESC')
				->group('r.messageId');
		//trigger_error($sqlSelect->__toString());
		return $this->getIterator($sqlSelect);
	}

	public function persistAlert($providerId,$personId,$eachTeam) {
		$providerId = (int)$providerId;
		$personId = (int)$personId;
		if (!$providerId > 0) $providerId = (int)$this->medication->prescriberPersonId;
		if (!$personId > 0) $personId = (int)$this->medication->personId;

		$teamId = '';
		if ($personId > 0) {
			$patient = new Patient();
			$patient->personId = $personId;
			$patient->populate();
			$teamId = (string)$patient->teamId;
		}
		if (!strlen($teamId) > 0) $teamId = (string)$this->medication->patient->teamId;
		$objectClass = get_class($this);
		$alert = new GeneralAlert();
		$filters = array();
		$filters['objectClass'] = $objectClass;
		$filters['userId'] = (int)$providerId;
		if ($eachTeam) $filters['teamId'] = $teamId;
		$alert->populateOpenedAlertByFilters($filters);
		$messages = array();
		if (strlen($alert->message) > 0) { // existing general alert
			$messages[] = $alert->message;
		}
		else { // new general alert
			$alert->urgency = 'High';
			$alert->status = 'new';
			$alert->dateTime = date('Y-m-d H:i:s');
			$alert->objectClass = $objectClass;
			$alert->objectId = $this->messageId;
			$alert->userId = (int)$providerId;
			if ($eachTeam) $alert->teamId = $teamId;
		}
		$messages[] = 'Refill request pending. '.$this->details;
		$alert->message = implode("\n",$messages);
		$alert->persist();
	}

	public static function refillRequestDatasourceHandler(Audit $auditOrm,$eachTeam=true) {
		$ret = array();
		if ($auditOrm->objectClass != 'MedicationRefillRequest') {
			WebVista::debug('Audit:objectClass is not MedicationRefillRequest');
			return $ret;
		}

		$orm = new self();
		$orm->messageId = $auditOrm->objectId;
		if (!$orm->populate()) {
			WebVista::debug('Failed to populate');
			return $ret;
		}
		$objectClass = get_class($orm);
		$messaging = new Messaging();
		$messaging->messagingId = $orm->messageId;
		$messaging->populate();
		$medicationId = (int)$orm->medicationId;
		$providerId = (int)$messaging->providerId;
		$personId = (int)$messaging->personId;
		//if (!$personId > 0 || !$medicationId > 0) {
		if (!$personId > 0) {
			WebVista::debug('Refill request needs manual matching');
			return $ret;
		}
		$patient = new Patient();
		$patient->personId = $personId;
		$patient->populate();
		$teamId = (string)$patient->teamId;

		$alert = new GeneralAlert();
		$alertTable = $alert->_table;
		$msgTable = $messaging->_table;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($msgTable,null)
				->join($alertTable,$alertTable.'.objectId = '.$msgTable.'.messagingId')
				->where($msgTable.'.objectType = ?',Messaging::TYPE_EPRESCRIBE)
				->where($msgTable.'.messageType = ?','RefillRequest')
				->where("{$alertTable}.status = 'new'")
				->where($alertTable.'.objectClass = ?',$objectClass)
				->where($alertTable.'.userId = ?',$providerId)
				->where($msgTable.'.personId = ?',$personId)
				->limit(1);
		if ($eachTeam) $sqlSelect->where($alertTable.'.teamId = ?',$teamId);
		$alert->populateWithSql($sqlSelect->__toString());

		$messages = array();
		if ($alert->generalAlertId > 0) { // existing general alert
			$messages[] = $alert->message;
		}
		else { // new general alert
			$alert->urgency = 'High';
			$alert->status = 'new';
			$alert->dateTime = date('Y-m-d H:i:s');
			$alert->objectClass = $objectClass;
			$alert->objectId = $auditOrm->objectId;
			$alert->userId = (int)$providerId;
			if ($eachTeam) $alert->teamId = $teamId;
		}
		$messages[] = 'Refill request pending. '.$orm->details;
		$alert->message = implode("\n",$messages);
		return $alert->toArray();
	}

	public static function refillRequestActionHandler(Audit $auditOrm,array $dataSourceData) {
		if (!count($dataSourceData) > 0) {
			WebVista::debug('Received an empty datasource');
			return false;
		}
		$orm = new GeneralAlert();
		$orm->populateWithArray($dataSourceData);
		$orm->persist();
		return true;

	}

	public static function getControllerName() {
		return 'MedicationsController';
	}

	public function getPersonId() {
		$personId = (int)$this->medication->personId;
		if (!$personId > 0) {
			$messaging = new Messaging();
			$messaging->messagingId = $this->messageId;
			$messaging->populate();
			$personId = (int)$messaging->personId;
		}
		return $personId;
	}

	public function getUnrespondedRefills($personId) {
		$db = Zend_Registry::get("dbAdapter");
		$sqlSelect = $db->select()
				->from(array('r'=>$this->_table))
				->join(array('m'=>'messaging'),'m.messagingId = r.messageId',null)
				->where('r.status = ?','')
				->where('m.personId = ?',(int)$personId)
				->order('r.dateTime DESC');
		//trigger_error($sqlSelect->__toString());
		return $this->getIterator($sqlSelect);
	}

}
