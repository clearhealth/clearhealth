<?php
/*****************************************************************************
*       DataIntegration.php
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


class DataIntegration extends WebVista_Model_ORM {

	protected $handlerType;

	public function __construct($handlerType = 0) {
		parent::__construct();
		$this->handlerType = (int)$handlerType;
	}

	public function getIterator($dbSelect = null) {
		if ($dbSelect === null) {
			$db = Zend_Registry::get('dbAdapter');
			$dbSelect = $db->select()
					->from($this->_table)
					->where('handlerType = ?',$this->handlerType)
					->order('name');
		}
		return parent::getIterator($dbSelect);
	}

	public static function handlerSSSourceData(Audit $audit) {
		$data = array();
		if ($audit->objectClass != 'ESignature') {
			return $data;
		}
		$eSignature = new ESignature();
		$eSignature->eSignatureId = $audit->objectId;
		$eSignature->populate();
		if ($eSignature->objectClass != 'Medication' || !strlen($eSignature->signature) > 0) {
			WebVista::log('esignature is not signed or medication');
			return $data;
		}

		// MEDICATION DATA
		$medication = new Medication();
		$medication->medicationId = (int)$eSignature->objectId;
		$medication->populate();
		if ($medication->transmit != 'ePrescribe' || $medication->isScheduled()) {
			WebVista::log('medication is either scheduled or not an eprescribe');
			return $data;
		}

		WebVista::log('generating source data');
		$data['_audit'] = $audit;
		$uuid = NSDR::create_guid();
		$data['messageId'] = str_replace('-','',$uuid);
		$data['prescriberOrderNumber'] = $medication->medicationId.'_'.$audit->auditId;
		$data['rxReferenceNumber'] = $medication->rxReferenceNumber;
		WebVista::log('messageId:['.$data['messageId'].'] prescriberOrderNumber:['.$data['prescriberOrderNumber'].'], rxReferenceNumber:['.$data['rxReferenceNumber'].']');

		$medData = array();
		$medData['description'] = $medication->description;
		$medData['strength'] = $medication->dose;
		$qualifiers = Medication::listQuantityQualifiersMapping();
		$medData['strengthUnits'] = $qualifiers[$medication->quantityQualifier];// temporarily set to the same with quantity
		$medData['quantity'] = $medication->quantity;
		$medData['quantityUnits'] = $qualifiers[$medication->quantityQualifier];
		$medData['daysSupply'] = $medication->daysSupply;
		$medData['directions'] = $medication->directions;
		$qualifier = 'R';
		if ($medication->prn) {
			$qualifier = 'PRN';
		}
		$medData['refills'] = $medication->refills;
		$medData['refillsUnits'] = $qualifier;
		$medData['substitutions'] = ($medication->substitution)?'0':'1';
		$writtenDate = date('Ymd',strtotime($medication->datePrescribed));
		if ($medication->datePrescribed == '0000-00-00 00:00:00') {
			$writtenDate = '';
		}
		$medData['writtenDate'] = $writtenDate;
		$medData['productCode'] = $medication->hipaaNDC;
		$medData['productQualifier'] = 'ND';
		$medData['dosageForm'] = DataTables::getDosageForm($medication->chmedDose);
		$medData['drugDBCode'] = $medication->pkey;
		$medData['drugDBQualifier'] = ''; //'pkey'; valid options: "E|G|FG|FS|MC|MD|MG|MM"
		$medData['note'] = $medication->comment;
		$data['Medication'] = $medData;
		WebVista::log('medication data: '.print_r($medData,true));

		// PHARMACY DATA
		$pharmacy = new Pharmacy();
		$pharmacy->pharmacyId = $medication->pharmacyId;
		$pharmacy->populate();

		$pharmacyData = array();
		$pharmacyData['NCPDPID'] = $pharmacy->NCPDPID;
		$pharmacyData['fileId'] = $pharmacy->pharmacyId;
		$pharmacyData['NPI'] = $pharmacy->NPI;
		$pharmacyData['storeName'] = $pharmacy->StoreName;
		$pharmacyData['storeNumber'] = $pharmacy->StoreNumber;
		$pharmacyData['email'] = $pharmacy->Email;
		$pharmacyData['twentyFourHourFlag'] = $pharmacy->TwentyFourHourFlag;
		$pharmacyData['crossStreet'] = $pharmacy->CrossStreet;
		$pharmacyData['addressLine1'] = $pharmacy->AddressLine1;
		$pharmacyData['addressLine2'] = $pharmacy->AddressLine2;
		$pharmacyData['city'] = $pharmacy->City;
		$pharmacyData['state'] = $pharmacy->State;
		$pharmacyData['zip'] = $pharmacy->Zip;
		$phones = array();
		$phones[] = array('number'=>$pharmacy->PhonePrimary,'type'=>'TE');
		$phones[] = array('number'=>$pharmacy->Fax,'type'=>'FX');
		$phones[] = array('number'=>$pharmacy->PhoneAlt1,'type'=>$pharmacy->PhoneAlt1Qualifier);
		$phones[] = array('number'=>$pharmacy->PhoneAlt2,'type'=>$pharmacy->PhoneAlt2Qualifier);
		$phones[] = array('number'=>$pharmacy->PhoneAlt3,'type'=>$pharmacy->PhoneAlt3Qualifier);
		$phones[] = array('number'=>$pharmacy->PhoneAlt4,'type'=>$pharmacy->PhoneAlt4Qualifier);
		$phones[] = array('number'=>$pharmacy->PhoneAlt5,'type'=>$pharmacy->PhoneAlt5Qualifier);
		$pharmacyData['phones'] = $phones;
		$data['Pharmacy'] = $pharmacyData;
		WebVista::log('pharmacy data: '.print_r($pharmacyData,true));

		// PRESCRIBER DATA
		$provider = new Provider();
		$provider->personId = $medication->prescriberPersonId;
		$provider->populate();
		$prescriberData = array();
		$prescriberData['DEANumber'] = $provider->deaNumber;
		// it has conflicts with DEANumber
		//$prescriberData['stateLicenseNumber'] = $provider->stateLicenseNumber;
		$prescriberData['fileId'] = $provider->personId;
		$prescriberData['clinicName'] = '';

		$identifierType = $provider->identifierType;
		if (strlen($identifierType) > 0) {
		//	$prescriberData[$identifierType] = $provider->identifier;
		}

		$prescriberData['lastName'] = $provider->person->lastName;
		$prescriberData['firstName'] = $provider->person->firstName;
		$prescriberData['middleName'] = $provider->person->middleName;
		$prescriberData['suffix'] = $provider->person->suffix;
		$prescriberData['prefix'] = '';
		$prescriberData['email'] = $provider->person->email;
		$prescriberData['specialtyCode'] = $provider->specialty;
		$specialtyQualifier = '';
		if (strlen($provider->specialty) > 0) {
			$specialtyQualifier = 'AM';
		}
		$prescriberData['specialtyQualifier'] = $specialtyQualifier;
		$building = Building::getBuildingDefaultLocation((int)$provider->personId);
		$ePrescriber = new EPrescriber();
		$ePrescriber->providerId = (int)$provider->personId;
		$ePrescriber->buildingId = (int)$building->buildingId;
		$ePrescriber->populateWithBuildingProvider();
		$prescriberData['SPI'] = $ePrescriber->SSID;
		$prescriberData['addressLine1'] = $building->line1;
		$prescriberData['addressLine2'] = $building->line2;
		$prescriberData['city'] = $building->city;
		$prescriberData['state'] = $building->state;
		$prescriberData['zip'] = $building->zipCode;
		$prescriberData['phones'] = $building->phoneNumbers;
		$data['Prescriber'] = $prescriberData;
		WebVista::log('prescriber data: '.print_r($prescriberData,true));

		// PATIENT DATA
		$patient = new Patient();
		$patient->personId = $medication->personId;
		$patient->populate();
		$patientData = array();
		$patientData['lastName'] = $patient->person->lastName;
		$patientData['firstName'] = $patient->person->firstName;
		$patientData['middleName'] = $patient->person->middleName;
		$patientData['suffix'] = $patient->person->suffix;
		$patientData['prefix'] = '';
		$patientData['email'] = $patient->person->email;
		$patientData['fileId'] = $patient->recordNumber;
		$patientData['medicareNumber'] = ''; // TODO: to be implemented

		$identifierType = $patient->identifierType;
		if (strlen($identifierType) > 0) {
			$patientData[$identifierType] = $patient->identifier;
		}

		$patientData['gender'] = $patient->person->getDisplayGender();
		$dateOfBirth = date('Ymd',strtotime($patient->person->dateOfBirth));
		if ($patient->person->dateOfBirth == '0000-00-00') {
			$dateOfBirth = '';
		}
		$patientData['dateOfBirth'] = $dateOfBirth;
		$address = new Address();
		$address->personId = $patient->personId;
		$addressIterator = $address->getIteratorByPersonId();
		foreach ($addressIterator as $address) {
			break; // retrieves the top address
		}
		$patientData['addressLine1'] = $address->line1;
		$patientData['addressLine2'] = $address->line2;
		$patientData['city'] = $address->city;
		$patientData['state'] = $address->state;
		$patientData['zip'] = $address->zipCode;
		$phoneNumber = new PhoneNumber();
		$phoneNumber->personId = $patient->personId;
		$patientData['phones'] = $phoneNumber->phoneNumbers;
		$data['Patient'] = $patientData;
		WebVista::log('patient data: '.print_r($patientData,true));

		// CHECK for attending/supervisor
		$attendingId = (int)TeamMember::getAttending($patient->teamId);
		$building = Building::getBuildingDefaultLocation($attendingId);
		$ePrescriber = new EPrescriber();
		$ePrescriber->providerId = $attendingId;
		$ePrescriber->buildingId = (int)$building->buildingId;
		$ePrescriber->populateWithBuildingProvider();
		if ($attendingId > 0 && strlen($ePrescriber->SSID) > 0) {
			// SUPERVISOR
			$provider = new Provider();
			$provider->personId = $attendingId;
			$provider->populate();
			$supervisorData = array();
			$supervisorData['DEANumber'] = $provider->deaNumber;
			$supervisorData['SPI'] = $ePrescriber->SSID;
			// it has conflicts with DEANumber
			//$supervisorData['stateLicenseNumber'] = $provider->stateLicenseNumber;
			$supervisorData['fileId'] = $provider->personId;
			$supervisorData['clinicName'] = '';

			$identifierType = $provider->identifierType;
			if (strlen($identifierType) > 0) {
			//	$prescriberData[$identifierType] = $provider->identifier;
			}
			$phoneNumber = new PhoneNumber();
			$phoneNumber->personId = $provider->personId;
			$supervisorData['phones'] = $phoneNumber->phoneNumbers;
	
			$supervisorData['lastName'] = $provider->person->lastName;
			$supervisorData['firstName'] = $provider->person->firstName;
			$supervisorData['middleName'] = $provider->person->middleName;
			$supervisorData['suffix'] = $provider->person->suffix;
			$supervisorData['prefix'] = '';
			$supervisorData['email'] = $provider->person->email;
			$supervisorData['specialtyCode'] = $provider->specialty;
			$specialtyQualifier = '';
			if (strlen($provider->specialty) > 0) {
				$specialtyQualifier = 'AM';
			}
			$supervisorData['specialtyQualifier'] = $specialtyQualifier;
			$supervisorData['addressLine1'] = $building->line1;
			$supervisorData['addressLine2'] = $building->line2;
			$supervisorData['city'] = $building->city;
			$supervisorData['state'] = $building->state;
			$supervisorData['zip'] = $building->zipCode;
			$supervisorData['phones'] = $building->phoneNumbers;
			$data['Supervisor'] = $supervisorData;
			WebVista::log('supervisor data: '.print_r($supervisorData,true));
		}

		return $data;
	}

	public static function handlerSSAct(Audit $audit,Array $sourceData) {
		if (!isset($sourceData['_audit']) || $audit->objectClass != 'ESignature') {
			WebVista::log('unable to send: _audit index does not exists or audit objectClass not ESignature ');
			return false;
		}
		$eSignature = new ESignature();
		$eSignature->eSignatureId = $audit->objectId;
		$eSignature->populate();
		if ($eSignature->objectClass != 'Medication' || !strlen($eSignature->signature) > 0) {
			WebVista::log('unable to send: signature is not signed or objectClass not Medication');
			return false;
		}

		$medication = new Medication();
		$medication->medicationId = (int)$eSignature->objectId;
		$medication->populate();
		$medication->dateTransmitted = date('Y-m-d H:i:s');
		$medication->persist();

		$patientInfo = $sourceData['Patient']['lastName'].', '.$sourceData['Patient']['firstName'].' '.$sourceData['Patient']['middleName'].' MRN#'.$sourceData['Patient']['fileId'];
		$patientInfo .= ' - '.$sourceData['Medication']['description'].' #'.date('m/d/Y',strtotime($sourceData['Medication']['writtenDate']));
		WebVista::log('patient info: '.$patientInfo);

		$audit = $sourceData['_audit'];
		unset($sourceData['_audit']);
		$messaging = new Messaging();
		$messaging->messagingId = $sourceData['messageId'];
		$messaging->messageType = 'NewRx';
		$messaging->populate();
		$messaging->objectId = (int)$eSignature->objectId;
		$messaging->objectClass = $audit->objectClass;
		$messaging->status = 'Sending';
		$messaging->note = 'Sending newRx ('.$patientInfo.')';
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->auditId = $audit->auditId; // this must be required for retransmission in case of error
		$messaging->persist();

		if ($messaging->resend && $messaging->pharmacyId  > 0) { // supersedes pharmacy from messaging
			$pharmacy = new Pharmacy();
			$pharmacy->pharmacyId = $messaging->pharmacyId;
			$pharmacy->populate();

			$pharmacyData = array();
			$pharmacyData['NCPDPID'] = $pharmacy->NCPDPID;
			$pharmacyData['StoreName'] = $pharmacy->StoreName;
			$pharmacyData['addressLine1'] = $pharmacy->AddressLine1;
			$pharmacyData['addressLine2'] = $pharmacy->AddressLine2;
			$pharmacyData['city'] = $pharmacy->City;
			$pharmacyData['state'] = $pharmacy->State;
			$pharmacyData['zip'] = $pharmacy->Zip;
			$pharmacyData['phone'] = $pharmacy->PhonePrimary;
			$pharmacyData['fax'] = '';
			$sourceData['Pharmacy'] = $pharmacyData;
		}

		$query = http_build_query(array('data'=>$sourceData));
		$ch = curl_init();
		$ePrescribeURL = Zend_Registry::get('config')->healthcloud->URL;
		$ePrescribeURL .= 'ss-manager.raw/new-rx?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		WebVista::log('SS URL: '.$ePrescribeURL);
		WebVista::log('URL Query: '.$query);
		curl_setopt($ch,CURLOPT_URL,$ePrescribeURL);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_USERPWD,'admin:ch3!');
		$output = curl_exec($ch);
		$error = '';
		$messaging->status = 'Sent';
		$messaging->note = 'newRx pending';
		$messaging->unresolved = 1;
		WebVista::log('RESPONSE: '.$output);
		if (!curl_errno($ch)) {
			try {
				$responseXml = new SimpleXMLElement($output);
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
				else if (isset($responseXml->status)) {
					$messaging->note = 'newRx awaiting confirmation';
					if ((string)$responseXml->status->code == '010') { // value 000 is for free standing error?
						$messaging->status .= ' and Verified';
						$messaging->note = 'newRx sent and verified';
						$messaging->unresolved = 0;
					}
				}
				else {
					$error = 'Unrecognized HealthCloud response: '.$output;
				}
				if (isset($responseXml->rawMessage)) {
					$messaging->rawMessage = base64_decode((string)$responseXml->rawMessage);
					$messaging->rawMessageResponse = base64_decode((string)$responseXml->rawMessageResponse);
				}
			}
			catch (Exception $e) {
				$error = __("There was an error connecting to HealthCloud to prescribe new medication. Please try again or contact the system administrator.");
				trigger_error("There was an error prescribing new medication, the response couldn't be parsed as XML: " . $output, E_USER_NOTICE);
			}
		}
		else {
			$error = __("There was an error connecting to HealthCloud to prescribe new medication. Please try again or contact the system administrator.");
			trigger_error("Curl error connecting to healthcare prescribed new medication: " . curl_error($ch),E_USER_NOTICE);
		}

		$messaging->note .= ' ('.$patientInfo.')';
		curl_close ($ch);
		$ret = true;
		if (strlen($error) > 0) {
			$messaging->status = 'Error';
			$patientInfo = $sourceData['Patient']['lastName'].', '.$sourceData['Patient']['firstName'].' '.$sourceData['Patient']['middleName'].' MRN#'.$sourceData['Patient']['fileId'];
			$providerInfo = $sourceData['Prescriber']['lastName'].', '.$sourceData['Prescriber']['firstName'].' '.$sourceData['Prescriber']['middleName'];
			$medicationInfo = $sourceData['Medication']['description'].' #'.date('m/d/Y',strtotime($sourceData['Medication']['writtenDate']));
			$messaging->note = $error.' Patient: '.$patientInfo.' Provider: '.$providerInfo.' Medication: '.$medicationInfo;
			$ret = false;
		}
		if ($messaging->resend) {
			$messaging->resend = 0;
		}
		$messaging->retries++;
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->persist();
		return $ret;
	}

	public static function handlereFaxSourceData(Audit $audit) {
		$data = array();
		if ($audit->objectClass != 'ESignature') {
			return $data;
		}
		$eSignature = new ESignature();
		$eSignature->eSignatureId = $audit->objectId;
		$eSignature->populate();
		if ($eSignature->objectClass != 'Medication') {
			return $data;
		}

		$data['_audit'] = $audit;
		$medication = new Medication();
		$medication->medicationId = $eSignature->objectId;
		$medication->populate();

		$data['transmissionId'] = (int)$medication->medicationId;
		$data['recipients'] = array();
		$patient = new Patient();
		$patient->personId = $medication->personId;
		$patient->populate();
		$pharmacyId = $patient->defaultPharmacyId;

		$provider = new Provider();
		$provider->personId = $medication->prescriberPersonId;
		$provider->populate();

		// recipients MUST be a pharmacy?
		$pharmacy = new Pharmacy();
		$pharmacy->pharmacyId = $pharmacyId;
		$pharmacy->populate();
		//$data['recipients'][] = array('fax'=>$pharmacy->Fax,'name'=>$pharmacy->StoreName,'company'=>$pharmacy->StoreName);
		// temporarily comment out the above recipient and use the hardcoded recipient
		$data['recipients'][] = array('fax'=>'6022976632','name'=>'Jay Walker','company'=>'ClearHealth Inc.');

		$prescription = new Prescription();
		$prescription->prescriberName = $provider->firstName.' '.$provider->lastName.' '.$provider->title;
		$prescription->prescriberStateLicenseNumber = $provider->stateLicenseNumber;
		$prescription->prescriberDeaNumber = $provider->deaNumber;

		// Practice Info
		$primaryPracticeId = $provider->primaryPracticeId;
		$practice = new Practice();
		$practice->id = $primaryPracticeId;
		$practice->populate();
		$address = $practice->primaryAddress;
		$prescription->practiceName = $practice->name;
		$prescription->practiceAddress = $address->line1.' '.$address->line2;
		$prescription->practiceCity = $address->city;
		$prescription->practiceState = $address->state;
		$prescription->practicePostalCode = $address->postalCode;

		$attachment = new Attachment();
		$attachment->attachmentReferenceId = $provider->personId;
		$attachment->populateWithAttachmentReferenceId();
		if ($attachment->attachmentId > 0) {
			$db = Zend_Registry::get('dbAdapter');
			$sqlSelect = $db->select()
					->from('attachmentBlobs')
					->where('attachmentId = ?',(int)$attachment->attachmentId);
			if ($row = $db->fetchRow($sqlSelect)) {
				$tmpFile = tempnam('/tmp','ch30_sig_');
				file_put_contents($tmpFile,$row['data']);
				$signatureFile = $tmpFile;
				$prescription->prescriberSignature = $signatureFile;
			}
		}

		$prescription->patientName = $patient->lastName.', '.$patient->firstName;
		$address = $patient->homeAddress;
		$prescription->patientAddress = $address->line1.' '.$address->line2;
		$prescription->patientCity = $address->city;
		$prescription->patientState = $address->state;
		$prescription->patientPostalCode = $address->postalCode;
		$prescription->patientDateOfBirth = date('m/d/Y',strtotime($patient->dateOfBirth));
		$prescription->medicationDatePrescribed = date('m/d/Y',strtotime($medication->datePrescribed));
		$prescription->medicationDescription = $medication->description;
		$prescription->medicationComment = $medication->comment;
		$prescription->medicationQuantity = $medication->quantity;
		$prescription->medicationRefills = $medication->refills;
		$prescription->medicationDirections = $medication->directions;
		$prescription->medicationSubstitution = $medication->substitution;
		$prescription->create();

		$filename = $prescription->imageFile;
		$fileType = pathinfo($filename,PATHINFO_EXTENSION);
		$data['files'] = array();
		$contents = file_get_contents($filename);
		unlink($filename);
		$data['files'][] = array('contents'=>base64_encode($contents),'type'=>$fileType);
		return $data;
	}

	public static function handlereFaxAct(Audit $audit,Array $sourceData) {
		if ($audit->objectClass != 'ESignature') {
			return false;
		}
		$eSignature = new ESignature();
		$eSignature->eSignatureId = $audit->objectId;
		$eSignature->populate();
		if ($eSignature->objectClass != 'Medication') {
			return false;
		}

		$medication = new Medication();
		$medication->medicationId = $eSignature->objectId;
		$medication->populate();

		$audit = $sourceData['_audit'];
		$messaging = new Messaging(Messaging::TYPE_OUTBOUND_FAX);
		$messaging->messagingId = (int)$sourceData['transmissionId'];
		$messaging->transmissionId = $messaging->messagingId;
		$messaging->populate();
		$messaging->objectId = $messaging->messagingId;
		$messaging->objectClass = $audit->objectClass;
		$messaging->status = 'Faxed';
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->auditId = $audit->auditId; // this must be required for retransmission in case of error
		$messaging->persist();

		$efax = new eFaxOutbound();
		$url = Zend_Registry::get('config')->healthcloud->eFax->outboundUrl;
		$url .= '?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		$efax->setUrl($url);

		$efax->setTransmissionId($sourceData['transmissionId']);
		$efax->setNoDuplicate(eFaxOutbound::NO_DUPLICATE_ENABLE);
		$efax->setDispositionMethod('POST');
		// use the default disposition URL
		$dispositionUrl = Zend_Registry::get('config')->healthcloud->eFax->dispositionUrl;
		$efax->setDispositionUrl($dispositionUrl);

		//$efax->setDispositionMethod('EMAIL');
		//$efax->addDispositionEmail('Arthur Layese','arthur@layese.com');
		foreach ($sourceData['recipients'] as $recipient) {
			if ($messaging->resend && strlen($messaging->faxNumber) > 9) { // supersedes fax number from messaging
				$recipient['fax'] = $messaging->faxNumber;
			}
			$efax->addRecipient($recipient['fax'],$recipient['name'],$recipient['company']);
		}
		foreach ($sourceData['files'] as $file) {
			$efax->addFile($file['contents'],$file['type']);
		}

		$ret = $efax->send();
		if (!$ret) {
			$messaging->status = 'Fax Error';
			$messaging->note = implode(PHP_EOL,$efax->getErrors());
		}
		else {
			$messaging->docid = $efax->getDocId();
			$messaging->status = 'Fax Sent';
			$messaging->note = '';
		}
		if ($messaging->resend) {
			$messaging->resend = 0;
		}
		$messaging->retries++;
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->persist();
		return true;
	}

	public function getNormalizedName() {
		return Handler::normalizeHandlerName($this->name);
	}

}
