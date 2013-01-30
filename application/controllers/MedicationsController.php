<?php
/*****************************************************************************
*       MedicationsController.php
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


/**
 * Medications controller
 */
class MedicationsController extends WebVista_Controller_Action {

	protected $_form;
	protected $_session;
	protected $_medication;

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
		if (!isset($this->_session->filters) || !isset($this->_session->filters['active'])) $this->_session->filters = array('active'=>1,'discontinued'=>1,'patientReported'=>1);
	}

	public function ajaxDiscontinueMedicationAction() {
		$medicationIds = explode(',',$this->_getParam('medicationId'));
		foreach ($medicationIds as $medicationId) {
			$medication = new Medication();
			$medication->medicationId = (int)$medicationId;
			$medication->populate();
			$medication->daysSupply = -1;
			$medication->dateDiscontinued = date('Y-m-d H:i:s');
			$medication->persist();
		}
		$data = array();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listMedicationsAction() {
		$personId = (int)$this->_getParam('personId');
		$filters = $this->_session->filters;
		$activeOnly = (int)$this->_getParam('activeOnly');
		$rows = array();
		$medicationIterator = array();
		if ($personId > 0) {
			$filter = array('patientId'=>$personId);
			if ($activeOnly || ($filters['active'] && !$filters['discontinued'])) {
				$filter['active'] = 1;
			}
			else if ($filters['discontinued'] && !$filters['active']) {
				$filter['active'] = 0;
			}
			if ($filters['active'] || $filters['discontinued']) $filter['flag'] = true;
			if (!$filters['active'] && !$filters['discontinued'] && !$filters['patientReported']) {
				$medicationIterator = array();
			}
			else {
				$filter['patientReported'] = $filters['patientReported'];
				$medicationIterator = new MedicationIterator();
				$medicationIterator->setFilter($filter);
			}
		}
		$unsigned = array();
		$signed = array();
		$discontinued = array();
		$patientReported = array();
		foreach ($medicationIterator as $medication) {
			if ($medication->daysSupply == -1 && $medication->dateDiscontinued != '0000-00-00 00:00:00') {
				$discontinued[] = $medication;
				continue;
			}
			if ($medication->eSignatureId > 0) { // signed
				$signed[] = $medication;
			}
			else {
				if ($medication->patientReported) { // unsigned
					$patientReported[] = $medication;
				}
				else {
					$unsigned[] = $medication;
				}
			}
		}
		krsort($signed);
		krsort($patientReported);
		krsort($discontinued);
		$medications = array_merge($unsigned,$signed,$patientReported,$discontinued);
		foreach ($medications as $medication) {
			$expiration = '';
			if ($medication->daysSupply == -1 && $medication->dateDiscontinued != '0000-00-00 00:00:00') {
				$expiration = '<font color="#ff0000">'.date('m/d/Y h:i A',strtotime($medication->dateDiscontinued)).'</font>';
			}
			else if ($medication->dateBegan != '0000-00-00 00:00:00') {
				$expiration = date('m/d/Y',strtotime($medication->expires));
				$color = '#00ff00'; // green
				if (strtotime(date('m/d/Y')) >= strtotime(substr($expiration,0,10))) {
					$color = '#ff0000'; // red
				}
				$expiration = '<font color="'.$color.'">'.$expiration.'</font>';
			}
			$tmp = array();
			$tmp['id'] = $medication->medicationId;
			$tmp['data'][] = '';
			$tmp['data'][] = $medication->description;
			$tmp['data'][] = $medication->displayAction;
			$tmp['data'][] = $medication->displayStatus;
			$tmp['data'][] = $expiration;
			$tmp['data'][] = '';
			$tmp['data'][] = $medication->refillsRemaining;
			$tmp['data'][] = $medication->comment;
			$tmp['data'][] = (int)$medication->eSignatureId;
			//$tmp['data'][] = $medication->datePrescribed; // extra row
			$rows[] = $tmp;
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function editMedicationAction() {
		$personId = (int)$this->_getParam('personId');
		$medicationId = (int)$this->_getParam('medicationId');
		$refillRequestId = $this->_getParam('refillRequestId');
		$copy = $this->_getParam('copy');
		$discontinue = $this->_getParam('discontinue');

		$patient = new Patient();
		$patient->personId = $personId;
		$patient->populate();

		if (strlen($copy) > 0) {
			$this->view->copy = $copy;
		}

		if (strlen($discontinue) > 0) {
			$this->view->discontinue = $discontinue;
		}

		$name = Medication::ENUM_ADMIN_SCHED;
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		$enumerationsClosure = new EnumerationsClosure();
		$rowset = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$scheduleOptions = array();
		$adminSchedules = array();
		foreach ($rowset as $row) {
			$scheduleOptions[] = $row->key;
			$adminSchedules[$row->key] = $row->name;
		}
		$this->view->scheduleOptions = $scheduleOptions;
		$this->view->adminSchedules = $adminSchedules;

		$this->view->chBaseMed24Url = Zend_Registry::get('config')->healthcloud->CHMED->chBaseMed24Url;
		$this->view->chBaseMed24DetailUrl = Zend_Registry::get('config')->healthcloud->CHMED->chBaseMed24DetailUrl;
		$this->_form = new WebVista_Form(array('name' => 'new-medication'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . "medications.raw/process-add-medication");

		$this->_medication = new Medication();
		$this->_medication->personId = $personId;
		
		if ($medicationId > 0) {
			$this->_medication->medicationId = (int)$medicationId;
			$this->_medication->populate();
		}
		if (!strlen($this->_medication->pharmacyId) > 0) {
			$this->_medication->pharmacyId = $patient->defaultPharmacyId;
			$this->_medication->pharmacy->populate();
		}
		$this->view->defaultPharmacy = $this->_medication->pharmacy;


		if (strlen($discontinue) > 0) {
			$this->_medication->daysSupply = -1;
			$this->_medication->dateDiscontinued = date('Y-m-d H:i:s');
			$this->_medication->persist();
		}

		if (strlen($copy) > 0) {
			$this->_medication->medicationId = 0;
			//$this->_medication->datePrescribed = date('Y-m-d H:i:s');
		}
		if (!strlen($this->_medication->datePrescribed) > 0 || $this->_medication->datePrescribed == '0000-00-00 00:00:00') $this->_medication->datePrescribed = date('Y-m-d H:i:s');
		$baseMed24 = new BaseMed24();
		if (strlen($refillRequestId) > 0) {
			$this->_medication->refillRequestId = $refillRequestId;
			$messaging = new Messaging();
			$messaging->messagingId = $refillRequestId;
			$messaging->populate();
			if (strlen($messaging->rawMessage) > 0) {
				$xml = new SimpleXMLElement($messaging->rawMessage);
				$xmlMedication = $xml->Body->RefillRequest->MedicationPrescribed;
				// override pkey
				// search by NDC if exists
				if ((string)$xmlMedication->DrugCoded->ProductCodeQualifier == 'ND' && strlen((string)$xmlMedication->DrugCoded->ProductCode) > 0) {
					$ndc = (string)$xmlMedication->DrugCoded->ProductCode;
					$baseMed24->hipaa_ndc = $ndc;
					$baseMed24->populateByHipaaNDC();
					if (!strlen($baseMed24->pkey) > 0) {
						// search by tradename
						$baseMed24->populateByDrugDescription((string)$xmlMedication->DrugDescription);
						if (strlen($baseMed24->pkey) > 0) {
							$this->_medication->pkey = $baseMed24->pkey;
						}
					}
					else {
						$this->_medication->pkey = $baseMed24->pkey;
					}
				}
			}
		}
		$this->view->baseMed24 = $baseMed24;

		$identity = Zend_Auth::getInstance()->getIdentity();

		$user = new User();
		$user->userId = (int)$identity->userId;
		$user->populate();
		$building = null;
		if (strlen($user->preferences) > 0) {
			$xmlPreferences = new SimpleXMLElement($user->preferences);
			$room = new Room();
			$room->roomId = (int)$xmlPreferences->currentLocation;
			$room->populate();
			$building = $room->building;
		}
		if ($building === null) {
			$building = new Building();
			$building->buildingId = (int)$user->defaultBuildingId;
			$building->populate();
		}
		$defaultBuildingId = 0;
		$location = '';
		if ($building->buildingId > 0) {
			$defaultBuildingId = (int)$building->buildingId;
			$location = $building->name;
		}
		$EPrescriber = new EPrescriber();
		$EPrescriber->populateWithBuildingProvider($defaultBuildingId,(int)$user->personId);
		$enabledePrescribe = false;
		if (strlen($EPrescriber->SSID) > 0) {
			$enabledePrescribe = true;
		}
		$this->view->location = $location;
		$this->view->enabledePrescribe = $enabledePrescribe;

		$this->_form->loadORM($this->_medication, "Medication");
		$this->_form->setWindow('windowNewMedication');
		$this->view->form = $this->_form;
		$this->view->medication = $this->_medication;
		$this->view->quantityQualifiers = Medication::listQuantityQualifiersMapping();
		$this->render('new-medication');
	}

	public function processAddMedicationAction() {
		$personId = (int)$this->_getParam('personId');
		$medicationId = (int)$this->_getParam('medicationId');
		$copy = $this->_getParam('copy');
		$discontinue = (int)$this->_getParam('discontinue');
		$forced = (int)$this->_getParam('forced');

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;

		$patient = new Patient();
		$patient->personId = $personId;
		$patient->populate();

		$this->_medication = new Medication();
		$this->_medication->personId = $personId;

		if ($medicationId > 0) {
			$this->_medication->medicationId = (int)$medicationId;
			$this->_medication->populate();
		}

		$params = $this->_getParam('medication');
		$params['daysSupply'] = preg_replace('/,/','',$params['daysSupply']);
		$params['quantity'] = preg_replace('/,/','',$params['quantity']);
		$params['refills'] = preg_replace('/,/','',$params['refills']);
		$this->_medication->populateWithArray($params);
		$medicationId = (int)$this->_medication->medicationId;
		if ($medicationId > 0) {
			$eSignatureId = (int)ESignature::retrieveSignatureId('Medication',$this->_medication->documentId);
			if ($eSignatureId > 0) {
				$data = array('error'=>__('Failed to edit, selected medication is already signed.'));
				$json->direct($data);
				return;
			}
		}
		if (!strlen($this->_medication->pharmacyId) > 0) {
			$this->_medication->pharmacyId = $patient->defaultPharmacyId;
		}
		if (strlen($copy) > 0) {
			$this->_medication->medicationId = 0;
		}
		$this->_medication->datePrescribed = date('Y-m-d H:i:s');
		$this->_medication->prescriberPersonId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$this->_medication->provider->populate();
		// daysSupply computation
		/*
		if (method_exists('DrugScheduleDaysSupply',$this->_medication->schedule)) {
			$methodName = $this->_medication->schedule;
			$this->_medication->daysSupply = (int)DrugScheduleDaysSupply::$methodName($this->_medication->quantity);
		}
		*/
		$data = array();
		if (!$forced && $this->_medication->isScheduled()) {
			$data['confirmation'] = 'Medication is a controlled substance, it cannot be sent electronically. The Rx will be printed and needs a wet signature before it can be faxed to the pharmacy or handed to the patient.';
		}
		else if (!$forced && $this->_medication->isFreeForm()) {
			$data['confirmation'] = 'If the entered freeform medication is a controlled medication, it cannot be eprescribed and will be rejected';
		}
		else {
			$ret = $this->_medication->ssCheck();
			if (isset($ret[0])) {
				$error = 'The following error';
				if (isset($ret[1])) {
					$error .= 's';
				}
				$error .= ' for medication detected:';
				foreach ($ret as $val) {
					$error .= "\n*) ".$val;
				}
				$data['error'] = $error;
			}
		}
		$pharmacy = new Pharmacy();
		$pharmacy->pharmacyId = $this->_medication->pharmacyId;
		$pharmacy->populate();
		if ($pharmacy->print) {
			$this->_medication->transmit = 'Print';
		}
		if (!isset($data['error']) && !isset($data['confirmation'])) {
			$this->_medication->persist();
			$data['medicationId'] = $this->_medication->medicationId;
		}
		$json->direct($data);
	}

	/**
	 * Default action to dispatch
	 */
	public function indexAction() {
		$this->render('index');
	}

	public function toolbarAction() {
		header("Cache-Control: public");
		header("Pragma: public");

		$cache = Zend_Registry::get('cache');
		$cacheKey = "toolbar-" . Menu::getCurrentlySelectedActivityGroup() . "-" . Menu::getCurrentUserRole();
		$cacheKey = str_replace('-', '_', $cacheKey);
		$cacheKey = str_replace('/', '_', $cacheKey);
		if ($cache->test($cacheKey."_hash")) {
			$hash = $cache->load($cacheKey."_hash");
			$lastModified = $cache->load($cacheKey."_lastModified");
			$headers = getallheaders();
			if (isset($headers['If-None-Match']) && preg_match('/'.$hash.'/', $headers['If-None-Match'])) {
				header("Last-Modified: " . $lastModified);
				header('HTTP/1.1 304 Not Modified');
				exit;
			}
		}

		if ($cache->test($cacheKey)) {
			$items = $cache->load($cacheKey);
		}
		else {
			$items = $this->render('toolbar');
			$hash = md5($items);
			$lastModified = gmdate("D, d M Y H:i:s")." GMT";
			$objConfig = new ConfigItem();
			$objConfig->configId = 'enableCache';
			$objConfig->populate();
			if ($objConfig->value) {
				$cache->save($hash, $cacheKey."_hash", array('tagToolbar'));
				$cache->save($lastModified, $cacheKey."_lastModified", array('tagToolbar'));
				$cache->save($items, $cacheKey, array('tagToolbar'));
			}
			header("ETag: ". $hash);
			header("Last-Modified: ". $lastModified);
			header("Content-length: "  . mb_strlen($items));
		}
		if (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
			header("Content-type: application/xhtml+xml");
		}
		else {
			header("Content-type: text/xml");
		}
		return $items;

	}

	public function selectPharmacyAction() {
		$personId = (int)$this->_getParam('personId');
		$selectedPharmacyId = preg_replace('/[^a-zA-Z0-9-]+/','',$this->_getParam('selectedPharmacyId'));
		$patient = new Patient();
		$patient->personId = $personId;
		$patient->populate();
		$patient->homeAddress->populateWithType('HOME');
		$this->view->patient = $patient;
		$defPharmacy = new Pharmacy();
		$defPharmacy->pharmacyId = $patient->defaultPharmacyId;
		if ($defPharmacy->populate()) {
			$this->view->defaultPharmacy=$defPharmacy;
		}
		$this->view->selectedPharmacyId = $selectedPharmacyId;
		$practice = new Practice();
                $practice->practiceId = MainController::getActivePractice();
                $practice->populate();
                $practice->primaryAddress->populate();
		$this->view->practice = $practice;
	}

	public function listPharmaciesAction() {
		$filters = (array)$this->_getParam('filters');
		if (count($filters) == 0) $filters['preferred'] =1;
		$pharmacy = new Pharmacy();
		$pharmacyIter = $pharmacy->getIterator();
		$pharmacyIter->setFilters($filters);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		$pharmacyIter->getDbColumns();
                $json->direct(array("rows" => $pharmacyIter->toJsonArray('pharmacyId',array('pharmacyId','StoreName','AddressLine1','City','newRxSupport','refReqSupport','rxFillSupport','rxChgSupport','canRx','rxHisSupport','rxEligSupport'))),true);
	}
	protected function getScheduleOptions() {
		$scheduleOptions = array('BID','MO-WE-FR','NOW','ONCE','Q12H','Q24H','Q2H','Q3H','Q4H','Q5MIN PRN');
		return $scheduleOptions;
	}

	public function transmitEprescriptionAction() {
		$medicationId = (int)$this->_getParam('medicationId');
		$medication = new Medication();
		$medication->medicationId = $medicationId;
		$medication->populate();
		//echo $medication->toString();
		//echo $medicationId;
		$data = $medication->toArray();
		$practice = new Practice();
		$practice->practiceId = MainController::getActivePractice();
		$practice->populate();
		$data['practiceName'] = $practice->name;
		$pharmacy = new Pharmacy();
		$pharmacy->pharmacyId = $medication->pharmacyId;
		$pharmacy->populate();
		$data['pharmacy'] = $pharmacy->toArray();
		$prescriber = new Provider();
		$prescriber->personId = $medication->prescriberPersonId;
		$prescriber->populate();
		$prescriber->person->populate();
		$data['prescriber'] = $prescriber->toArray();
		$data['prescriber']['agentFirstName'] = '';
		$data['prescriber']['agentLastName'] = '';
		$data['prescriber']['agentSuffix'] = '';
		$addressIterator = new AddressIterator();
		$addressIterator->setFilters(array('class' => 'person','personId' => $prescriber->personId));
		$data['prescriber']['address'] = $addressIterator->first()->toArray();
		$phoneIterator = new PhoneNumberIterator();
		$phoneIterator->setFilters(array('class' => 'person','personId' => $prescriber->personId));
		$data['prescriber']['phone'] = $phoneIterator->first()->toArray();
		$patient = new Patient();
		$patient->personId = $medication->personId;
		$patient->populate();
		$data['patient'] = $patient->toArray();
		$phoneIterator->setFilters(array('class' => 'person','personId' => $patient->personId));
		$data['patient']['phone'] = $phoneIterator->first()->toArray();
		//var_dump($data);exit;
		$data = $this->makePostArray($data);
		//var_dump($this->makePostArray($data));exit;
		//var_dump($data);exit;
		$transmitEPrescribeURL = Zend_Registry::get('config')->healthcloud->URL;
                $transmitEPrescribeURL .= "SSRX/NewRx?apiKey=" . Zend_Registry::get('config')->healthcloud->apiKey;
                $cookieFile = tempnam(sys_get_temp_dir(),"ssddcookies_");
		$ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$transmitEPrescribeURL);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                $output = curl_exec ($ch);
		echo $output;
		exit;
	}
	private function makePostArray($data,$leading = '') {
		$pData = array();
		foreach($data as $key => $value) {
			if (is_array($value)) {
				$pData = array_merge($pData,$this->makePostArray($value,$leading . '[' . $key . ']'));
			}
			else {
				$pData ['medication' . $leading . '[' . $key . ']'] = $value;
			}
		}
		return $pData;
	}

	public function getPrescriptionPdfAction() {
		$medicationIds = explode(',',$this->_getParam('medicationId',''));
		$xmlData = '';
		foreach ($medicationIds as $medicationId) {
			$medicationId = (int)$medicationId;
			if (!$medicationId > 0) continue;
			$medication = new Medication();
			$medication->medicationId = $medicationId;
			$medication->populate();
			$xmlData .=  PdfController::toXML($medication,'Medication',null);
		}
		//ff560b50-75d0-11de-8a39-0800200c9a66 is uuid for prescription PDF
		$this->_forward('pdf-merge-attachment','pdf', null, array('attachmentReferenceId' => 'ff560b50-75d0-11de-8a39-0800200c9a66','xmlData'=>$xmlData));
	}

	public function medicationsContextMenuAction() {
		//placeholder function, template is xml and autorenders when called as medications-context-menu.raw
	}

	public function processPrintedRxAction() {
		// transmit
		// dateTransmitted
		$medicationIds = explode(',',$this->_getParam('medicationId',''));
		foreach ($medicationIds as $medicationId) {
			$medicationId = (int)$medicationId;
			if (!$medicationId > 0) continue;
			$medication = new Medication();
			$medication->medicationId = $medicationId;
			$medication->populate();
			$medication->transmit = 'Print';
			$date = date('Y-m-d H:i:s');
			$medication->dateTransmitted = $date;
			$medication->dateBegan = $date;
			$medication->persist();
		}

		$rows = array();
		$rows['msg'] = __('Saved successfully.');
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public static function buildJSJumpLink($objectId,$signingUserId,$objectClass) {
		if ($objectClass == 'MedicationRefillRequest') {
			$orm = new MedicationRefillRequest();
			$orm->messageId = $objectId;
			$orm->populate();
			$messaging = new Messaging();
			$messaging->messagingId = $objectId;
			$messaging->populate();
			$medicationId = (int)$orm->medicationId;
			if ($medicationId > 0) $objectId = $medicationId;
			$patientId = (int)$messaging->personId;
		}
		else {
			$medication = new Medication();
			$medication->medicationId = $objectId;
			$medication->populate();
			$patientId = $medication->personId;
		}
		$objectClass = 'Medications'; // temporarily hard code objectClass based on MainController::getMainTabs() definitions
		$js = parent::buildJSJumpLink($objectId,$patientId,$objectClass);
		$js .= <<<EOL

mainTabbar.setOnTabContentLoaded(function(tabId){
	loadMedication("{$objectId}");
	/*openNewMedicationWindow("{$objectId}");*/
});

EOL;
		return $js;
	}

	public function listMedicationRefillsAction() {
		$personId = (int)$this->_getParam('personId');
		$rows = array();

		$responded = array();
		$refillRequestIterator = array();
		if ($personId > 0) {
			$refillRequest = new MedicationRefillRequest();
			$refillRequestIterator = $refillRequest->getIteratorByPersonId($personId);
		}
		foreach ($refillRequestIterator as $refill) {
			$row = array();
			$row['id'] = $refill->messageId;
			//$row['data'][] = $this->view->baseUrl.'/medications.raw/medication-refills-info?messagingId='.$row['id'];

			$refillDetails = $this->_generateRefillDetails($refill->messageId);
			if ($refillDetails === false) continue;

			$pharmacy = $refillDetails['pharmacy'];
			$provider = $refillDetails['prescriber'];
			$patient = $refillDetails['patient'];
			$medication = $refillDetails['medication'];
			$responseInfo = '';
			if ($refill->refillResponse->dateTime != '' && $refill->refillResponse->dateTime != '0000-00-00 00:00:00') {
				$responseInfo .= 'Req. '.$refillDetails['dateRequested'];
				$responseInfo .= '<br />Res. '.date('m/d/Y h:iA',strtotime($refill->refillResponse->dateTime));
			}
			$medicationDescription = $refill->medication->description;
			$medicationDescription = $refillDetails['medicationDescription'];
			$row['data'][] = '<table class="refillInfo"><tbody>
				<tr>
					<th class="firstCol">'.__('Pharmacy Data').'</th>
					<th>'.__('Prescriber Data').'</th>
					<th>'.__('Patient').'</th>
					<th>'.__('Medication Prescribed').'</th>
					<th class="lastCol">'.__('Response Info').'</th>
				</tr>
				<tr>
					<td class="firstCol" title="'.htmlspecialchars(str_replace('<br />',' ',$pharmacy)).'">'.$pharmacy.'</td>
					<td title="'.htmlspecialchars(str_replace('<br />',' ',$provider)).'">'.$provider.'</td>
					<td title="'.htmlspecialchars(str_replace('<br />',' ',$patient)).'">'.$patient.'</td>
					<td title="'.htmlspecialchars(str_replace('<br />',' ',str_replace(' &nbsp; ',' ',$medication))).'">'.$medication.'</td>
					<td class="lastCol" title="'.htmlspecialchars(str_replace('<br />',' ',$responseInfo)).'">'.$responseInfo.'</td>
				</tr>
			</tbody></table>';
			$row['data'][] = $medicationDescription.' (Req. '.date('m/d/Y',strtotime($refillDetails['dateRequested'])).')';
			$status = $refill->status;
			$description = trim(preg_replace('/([A-Z])(?![A-Z])/',' $1',$refill->refillResponse->response)) .': '.$refill->refillResponse->message;
			if (!strlen($status) > 0) {
				$description = '';
				$status = '';
				//$isScheduled = $refill->medication->isScheduled();
				//$controlled = '';
				//if (!$isScheduled) {
					$status = '<input type="button" name="approved-'.$refill->messageId.'" id="approved-'.$refill->messageId.'" value="'.__('Approve').'" onClick="refillResponse(\''.$refill->messageId.'\',\'approved\')" style="width:70px;" />';
				//}
				//else {
				//	$controlled = ",'1'";
				//}
				$status .= '<input type="button" name="denied-'.$refill->messageId.'" id="denied-'.$refill->messageId.'" value="'.__('Deny').'" onClick="refillResponse(\''.$refill->messageId.'\',\'denied\')" style="width:70px;" />';
			}
			else if ($status != '') {
				if ($refill->refillResponse->dateTime == '' || $refill->refillResponse->dateTime == '0000-00-00 00:00:00') {
					$refill->refillResponse->dateTime = date('Y-m-d H:i:s');
				}
				$status .= ' ('.date('m/d/Y',strtotime($refill->refillResponse->dateTime)).')';
			}
			$row['data'][] = $description;
			$row['data'][] = $status;
			//$row['data'][] = ($refill->dateStart == '0000-00-00 00:00:00')?'':$refill->dateStart;
			$row['data'][] = $refill->details;
			$row['data'][] = $refill->medicationId;
			if (strlen($refill->status) > 0) {
				$responded[] = $row;
			}
			else {
				$rows[] = $row;
			}
		}
		foreach ($responded as $respond) {
			$rows[] = $respond;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	protected function _generateRefillDetails($messageId) {
		// TODO: to be optimized
		$ret = array();
		$messaging = new Messaging();
		$messaging->messagingId = $messageId;
		$messaging->populate();
		if (!strlen($messaging->rawMessage) > 0) {
			return false;
		}
		$xml = new SimpleXMLElement($messaging->rawMessage);

		$xmlPharmacy = $xml->Body->RefillRequest->Pharmacy;
		$pharmacy = array();
		$pharmacy[] = (string)$xmlPharmacy->StoreName;
		$pharmacy[] = (string)$xmlPharmacy->Address->AddressLine1;
		if (strlen($xmlPharmacy->Address->AddressLine2) > 0) {
			$pharmacy[] = (string)$xmlPharmacy->Address->AddressLine2;
		}
		$pharmacy[] = (string)$xmlPharmacy->Address->City.', '.(string)$xmlPharmacy->Address->State.', '.(string)$xmlPharmacy->Address->ZipCode;
		$phones = array();
		foreach ($xmlPharmacy->PhoneNumbers->Phone as $key=>$phone) {
			$phones[] = (string)$phone->Number;
		}
		$pharmacy[] = implode(', ',$phones);
		$ret['pharmacy'] = implode('<br />',$pharmacy);

		$xmlPrescriber = $xml->Body->RefillRequest->Prescriber;
		$prescriber = array();
		$prescriber[] = (string)$xmlPrescriber->Name->LastName.', '.(string)(string)$xmlPrescriber->Name->FirstName;
		$prescriber[] = (string)$xmlPrescriber->Address->AddressLine1;
		if (strlen($xmlPrescriber->Address->AddressLine2) > 0) {
			$prescriber[] = (string)$xmlPrescriber->Address->AddressLine2;
		}
		$prescriber[] = (string)$xmlPrescriber->Address->City.', '.(string)$xmlPrescriber->Address->State.', '.(string)$xmlPrescriber->Address->ZipCode;
		$phones = array();
		foreach ($xmlPrescriber->PhoneNumbers->Phone as $key=>$phone) {
			$phones[] = (string)$phone->Number;
		}
		$prescriber[] = implode(', ',$phones);
		$ret['prescriber'] = implode('<br />',$prescriber);

		$xmlPatient = $xml->Body->RefillRequest->Patient;
		$patient = array();
		$patient[] = (string)$xmlPatient->Name->LastName.', '.(string)(string)$xmlPatient->Name->FirstName;
		$patient[] = (string)$xmlPatient->Address->AddressLine1;
		if (strlen($xmlPatient->Address->AddressLine2) > 0) {
			$patient[] = (string)$xmlPatient->Address->AddressLine2;
		}
		$patient[] = (string)$xmlPatient->Address->City.', '.(string)$xmlPatient->Address->State.', '.(string)$xmlPatient->Address->ZipCode;
		$phones = array();
		if (isset($xmlPatient->PhoneNumbers->Phone)) {
			foreach ($xmlPatient->PhoneNumbers->Phone as $key=>$phone) {
				$phones[] = (string)$phone->Number;
			}
		}
		$patient[] = implode(', ',$phones);
		$ret['patient'] = implode('<br />',$patient);

		$medication = array();
		$medicationDescription = '';
		if (isset($xml->Body->RefillRequest)) {
			$medication = Messaging::convertXMLMessage($xml->Body->RefillRequest->MedicationPrescribed,$medication,-1,' &nbsp; ');
			$medicationDescription = (string)$xml->Body->RefillRequest->MedicationPrescribed->DrugDescription;
		}
		$ret['medication'] = implode('<br />',$medication);
		$ret['medicationDescription'] = $medicationDescription;

		$sentTime = strtotime((string)$xml->Header->SentTime);
		if (!$sentTime > 0) {
			$sentTime = time();
		}
		$ret['dateRequested'] = date('m/d/Y h:iA',$sentTime);

		return $ret;
	}

	public function refillsContextMenuAction() {
		//placeholder function, template is xml and autorenders when called as medications.xml/refills-context-menu
	}

	public function processRefillResponseAction() {
		$medicationId = (int)$this->_getParam('medicationId');
		$response = $this->_getParam('response');

		$refillResponse = new MedicationRefillResponse();
		$refillResponse->medicationId = $medicationId;
		$ret = $refillResponse->send($response);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function detailsMedicationAction() {
		$medicationId = (int)$this->_getParam('medicationId');

		$name = Medication::ENUM_ADMIN_SCHED;
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		$enumerationsClosure = new EnumerationsClosure();
		$rowset = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$scheduleOptions = array();
		$adminSchedules = array();
		foreach ($rowset as $row) {
			$scheduleOptions[] = $row->key;
			$adminSchedules[$row->key] = $row->name;
		}
		$this->view->scheduleOptions = $scheduleOptions;
		$this->view->adminSchedules = $adminSchedules;

		$this->view->chBaseMed24Url = Zend_Registry::get('config')->healthcloud->CHMED->chBaseMed24Url;
		$this->view->chBaseMed24DetailUrl = Zend_Registry::get('config')->healthcloud->CHMED->chBaseMed24DetailUrl;
		$this->_form = new WebVista_Form(array('name' => 'new-medication'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . "medications.raw/process-add-medication");

		$this->_medication = new Medication();
		$this->_medication->medicationId = (int)$medicationId;
		$this->_medication->populate();
		if (!strlen($this->_medication->pharmacyId) > 0) {
			$this->_medication->pharmacyId = $patient->defaultPharmacyId;
		}
		if ($this->_medication->pharmacy->populate()) {
			$this->view->defaultPharmacy = $this->_medication->pharmacy;
		}

		$this->_form->loadORM($this->_medication,"Medication");
		$this->_form->setWindow('windowDetailsMedication');
		$this->view->form = $this->_form;
		$this->view->medication = $this->_medication;
		$this->view->quantityQualifiers = Medication::listQuantityQualifiersMapping();

		$medication = $this->_medication;
		$prescriberDetails = 'Prescribed on: '.$medication->datePrescribed.' by: '.$medication->provider->firstName.' '.$medication->provider->lastName.' '.$medication->provider->suffix;
		$this->view->prescriberDetails = $prescriberDetails;
		$signedDetails = '**Unsigned**';
		if ($this->_medication->eSignatureId > 0) {
			$signature = new ESignature();
			$signature->eSignatureId = $medication->eSignatureId;
			$signature->populate();
			$person = new Person();
			$person->personId = $signature->signingUserId;
			$person->populate();
			$signedDetails = 'Signed on: '.$signature->signedDateTime.' by: '.$person->firstName.' '.$person->lastName.' '.$person->suffix;
		}
		$this->view->signedDetails = $signedDetails;
		$transmitDetails = '';
		if ($medication->transmit == 'ePrescribe') {
			$transmitDetails = 'Transmitted on: ';
			if ($medication->dateTransmitted != '0000-00-00 00:00:00') {
				$transmitDetails .= $medication->dateTransmitted;
			}
			else {
				$transmitDetails .= 'pending';
			}
			$transmitDetails .= ' to pharmacy: '.$medication->pharmacy->StoreName;
		}
		else if ($medication->transmit == 'print') {
			$transmitDetails = 'Printed on: ';
			if ($medication->dateTransmitted != '0000-00-00 00:00:00') {
				$transmitDetails .= $medication->dateTransmitted;
			}
			else {
				$transmitDetails .= 'pending';
			}
		}
		$this->view->transmitDetails = $transmitDetails;
		$this->render('details-medication');
	}

	public function ajaxCheckPatientInfoAction() {
		$personId = (int)$this->_getParam('personId');
		$data = true;
		$patient = new Patient();
		$patient->personId = $personId;
		$patient->populate();
		$ret = $patient->ssCheck();
		if (isset($ret[0])) {
			$error = 'The following error';
			if (isset($ret[1])) {
				$error .= 's';
			}
			$error .= ' for patient detected:';
			foreach ($ret as $val) {
				$error .= "\n*) ".$val;
			}
			$data = array('error'=>$error);
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteMedicationAction() {
		$medicationId = (int)$this->_getParam('medicationId');
		$medication = new Medication();
		$medication->medicationId = (int)$medicationId;
		$medication->setPersistMode(WebVista_Model_ORM::DELETE);
		$eSignatureId = (int)ESignature::retrieveSignatureId('Medication',$medication->documentId);
		$data = true;
		if ($eSignatureId > 0) {
			$data = __('Failed to delete, selected medication is already signed.');
		}
		else {
			$medication->persist();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function filterAction() {
		$this->view->filters = $this->_session->filters;
		$this->render();
	}

	public function setSessionFilterAction() {
		$this->_session->filters = $this->_getParam('filters');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);
	}

	public function listMedicationUnrespondedRefillsAction() {
		$personId = (int)$this->_getParam('personId');
		$rows = array();

		$responded = array();
		$refillRequestIterator = array();
		if ($personId > 0) {
			$refillRequest = new MedicationRefillRequest();
			$refillRequestIterator = $refillRequest->getUnrespondedRefills($personId);
		}
		foreach ($refillRequestIterator as $refill) {
			$row = array();
			$row['id'] = $refill->messageId;
			$refillDetails = $this->_generateRefillDetails($refill->messageId);
			if ($refillDetails === false) continue;
			$row['data'][] = $refillDetails['medicationDescription'].' (Req. '.date('m/d/Y',strtotime($refillDetails['dateRequested'])).')';
			$row['data'][] = $refill->details;

			$objectClass = 'MedicationRefillRequest';
			$objectId = $row['id'];
			$userId = '';
			$controllerName = call_user_func($objectClass.'::getControllerName');
			$jumpLink = call_user_func_array($controllerName.'::buildJSJumpLink',array($objectId,$userId,$objectClass));
			$js = "function jumpLink{$objectClass}(objectId,patientId) {\n{$jumpLink}\n}";
			$row['userdata']['jumpLink'] = $js;
			$row['userdata']['others'] = $objectClass.':'.$objectId.':'.$personId;
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

}
