<?php
/*****************************************************************************
*       MedicationRefillResponse.php
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


class MedicationRefillResponse extends WebVista_Model_ORM {

	protected $messageId;
	protected $medicationId;
	protected $medication;
	protected $response;
	protected $message;
	protected $dateTime;

	protected $_table = 'medicationRefillResponses';
	protected $_primaryKeys = array('messageId');
	protected $_cascadePersist = false;

	protected $_validResponses = array('approved'=>'Approved','approvedWithChanges'=>'Approved With Changes','denied'=>'Denied','deniedNewPrescriptionToFollow'=>'Denied New Prescription To Follow');

	function __construct() {
		parent::__construct();
		$this->medication = new Medication();
		$this->medication->_cascadePersist = false;
	}

	public function setMedicationId($id) {
		$this->medicationId = (int)$id;
		$this->medication->medicationId = $this->medicationId;
	}

	public function getIteratorByPersonId($personId) {
		$db = Zend_Registry::get("dbAdapter");
		$sqlSelect = $db->select()
				->from(array('r'=>$this->_table))
				->join(array('m'=>'medications'),'m.medicationId = r.medicationId')
				->where('m.personId = ?',(int)$personId)
				->order('r.dateTime');
		return $this->getIterator($sqlSelect);
	}

	public function send($response,$inputs,$messageId=null) {
		if ($messageId === null) {
			$messageId = $this->messageId;
		}
		if (!isset($this->_validResponses[$response])) {
			$response = 'approved';
		}

		$messaging = new Messaging();
		$messaging->messagingId = $messageId;
		if (!$messaging->populate()) {
			trigger_error(__('Refill request messaging does not exists.'),E_USER_NOTICE);
			return false;
		}
		$refillRequest = new MedicationRefillRequest();
		$refillRequest->messageId = $messageId;
		if (!$refillRequest->populate()) {
			trigger_error(__('Refill request does not exists.'),E_USER_NOTICE);
			return false;
		}

		$data = array();
		//$data['writtenDate'] = date('Ymd',strtotime($inputs['datePrescribed']))
		$data['writtenDate'] = date('Ymd'); // should be set to the date the prescriber authorized the renewal of the prescription
		$data['message'] = $messaging->rawMessage;
		if ($response == 'approved' && $refillRequest->medication->isScheduled()) {
			$response = 'denied';
			$inputs['note'] = 'This Refill Request is for a controlled substance (Schedule III - V). The approved controlled substance prescription is being faxed to your pharmacy';
			$inputs['refills'] = 0;
		}
		$arrResponse = array();
		switch ($response) {
			case 'approved':
				$this->response = 'Approved';
				$refills = $messaging->refills;
				$newRefills = (int)$inputs['refills'];
				if ($refills != $newRefills) {
					if ($refills != 0) {
						$this->response = 'ApprovedWithChanges';
					}
					//else {
						$data['refills'] = $newRefills;
					//}
				}
				$this->message = $inputs['note'];
				$arrResponse[$this->response] = array('Note'=>$this->message); 
				break;
			case 'denied': // quantity should be set to zero
				$data['refills'] = 0;
				if (isset($inputs['note'])) {
					$this->response = 'DeniedNewPrescriptionToFollow';
					$this->message = $inputs['note'];
					$arrResponse[$this->response] = array('Note'=>$this->message);
					//$data['medicationId'] = (int)$inputs['medicationId'];
				}
				else {
					$this->response = 'Denied';
					//$this->message = $inputs['reasonCode'].':'.$inputs['reason']; // empty reason
					$this->message = $inputs['reason'];
					$arrResponse[$this->response] = array('DenialReasonCode'=>$inputs['reasonCode'],'DenialReason'=>$inputs['reason']);
				}
				break;
		}
		$data['response'] = $arrResponse;
		$data['type'] = 'refill';
		$ret = ePrescribe::sendResponse($data,$messaging);
		if ($ret === true) {
			$this->dateTime = date('Y-m-d H:i:s');
			$this->persist();

			$refillRequest->action = $this->_validResponses[$response];
			$refillRequest->status = 'RESPONDED';
			$refillRequest->persist();
		}

		return $ret;
	}

	public function getRespondedBy() {
		$ret = '';
		if (!strlen($this->response) > 0) {
			return $ret;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from(array('r'=>$this->_table))
				->join(array('m'=>'messaging'),'r.messageId = m.objectId')
				->join(array('a'=>'audits'),'a.objectId = m.messagingId')
				->join(array('u'=>'user'),'u.user_id = a.userId')
				->join(array('p'=>'person'),'p.person_id = u.person_id')
				->where("a.objectClass = 'Messaging'")
				->where('r.messageId = ?',$this->messageId)
				->group('a.objectClass');
		if ($row = $db->fetchRow($sqlSelect)) {
			if (strlen($row['last_name']) > 0) {
				$ret = $row['last_name'].', '.$row['first_name'].' '.$row['middle_name'];
			}
		}
		//trigger_error($sqlSelect->__toString());
		return $ret;
	}

	public function getMedicationRefillResponseId() {
		return $this->messageId;
	}

}
