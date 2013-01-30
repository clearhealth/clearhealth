<?php
/*****************************************************************************
*       MessagingController.php
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


class MessagingController extends WebVista_Controller_Action {

	protected $_session;

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
	}

	public function indexAction() {
		$this->render('index');
	}

	public function toolbarXmlAction() {
		header('Content-Type: text/xml');
		$this->render('toolbar-xml');
	}

	public function settingsAction() {
		$this->view->clinicalNoteDefinitions = new ClinicalNoteDefinitionIterator();
		$this->view->autoAttach = Messaging::settingsGetAutoAttach();
		$this->view->defaultClinicalNote = Messaging::settingsGetDefaultClinicalNote();
		$this->render('settings');
	}

	public function processSettingsAction() {
		$params = $this->_getParam('messaging');
		Messaging::settingsSetAutoAttach($params['autoAttach']);
		Messaging::settingsSetDefaultClinicalNote($params['defaultClinicalNote']);
		$ret = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function setFiltersAction() {
		$params = $this->_getParam('filters');
		$filters = $this->_session->filters;
		if (!$filters) {
			$filters = array();
		}
		$filterList = explode(';',$params);
		foreach ($filterList as $filter) {
			$x = explode(':',$filter);
			//if (!isset($x[1])) continue;
			$filters[$x[0]] = $x[1];
		}
		$this->_session->filters = $filters;
		$ret = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	protected function _initDefaultFilters() {
		$filters = array();
		$filters['dateStatus'] = date('Y-m-d 00:00:00',strtotime('-7 days')).','.date('Y-m-d 23:59:59');
		$statusOptions = array();
		$messagingIterator = new MessagingIterator(null,false);
		$messagingIterator->setFilters(array('optionGroup'=>'status'));
		foreach ($messagingIterator as $message) {
			$statusOptions[] = $message->status;
		}
		$filters['status'] = implode(',',$statusOptions);
		$messageOptions = array('EPrescribes','InboundFaxes','OutboundFaxes');
		$filters['message'] = implode(',',$messageOptions);
		$filters['resolution'] = 0;
		$this->_session->filters = $filters;
		return $filters;
	}

	public function listAction() {
		$filters = $this->_session->filters;
		if (!$filters) {
			$filters = $this->_initDefaultFilters();
		}
		$messagingIterator = new MessagingIterator(null,false);
		$messagingIterator->setFilters($filters);
		$rows = array();
		foreach ($messagingIterator as $item) {
			$tmp = array();
			$tmp['id'] = $item->messagingId;
			$tmp['data'][] = $item->dateStatus;
			$tmp['data'][] = $item->status;
			$tmp['data'][] = $item->displayType;
			$tmp['data'][] = nl2br($item->note);
			$tmp['data'][] = $item->objectType;
			$tmp['data'][] = $item->messageType;
			$tmp['userdata']['unresolved'] = $item->unresolved;
			$rows[] = $tmp;
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function viewEprescribeAction() {
		$messagingId = $this->_getParam('messagingId');
		$messaging = new Messaging();
		$messaging->messagingId = $messagingId;
		$messaging->populate();
		$missingPON = '';
		if ($messaging->objectClass == 'MedicationRefillRequest' && preg_match('/\(Invalid\/Missing PON\)/',$messaging->note)) {
			$missingPON = $messagingId;
		}
		$relatedMessage = '';
		$prettyPrint = __('There is no more information available about this message');
		//if (strtolower($messaging->messageType) == 'error' && strlen($messaging->rawMessage) > 0) {
		if (strlen($messaging->rawMessage) > 0) {
			$xml = new SimpleXMLElement($messaging->rawMessage);
			$relatesToMessageID = (string)$xml->Header->RelatesToMessageID;
			$tmpMsg = new Messaging();
			$tmpMsg->messagingId = $relatesToMessageID;
			$tmpMsg->populate();
			if (strtolower($tmpMsg->messageType) == 'newrx' && strlen($tmpMsg->rawMessage) > 0) {
				$tmlXmlMsg = new SimpleXMLElement($tmpMsg->rawMessage);
				$relatedMessage = Messaging::convertXMLMessage($tmlXmlMsg->Body->NewRx,$relatedMessage);
				$relatedMessage = implode("\n",$relatedMessage);
			}
			$prettyPrint = $this->_generateRefillRequestDetails($xml->Body,$tmp);
		}
		$this->view->prettyPrint = $prettyPrint;
		$this->view->missingPON = $missingPON;
		$this->view->messaging = $messaging;
		$this->view->relatedMessage = $relatedMessage;

		$this->render('view-eprescribe');
	}

	public function viewInboundFaxAction() {
		$messagingId = (int)$this->_getParam('messagingId');
		$messaging = new Messaging();
		$messaging->messagingId = $messagingId;
		$messaging->populate();
		$inboundFax = $messaging->object;
		if (!$inboundFax instanceof MessagingInboundFax) {
			$this->view->error = __('No additional details');
		}
		else {
			$this->view->messaging = $messaging;
			$personId = (int)$inboundFax->personId;
			$patient = new Patient();
			if ($personId > 0) {
				$patient->personId = $personId;
				$patient->populate();
			}
			$this->view->patient = $patient;

			$attachment = new Attachment();
			$attachment->attachmentId = $messaging->attachmentId;
			$attachment->populate();
			$this->view->attachment = $attachment;
		}
		$this->render('view-inbound-fax');
	}

	public function viewOutboundFaxAction() {
		$messagingId = (int)$this->_getParam('messagingId');
		$messaging = new Messaging();
		if ($messagingId > 0) {
			$messaging->messagingId = $messagingId;
			$messaging->populate();
			$messaging->object->checkFinalDisposition();
		}
		$this->view->messaging = $messaging;
		$this->render('view-outbound-fax');
	}

	public function processAttachToNotesAction() {
		$attachmentId = (int)$this->_getParam('attachmentId');
		$attachmentReferenceId = $this->_getParam('attachmentReferenceId');
		$attachment = new Attachment();
		$attachment->attachmentId = $attachmentId;
		$attachment->populate();
		$attachment->attachmentReferenceId = $attachmentReferenceId;
		$attachment->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);
	}

	public function processResendOutboundFaxAction() {
		$messagingId = (int)$this->_getParam('messagingId');
		$faxNumber = $this->_getParam('faxNumber');
		$messaging = new Messaging();
		if ($messagingId > 0) {
			$messaging->messagingId = $messagingId;
			$messaging->populate();
		}
		if ($messagingId->auditId > 0) {
			$messaging->faxNumber = '';
			if (is_numeric($faxNumber) && strlen($faxNumber) > 9) {
				$messaging->faxNumber = $faxNumber;
			}
			$messaging->resend = 1;
			$messaging->persist();
			$audit = new Audit();
			$audit->auditId = $messagingAudit->auditId;
			$audit->populate();
			$audit->startProcessing = '0000-00-00 00:00:00';
			$audit->endProcessing = '0000-00-00 00:00:00';
			$audit->persist();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);
	}

	public function processCheckInboundAction() {
		$inbound = new eFaxInbound();
		$inbounds = array();
		$ret = 0;//$inbound->checkInbounds();
		if ($ret > 0) {
			$inbounds[] = 'eFax: '.$ret;
		}
		$ret = ePrescribe::pull();
		if ($ret > 0) {
			$inbounds[] = 'ePrescribe: '.$ret;
		}
		if (!isset($inbounds[0])) {
			$data = 'none';
		}
		else {
			$data = "\n".implode("\n",$inbounds);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function getContextMenuAction() {
		//placeholder function, template is xml and autorenders when called as messaging.xml/get-context-menu
	}

	public function processRefillResponseAction() {
		$messagingId = $this->_getParam('messagingId');
		$response = $this->_getParam('response');
		$params = $this->_getParam('data');
		$refillResponse = new MedicationRefillResponse();
		$refillResponse->messageId = $messagingId;
		$ret = $refillResponse->send($response,$params);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function viewRawMessageAction() {
		$messagingId = $this->_getParam('messagingId');
		$messaging = new Messaging();
		$messaging->messagingId = $messagingId;
		$messaging->populate();
		$this->view->rawMessage = $messaging->rawMessage;
		$this->render('view-raw-message');
	}

	public function refillResponseApprovedAction() {
		$messagingId = $this->_getParam('messagingId');
		$refillRequest = new MedicationRefillRequest();
		$refillRequest->messageId = $messagingId;
		$refillRequest->populate();
		$isScheduled = false;
		$note = '';
		if ($refillRequest->medication->isScheduled()) {
			//$this->_setParam('messagingId',$messagingId);
			//$this->_setParam('controlled',1);
			$note = 'This Refill Request is for a controlled substance (Schedule III - V).';
			//return $this->refillResponseDeniedAction();
			$isScheduled = true;
		}
		$this->view->isScheduled = $isScheduled;
		$this->view->note = $note;

		$messaging = new Messaging();
		$messaging->messagingId = $messagingId;
		$messaging->populate();
		$refills = '0';
		if (strlen($messaging->refills) > 0) {
			$refills = $messaging->refills;
			$arrRefills = explode(' ',$messaging->refills);
			$quantity = $arrRefills[0];
			$qualifier = isset($arrRefills[1])?$arrRefills[1]:'';
			$refills = $quantity;
			/*else if ($arrRefills[1] == 'PRN') {
			}*/
		}
		$this->view->refillRequest = $refillRequest;

		$this->view->refills = $refills;
		$this->view->messagingId = $messagingId;

		$xml = new SimpleXMLElement($messaging->rawMessage);
		$this->view->data = $this->_generateInfoDetails($xml->Body->RefillRequest);
		$refillRequestDetails = $this->_generateRefillRequestDetails($xml->Body,$refillRequestDetails);
		$this->view->refillRequestDetails = $refillRequestDetails;
		$this->render('refill-response-approved');
	}

	protected function _generateInfoDetails(SimpleXMLElement $xml) {
		$data = array();
		$data['Pharmacy'] = (string)$xml->Pharmacy->StoreName;
		$provider = $xml->Prescriber->Name;
		$data['Provider'] = (string)$provider->LastName.', '.(string)$provider->FirstName.' '.(string)$provider->MiddleName;
		$patient = $xml->Patient->Name;
		$data['Patient'] = (string)$patient->LastName.', '.(string)$patient->FirstName.' '.(string)$patient->MiddleName;
		$data['Medication'] = (string)$xml->MedicationPrescribed->DrugDescription;
		return $data;
	}

	protected function _generateRefillRequestDetails(SimpleXMLElement $xml,&$output,$level=-1) {
		static $transformKeys = array(
			'NCPDPID'=>'NCPDPID',
			'FileID'=>'File ID',
			'SPI'=>'SPI',
		);
		$level++;
		foreach ($xml as $key=>$value) {
			$key = (string)$key;
			if (isset($transformKeys[$key])) {
				$key = $transformKeys[$key];
			}
			else {
				$key = preg_replace('/([A-Z])(?![A-Z])/',' $1',$key);
				$key = trim(ucwords($key));
			}
			$children = $value->children();
			if ($children) {
				if ($level > 1) {
					$output .= "\n".str_repeat(' ',($level-2)+1).'=== '.$key.' ===';
				}
				$this->_generateRefillRequestDetails($children,$output,$level);
			}
			else {
				$value = (string)$value;
				$output .= "\n".str_repeat(' ',$level-2).$key.': '.$value;
			}
		}
		return $output;
	}

	public function refillResponseDeniedAction() {
		$messagingId = $this->_getParam('messagingId');
		$messaging = new Messaging();
		$messaging->messagingId = $messagingId;
		$messaging->populate();
		$refills = '0';
		if (strlen($messaging->refills) > 0) {
			$refills = $messaging->refills;
			$arrRefills = explode(' ',$messaging->refills);
			$quantity = $arrRefills[0];
			$qualifier = isset($arrRefills[1])?$arrRefills[1]:'';
			$refills = $quantity;
			/*else if ($arrRefills[1] == 'PRN') {
			}*/
		}
		$refillRequest = new MedicationRefillRequest();
		$refillRequest->messageId = $messagingId;
		$refillRequest->populate();
		$patientId = $refillRequest->medication->personId;
		$medicationId = $refillRequest->medicationId;
		$medicationIterator = new MedicationIterator();
		$filters = array('patientId'=>$patientId);
		$medicationIterator->setFilter($filters);
		$medications = array(''=>'');
		foreach ($medicationIterator as $medication) {
			if ($medication->medicationId == $medicationId) continue;
			$medications[$medication->medicationId] = $medication->description;
		}
		$this->view->medications = $medications;

		$this->view->refills = $refills;
		$this->view->refillRequest = $refillRequest;
		$this->view->messagingId = $messagingId;

		$this->view->reasonCodes = $this->getDenialReasonCodes();

		$note = '';
		$denialReason = '';
		$isScheduled = false;
		if ($refillRequest->medication->isScheduled()) {
			$denialReason = __('Requested medication is controlled; Call or Fax to follow');
			$isScheduled = true;
		}
		$this->view->isScheduled = $isScheduled;
		$this->view->denialReason = $denialReason;
		$this->view->note = $note;
		$xml = new SimpleXMLElement($messaging->rawMessage);
		$this->view->data = $this->_generateInfoDetails($xml->Body->RefillRequest);
		$refillRequestDetails = $this->_generateRefillRequestDetails($xml->Body,$refillRequestDetails);
		$this->view->refillRequestDetails = $refillRequestDetails;
		$this->render('refill-response-denied');
	}

	protected function getDenialReasonCodes() {
		$reasonCodes = array(''=>'');
		$reasonCodes['AA'] = 'Patient unknown to the Prescriber';
		$reasonCodes['AB'] = 'Patient never under Prescriber care';
		$reasonCodes['AC'] = 'Patient no longer under Prescriber care';
		$reasonCodes['AD'] = 'Patient has requested refill too soon';
		$reasonCodes['AE'] = 'Medication never prescribed for the patient';
		$reasonCodes['AF'] = 'Patient should contact Prescriber first';
		$reasonCodes['AG'] = 'Refill not appropriate';
		$reasonCodes['AH'] = 'Patient has picked up prescription';
		$reasonCodes['AJ'] = 'Patient has picked up partial fill of prescription';
		$reasonCodes['AK'] = 'Patient has not picked up prescription, drug returned to stock';
		$reasonCodes['AL'] = 'Change not appropriate';
		$reasonCodes['AM'] = 'Patient needs appointment';
		$reasonCodes['AN'] = 'Prescriber not associated with this practice or location';
		$reasonCodes['AO'] = 'No attempt will be made to obtain Prior Authorization';
		$reasonCodes['AP'] = 'Request already responded to by other means (e.g. phone or fax)';
		return $reasonCodes;
	}

	public function filterAction() {
		$statusOptions = array();
		$messagingIterator = new MessagingIterator(null,false);
		$messagingIterator->setFilters(array('optionGroup'=>'status'));
		foreach ($messagingIterator as $message) {
			$statusOptions[] = $message->status;
		}
		$messageOptions = array('EPrescribes'=>'EPrescribe','InboundFaxes'=>'InboundFax','OutboundFaxes'=>'OutboundFax');
		$this->view->statusOptions = $statusOptions;
		$this->view->messageOptions = $messageOptions;

		$filters = $this->_session->filters;
		$message = array();
		$status = array();
		$resolution = '';
		if (isset($filters['message'])) {
			foreach (explode(',',$filters['message']) as $val) {
				$message[$val] = $val;
			}
		}
		if (isset($filters['status'])) {
			foreach (explode(',',$filters['status']) as $val) {
				$status[$val] = $val;
			}
		}
		if (isset($filters['resolution'])) {
			$resolution = $filters['resolution'];
		}
		$dateBegin = date('Y-m-d',strtotime('-7days'));
		$dateEnd = date('Y-m-d');
		if (isset($filters['dateStatus'])) {
			$x = explode(',',$filters['dateStatus']);
			$dateBegin = date('Y-m-d',strtotime($x[0]));
			$dateEnd = date('Y-m-d',strtotime($x[1]));
		}
		$this->view->message = $message;
		$this->view->status = $status;
		$this->view->resolution = $resolution;
		$this->view->dateBegin = $dateBegin;
		$this->view->dateEnd = $dateEnd;
		$this->render('filter');
	}

	public function processSetPonAction() {
		$messagingId = $this->_getParam('messagingId');
		$medicationId = (int)$this->_getParam('medicationId');
		$personId = (int)$this->_getParam('personId');
		$ret = false;
		$messaging = new Messaging();
		$messaging->messagingId = $messagingId;
		$messaging->populate();
		//if ($medicationId > 0 && strlen($messaging->rawMessage) > 0) {
		if ($personId > 0 && strlen($messaging->rawMessage) > 0) {
			$xml = new SimpleXMLElement($messaging->rawMessage);
			$auditId = Medication::getAuditId($medicationId);
			if ($medicationId > 0) {
				$xml->Body->RefillRequest->PrescriberOrderNumber = $medicationId.'_'.$auditId;
				$messaging->rawMessage = $xml->asXML();
			}
			else {
				//$xml->Body->RefillRequest->PrescriberOrderNumber = $personId;
			}
			$messaging->personId = $personId;
			$messaging->auditId = $auditId;
			$messaging->unresolved = 0;
			$messaging->note = str_replace('Missing PON','Missing PON - fixed',$messaging->note);
			$messaging->persist();
			$refillRequest = new MedicationRefillRequest();
			$refillRequest->messageId = $messagingId;
			$refillRequest->populate();
			$refillRequest->medicationId = $medicationId;
			$refillRequest->persist();
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function processMarkResolvedAction() {
		$messagingId = $this->_getParam('messagingId');
		$messaging = new Messaging();
		$messaging->messagingId = $messagingId;
		$ret = false;
		if ($messaging->populate()) {
			$messaging->unresolved = 0;
			$messaging->persist();
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function processDirectDeniedAction() {
		$messagingId = $this->_getParam('messagingId');
		$code = $this->_getParam('code');
		$refillResponse = new MedicationRefillResponse();
		$refillResponse->messageId = $messagingId;
		$reasonCodes = $this->getDenialReasonCodes();
		if (!isset($reasonCodes[$code])) {
			$code = 'AA';
		}
		$inputs = array(
			'reason' => $reasonCodes[$code],
			'reasonCode' => $code,
		);
		$ret = $refillResponse->send('denied',$inputs);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function viewGatewayResponseRawMessageAction() {
		$messagingId = $this->_getParam('messagingId');
		$messaging = new Messaging();
		$messaging->messagingId = $messagingId;
		$messaging->populate();
		$this->view->gatewayResponseRawMessage = $messaging->rawMessageResponse;
		$this->render();
	}

}
