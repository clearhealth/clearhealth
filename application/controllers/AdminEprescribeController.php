<?php
/*****************************************************************************
*       AdminEprescribeController.php
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


class AdminEprescribeController extends WebVista_Controller_Action {

	protected $_form;
	protected $_provider;
	
	public function editAction() {
		$personId = (int)$this->_getParam('personId');
        	if (isset($this->_session->messages)) {
        	    $this->view->messages = $this->_session->messages;
        	}
		$this->_form = new WebVista_Form(array('name' => 'eprescribe-detail'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . "admin-eprescribe.raw/edit-process");
		$this->_provider = new Provider();
                $this->_provider->person_id = $personId;
                $this->_provider->populate();
		if (!strtotime($this->_provider->dateActiveStart) > 0) {
			$this->_provider->dateActiveStart = date('Y-m-d');
			$this->_provider->dateActiveEnd = date('Y-m-d',strtotime('+1 year'));
		}
		$serviceLevel = (int)$this->_provider->serviceLevel;
		$this->_provider->serviceLevel = $serviceLevel;
                $this->_form->loadORM($this->_provider, "Provider");
                //var_dump($this->_form);
                $this->view->form = $this->_form;
                $this->view->provider = $this->_provider;
		$this->view->serviceLevels = Provider::getServiceLevelOptions();
                $this->render('edit');
	}

	public function editProcessAction() {
		$params = $this->_getParam('provider');
		$personId = (int)$params['personId'];
		$data = true;
		if ($personId > 0) {
			$provider = new Provider();
			$provider->personId = $personId;
			$provider->populate();
			$provider->populateWithArray($params);
			$provider->persist();
			$this->_setParam('personId',$personId);
			$this->_setParam('serviceLevel',$provider->serviceLevel);
			$this->addProcessAction();
			$data = array('message'=>$this->view->message,'prescriberSPI'=>$this->view->prescriberSPI,'error'=>$this->view->error);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	function addEprescribingRecordAction() {
		$personId = (int)$this->_getParam("personId");
	}


	public function addProcessAction() {
		$dateActiveStart = strtotime($this->_getParam('dateActiveStart'));
		$dateActiveEnd = strtotime($this->_getParam('dateActiveEnd'));
		$serviceLevel = (int)$this->_getParam('serviceLevel');

		$personId = (int)$this->_getParam('personId');
		$provider = new Provider();
		$provider->person_id = $personId;
		$provider->populate();
		$provider->serviceLevel = $serviceLevel;

		if ($dateActiveStart > 0 && $dateActiveEnd > 0) {
			$provider->dateActiveStart = date('Y-m-d H:i:s',$dateActiveStart);
			$provider->dateActiveEnd = date('Y-m-d H:i:s',$dateActiveEnd);
		}

		$person = new Person();
		$person->person_id = $personId;
		$person->populate();
		$address = new Address();
		$address->person_id = $personId;
		$address->populateWithPersonId($personId);

		$phoneNumber = new PhoneNumber();
		$phoneNumber->person_id = $personId;
		$phoneNumber->populateWithType(4);

		$practice = new Practice();
		$practice->practiceId = $person->primaryPracticeId;
		$practice->populate();

		$data = array();
		$data['deaNumber'] = $provider->deaNumber;
		$data['stateLicenseNumber'] = $provider->stateLicenseNumber;
		//$data['portalId'] = Zend_Registry::get('config')->sureScripts->portalId;
		//$data['accountId'] = Zend_Registry::get('config')->sureScripts->accountId;
		$data['clinicName'] = ''.$practice->name;
		$data['lastName'] = $person->last_name;
		$data['firstName'] = $person->first_name;
		$address = new Address();
		$address->personId = $provider->personId;
		$address->populateWithType(4);
		$data['addressLine1'] = $address->line1;
		$data['addressLine2'] = $address->line2;
		$data['addressCity'] = $address->city;
		$data['addressState'] = $address->state;
		$data['addressZipCode'] = $address->zipCode;
		$data['email'] = $person->email;

		$phoneNumber = new PhoneNumber();
		$phoneNumber->personId = $provider->personId;
		/*
		$phoneNumberIterator = $phoneNumber->getIteratorByPatientId();
		$phones = array();
		foreach ($phoneNumberIterator as $number) {
			if (!strlen($number->number) > 0) continue;
			// SS Type options: BN - Beeper, CP - Cellular, FX - Fax, HP - Home, NP - Night, TE – Telephone*, WP – Work
			$type = '';
			switch ($number->type) {
				case PhoneNumber::TYPE_HOME:
					$type = 'HP'; 
				case PhoneNumber::TYPE_WORK:
					$type = 'WP';
					break;
				case PhoneNumber::TYPE_MOBILE:
					$type = 'CP';
					break;
				case PhoneNumber::TYPE_FAX:
					$type = 'FX';
					break;
				case PhoneNumber::TYPE_EMERGENCY:
				case PhoneNumber::TYPE_EMPLOYER:
				case PhoneNumber::TYPE_BILLING:
					$type = 'TE';
					break;
				default:
					continue;
			}
			$phones[$type] = array('number'=>$number->number,'type'=>$type);
		}
		$te = null;
		if (isset($phones['TE'])) {
			$te = $phones['TE'];
			unset($phones['TE']);
		}
		$fx = null;
		if (isset($phones['FX'])) {
			$fx = $phones['FX'];
			unset($phones['FX']);
		}
		if ($te === null) {
			if (count($phones) > 0) {
				$te = array_unshift($phones);
			}
			else if ($fx !== null) {
				$te = $fx;
			}
		}
		if ($fx === null) {
			if (count($phones) > 0) {
				$fx = array_unshift($phones);
			}
			else if ($te !== null) {
				$fx = $te;
			}
		}

		$data['phones'] = array();
		if ($te !== null) {
			$data['phones'][] = $te;
		}
		if ($fx !== null) {
			$data['phones'][] = $fx;
		}
		foreach ($phones as $p) {
			$data['phones'][] = $p;
		}
		*/
		$data['phones'] = $phoneNumber->phoneNumbers;

		/*$phoneNumbers = $phoneNumber->phoneNumbers;
		$fax = '';
		if (isset($phoneNumbers['FAX'])) {
			$fax = $phoneNumbers['FAX'];
			unset($phoneNumbers['FAX']);
		}
		$phone = $fax;
		if (count($phoneNumbers) > 0) {
			$phone = array_pop($phoneNumbers);
		}
		$data['phoneNumber'] = $phone;
		$data['faxNumber'] = $fax;*/

		$data['specialtyCode'] = $provider->specialty;
		$specialtyQualifier = '';
		if (strlen($provider->specialty) > 0) {
			$specialtyQualifier = 'AM';
		}
		$data['specialtyQualifier'] = $specialtyQualifier;
		$data['serviceLevel'] = $provider->serviceLevel;

		$now = strtotime('now');
		$days30 = strtotime('+30 days',$now);
		$activeStartTime = gmdate("Y-m-d\TH:i:s.0",$now).'Z';
		$activeEndTime = gmdate("Y-m-d\TH:i:s.0",$days30).'Z';
		$data['activeStartTime'] = $provider->dateActiveStartZ;
		$data['activeEndTime'] = $provider->dateActiveEndZ;
		$dateActiveEnd = strtotime(date('Y-m-d',strtotime($provider->dateActiveEndZ)));
		if ($dateActiveEnd <= strtotime(date('Y-m-d'))) {
			// to disable a prescriber ActiveEndTime must be set to current date and ServiceLevel must be set to zero.
			$data['activeEndTime'] = date('Y-m-d');
			$data['serviceLevel'] = 0;
			$provider->serviceLevel = 0;
		}
		$provider->persist();
		$identifierType = $provider->identifierType;
		if (strlen($identifierType) > 0) {
			$data[$identifierType] = $provider->identifier;
		}
/*
		foreach ($data as $k=>$v) {
			if (!strlen(trim($v)) > 0) {
				$tmp = ' ';
				if ($k == 'addressState') {
					$tmp = 'AZ';
				}
				if ($k == 'addressZipCode') {
					$tmp = '12345';
				}
				$data[$k] = $tmp;
			}
		}*/

		$messaging = new Messaging();
		//$messaging->messagingId = '';
		$type = 'add';
		$messaging->messageType = 'AddPrescriber';
		if (strlen($provider->sureScriptsSPI) > 0) {
			$messaging->messageType = 'UpdatePrescriber';
			$data['SPI'] = $provider->sureScriptsSPI;
			$type = 'update';
		}
		$messaging->populate();
		//$messaging->objectId = '';
		//$messaging->objectClass = '';
		$messaging->status = 'Sending';
		$messaging->note = 'Sending prescriber data';
		$messaging->dateStatus = date('Y-m-d H:i:s');
		//$messaging->auditId = '';
		$messaging->persist();

		$query = http_build_query(array('type'=>$type,'data'=>$data));
		$ch = curl_init();
		$ePrescribeURL = Zend_Registry::get('config')->healthcloud->URL;
		$ePrescribeURL .= 'ss-manager.raw/edit-prescriber?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		curl_setopt($ch,CURLOPT_URL,$ePrescribeURL);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); 
		$output = curl_exec($ch);
		trigger_error('OUTPUT: '.$output,E_USER_NOTICE);
		$error = "";
		$prescriberSPI = '';
		$messaging->status = 'Sent';
		$messaging->note = 'Prescriber data sent';
		if (!curl_errno($ch)) {
			try {
				$responseXml = simplexml_load_string($output);
				if (isset($responseXml->error)) {
					$errorCode = (string)$responseXml->error->code;
					$errorMsg = (string)$responseXml->error->message;
					if (isset($responseXml->error->errorCode)) {
						$errorCode = (string)$responseXml->error->errorCode;
					}
					if (isset($responseXml->error->errorMsg)) {
						$errorMsg = (string)$responseXml->error->errorMsg;
					}
					$error = $errorMsg;
					trigger_error('There was an error enabling an ePresciber, Error code: '.$errorCode.' Error Message: '.$errorMsg,E_USER_NOTICE);
				}
				elseif (isset($responseXml->data)) {
					$xml = new SimpleXMLElement($responseXml->data);
					$prescriber = $xml->AddPrescriberResponse->Prescriber;
					if (isset($xml->AddPrescriberLocationResponse)) {
						$prescriber = $xml->AddPrescriberLocationResponse->Prescriber;
					}
					$prescriberSPI = (string)$prescriber->Identification->SPI;
				}
				if (isset($responseXml->rawMessage)) {
					$messaging->rawMessage = base64_decode((string)$responseXml->rawMessage);
					$messaging->rawMessageResponse = base64_decode((string)$responseXml->rawMessageResponse);
				}
			}
			catch (Exception $e) {
				$error = __("There was an error connecting to HealthCloud to enable ePrescribing for this provider. Please try again or contact the system administrator.");
					trigger_error("There was an error enabling an ePresciber, the response couldn't be parsed as XML: " . $output, E_USER_NOTICE);
			}
		}
		else {
			$error = __("There was an error connecting to HealthCloud to enable ePrescribing for this provider. Please try again or contact the system administrator.");
			trigger_error("Curl error connecting to healthcare enabled an ePrescribe record: " . curl_error($ch),E_USER_NOTICE);
		}
		curl_close ($ch);
		if (strlen($error) > 0) {
			$messaging->status = 'Error';
			$messaging->note = $error;
			$ret = false;
		}
		if ($messaging->resend) {
			$messaging->resend = 0;
		}
		$messaging->retries++;
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$this->view->error = $error;
		if (strlen($error) > 0) {
			//$this->getResponse()->setHttpResponseCode(500);
			$json->direct(array('error'=>$error));
			return;
		}
		if ($type == 'add') {
			$provider->sureScriptsSPI = trim($prescriberSPI);
			$provider->persist();
		}
		else {
			$prescriberSPI = $provider->sureScriptsSPI;
		}
		$this->view->message = "ePrescribing Information Saved for Provider, assigned SPI Number: " . $prescriberSPI;
		$this->view->prescriberSPI = $prescriberSPI;
		$json->direct(array('message'=>$this->view->message,'prescriberSPI'=>$prescriberSPI,'error'=>$error));
	}

	public function newRxAction() {
		$medicationId = 1077476;
		$data = array();
		$medication = new Medication();
		$medication->medicationId = $medicationId;
		$medication->populate();
		$data['PrescriberOrderNumber'] = $medication->medicationId;

		$medData = array();
		$medData['DrugDescription'] = $medication->description;
		$medData['Strength'] = $medication->strength;
		$medData['StrengthUnits'] = $medication->unit;
		$medData['Quantity'] = $medication->quantity;
		$medData['Directions'] = $medication->directions;
		$medData['Refills'] = $medication->refills;
		$medData['Substitutions'] = $medication->substitution;
		$medData['WrittenDate'] = date('Ymd',strtotime($medication->datePrescribed));
		$data['medication'] = $medData;

		$pharmacy = new Pharmacy();
		$pharmacy->pharmacyId = $medication->pharmacyId;
		$pharmacy->populate();

		$pharmacyData = array();
		$pharmacyData['NCPDPID'] = $pharmacy->NCPDPID;
		$pharmacyData['StoreName'] = $pharmacy->StoreName;
		$pharmacyData['AddressLine1'] = $pharmacy->AddressLine1.' '.$pharmacy->AddressLine2;
		$pharmacyData['City'] = $pharmacy->City;
		$pharmacyData['State'] = $pharmacy->State;
		$pharmacyData['ZipCode'] = $pharmacy->Zip;
		$pharmacyData['PhoneNumber'] = $pharmacy->PhonePrimary;
		$data['pharmacy'] = $pharmacyData;

		$provider = new Provider();
		$provider->personId = $medication->prescriberPersonId;
		$provider->populate();
		$prescriberData = array();
		$prescriberData['DEANumber'] = $provider->deaNumber;
		$prescriberData['SPI'] = $provider->sureScriptsSPI;
		$prescriberData['ClinicName'] = '';
		$prescriberData['LastName'] = $provider->person->lastName;
		$prescriberData['FirstName'] = $provider->person->firstName;
		$prescriberData['Suffix'] = '';
		$address = new Address();
		$address->personId = $provider->personId;
		$address->populateWithPersonId();
		$prescriberData['AddressLine1'] = $address->line1.' '.$address->line2;
		$prescriberData['City'] = $address->city;
		$prescriberData['State'] = 'AZ'; //$address->state;
		$prescriberData['ZipCode'] = $address->zipCode;
		$phoneNumber = new PhoneNumber();
		$phoneNumber->personId = $provider->personId;
		$phoneNumber->populateWithPersonId();
		$prescriberData['PhoneNumber'] = $phoneNumber->number;
		$data['prescriber'] = $prescriberData;

		$patient = new Patient();
		$patient->personId = $medication->personId;
		$patient->populate();
		$patientData = array();
		$patientData['LastName'] = $patient->person->lastName;
		$patientData['FirstName'] = $patient->person->firstName;

		$patientData['Gender'] = $patient->person->gender;
		$patientData['DateOfBirth'] = date('Ymd',strtotime($patient->person->dateOfBirth));
		$address = new Address();
		$address->personId = $patient->personId;
		$address->populateWithPersonId();
		$patientData['AddressLine1'] = $address->line1.' '.$address->line2;
		$patientData['City'] = $address->city;
		$patientData['State'] = 'AZ'; //$address->state;
		$patientData['ZipCode'] = $address->zipCode;
		$phoneNumber = new PhoneNumber();
		$phoneNumber->personId = $patient->personId;
		$phoneNumber->populateWithPersonId();
		$patientData['PhoneNumber'] = $phoneNumber->number;
		$data['patient'] = $patientData;

		$postFields = array();
		foreach ($data as $type=>$row) {
			if (is_array($row)) {
				foreach ($row as $field=>$value) {
					$key = $type.'['.$field.']';
					$postFields[$key] = $value;
				}
			}
			else {
				$postFields[$type] = $row;
			}
		}

		$ch = curl_init();
		$ePrescribeURL = Zend_Registry::get('config')->healthcloud->URL;
		$ePrescribeURL .= 'ss-manager.raw/new-rx?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		curl_setopt($ch,CURLOPT_URL,$ePrescribeURL);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postFields);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); 
		curl_setopt($ch,CURLOPT_USERPWD,'admin:ch3!');
		$output = curl_exec($ch);
		trigger_error('OUTPUT: '.$output,E_USER_NOTICE);
		$error = "";
		if (!curl_errno($ch)) {
			try {
				$responseXml = simplexml_load_string($output);
				if (isset($responseXml->error)) {
					$errorCode = (string)$responseXml->error->code;
					$errorMsg = (string)$responseXml->error->message;
					if (isset($responseXml->error->errorCode)) {
						$errorCode = (string)$responseXml->error->errorCode;
					}
					if (isset($responseXml->error->errorMsg)) {
						$errorMsg = (string)$responseXml->error->errorMsg;
					}
					$error = $errorMsg;
					trigger_error('There was an error prescribing new medication, Error code: '.$errorCode.' Error Message: '.$errorMsg,E_USER_NOTICE);
				}
			}
			catch (Exception $e) {
				$error = __("There was an error connecting to HealthCloud to prescribe new medication. Please try again or contact the system administrator.");
				trigger_error("There was an error prescribeing new medication, the response couldn't be parsed as XML: " . $output, E_USER_NOTICE);
			}
		}
		else {
			$error = __("There was an error connecting to HealthCloud to prescribe new medication. Please try again or contact the system administrator.");
			trigger_error("Curl error connecting to healthcare prescribed new medication: " . curl_error($ch),E_USER_NOTICE);
		}
		curl_close ($ch);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		if (strlen($error) > 0) {
			$this->getResponse()->setHttpResponseCode(500);
			$json->direct(array('error'=>$error));
			return;
		}
		$json->direct(true);
	}

	public function editPharmacyAction() {
		$pharmacyId = $this->_getParam('pharmacyId');
		$pharmacy = new Pharmacy();
		$pharmacy->pharmacyId = $pharmacyId;
		$pharmacy->populate();
		if ($pharmacy->RecordChange == '') {
			$pharmacy->RecordChange = 'U';
		}
		$this->view->pharmacy = $pharmacy;
		$this->render('edit-pharmacy');
	}

	public function processSendPharmacyAction() {
		$pharmacyId = $this->_getParam('pharmacyId');
		$recordChange = $this->_getParam('recordChange');
		$pharmacy = new Pharmacy();
		$pharmacy->pharmacyId = $pharmacyId;
		$pharmacy->populate();
		$pharmacy->RecordChange = $recordChange;
		$ret = $pharmacy->sendPharmacy();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

}
