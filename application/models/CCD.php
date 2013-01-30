<?php
/*****************************************************************************
*       CCD.php
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


class CCD {

	protected $_xml = null;
	protected $_patientId = 0;
	public $patient = null;
	protected $_userId = 0;
	public $user = null;
	public $building = null;
	public $visit = null;
	protected $_title = '';
	public $problemLists = array();
	public $performers = array();
	public $labResults = array();

	public function __construct($withXSLT=false) {
		$xmlStr = '<?xml version="1.0" encoding="UTF-8"?>';
		if ($withXSLT) {
			$baseUrl = Zend_Registry::get('baseUrl');
			$xmlStr .= '<?xml-stylesheet type="text/xsl" href="'.$baseUrl.'ccd.raw/xsl"?>';
		}
		$xmlStr .= '<ClinicalDocument xmlns="urn:hl7-org:v3" xmlns:sdtc="urn:hl7-org:sdtc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:hl7-org:v3 http://xreg2.nist.gov:8080/hitspValidation/schema/cdar2c32/infrastructure/cda/C32_CDA.xsd"/>';
		$this->_xml = new SimpleXMLElement($xmlStr);
	}

	public function getVisit() {
		return $this->visit;
	}

	public function setFiltersDateRange(&$filters) {
		if ($this->visit !== null) {
			$dateOfTreatment = strtotime($this->visit->dateOfTreatment);
			$dateRange = date('Y-m-d',strtotime('-30 days',$dateOfTreatment));
			$dateRange .= ';'.date('Y-m-d',$dateOfTreatment);
			$filters['dateRange'] = $dateRange;
		}
	}

	public static function formatDate($date = null) {
		if ($date === null) {
			$date = date('Y-m-d H:i:s');
		}
		$time = strtotime($date);
		$timezone = date('Z',$time);
		$hour = 60 * 60;
		$tz = $timezone / $hour;
		$time = date('YmdHis',$time).sprintf('%03d00',$tz);
		return $time;
	}

	public function populate($patientId,$userId,$visitId) {
		$this->_patientId = (int)$patientId;
		$patient = new Patient();
		$patient->personId = $this->_patientId;
		$patient->populate();
		$this->_title = $patient->displayName.' Healthcare Record';
		$this->patient = $patient;
		$this->_userId = (int)$userId;
		$user = new User();
		$user->personId = $this->_userId;
		$user->populate();
		$this->user = $user;
		$visit = new Visit();
		$visit->visitId = (int)$visitId;
		if ($visit->visitId > 0 && $visit->populate()) $this->visit = $visit;
		$this->building = Building::getBuildingDefaultLocation($this->user->personId);

		$performers = array();
		$problemList = new ProblemList();
		$filters = array();
		$filters['personId'] = $this->_patientId;
		$this->setFiltersDateRange($filters);
		$problems = array();
		$problemListIterator = new ProblemListIterator();
		$problemListIterator->setFilters($filters);
		foreach ($problemListIterator as $problem) {
			$problems[] = $problem;
			$providerId = (int)$problem->providerId;
			if (!isset($performers[$providerId])) {
				$provider = new Provider();
				$provider->personId = $providerId;
				$provider->populate();
				$performers[$providerId] = $provider;
			}
		}
		$this->problemLists = $problems;

		unset($filters['personId']);
		$filters['patientId'] = $this->_patientId;

		$labResults = array();
		$labTests = array();
		$labOrderTests = array();
		$labsIterator = new LabsIterator();
		$labsIterator->setFilters($filters);
		foreach ($labsIterator as $lab) {
			// get the lab order
			$labTestId = (int)$lab->labTestId;
			if (!isset($labTests[$labTestId])) {
				$labTest = new LabTest();
				$labTest->labTestId = (int)$lab->labTestId;
				$labTest->populate();
				$labTests[$labTestId] = $labTest;
			}
			$labTest = $labTests[$labTestId];
			$orderId = (int)$labTest->labOrderId;
			if (!isset($labOrderTests[$orderId])) {
				$orderLabTest = new OrderLabTest();
				$orderLabTest->orderId = $orderId;
				$orderLabTest->populate();
				$labOrderTests[$orderId] = $orderLabTest;
			}
			$orderLabTest = $labOrderTests[$orderId];
			$providerId = (int)$orderLabTest->order->providerId;
			if (!isset($performers[$providerId])) {
				$provider = new Provider();
				$provider->personId = $providerId;
				$provider->populate();
				$performers[$providerId] = $provider;
			}
			if (!isset($labResults[$orderId])) {
				$labResults[$orderId] = array();
				$labResults[$orderId]['results'] = array();
				$labResults[$orderId]['labTest'] = $labTest;
				$labResults[$orderId]['orderLabTest'] = $orderLabTest;
			}
			$labResults[$orderId]['results'][] = $lab;
		}
		$this->labResults = $labResults;

		$this->performers = $performers;
		$this->populateHeader($this->_xml);
		$this->populateBody($this->_xml);
		return $this->_xml->asXML();
	}

	public function populateHeader(SimpleXMLElement $xml) {
		$patientName = array();
		$patientName['given'] = $this->patient->person->firstName;
		$patientName['family'] = $this->patient->person->lastName;
		$patientName['suffix'] = $this->patient->person->suffix;

		$providerName = array();
		$providerName['prefix'] = $this->user->person->prefix;
		$providerName['given'] = $this->user->person->firstName;
		$providerName['family'] = $this->user->person->lastName;
		$building = $this->building;
		$buildingName = $building->displayName;

		$realmCode = $xml->addChild('realmCode');
		$realmCode->addAttribute('code','US');
		$typeId = $xml->addChild('typeId');
		$typeId->addAttribute('root','2.16.840.1.113883.1.3');
		$typeId->addAttribute('extension','POCD_HD000040');

		$templateId = $xml->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.3.27.1776');
		$templateId->addAttribute('assigningAuthorityName','CDA/R2');
		$templateId = $xml->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.10.20.3');
		$templateId->addAttribute('assigningAuthorityName','HL7/CDT Header');
		$templateId = $xml->addChild('templateId');
		$templateId->addAttribute('root','1.3.6.1.4.1.19376.1.5.3.1.1.1');
		$templateId->addAttribute('assigningAuthorityName','IHE/PCC');
		$templateId = $xml->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.3.88.11.32.1');
		$templateId->addAttribute('assigningAuthorityName','HITSP/C32');
		$id = $xml->addChild('id');
		$id->addAttribute('root','2.16.840.1.113883.3.72');
		$id->addAttribute('extension','HITSP_C32v2.5');
		$id->addAttribute('assigningAuthorityName','ClearHealth');
		$code = $xml->addChild('code');
		$code->addAttribute('code','34133-9');
		$displayName = 'Summarization of episode note';
		$code->addAttribute('displayName',$displayName);
		$code->addAttribute('codeSystem','2.16.840.1.113883.6.1');
		$code->addAttribute('codeSystemName','LOINC');
		$xml->addChild('title',html_convert_entities($this->_title));
		$effectiveTime = $xml->addChild('effectiveTime');
		$dateEffective = self::formatDate();
		$effectiveTime->addAttribute('value',$dateEffective);
		$confidentialityCode = $xml->addChild('confidentialityCode');
		$confidentialityCode->addAttribute('code','N');
		//$confidentialityCode->addAttribute('codeSystem','2.16.840.1.113883.5.25');
		$languageCode = $xml->addChild('languageCode');
		$languageCode->addAttribute('code','en-US');

		// RECORD TARGET
		$recordTarget = $xml->addChild('recordTarget');
		$patientRole = $recordTarget->addChild('patientRole');
		$id = $patientRole->addChild('id');
		//$id->addAttribute('root','CLINICID');
		$id->addAttribute('root','MRN');
		//$id->addAttribute('extension','PatientID');
		$id->addAttribute('extension',html_convert_entities($this->patient->recordNumber));
		// Address
		$address = new Address();
		$address->personId = $this->_patientId;
		$addressIterator = $address->getIteratorByPersonId();
		foreach ($addressIterator as $address) {
			break; // retrieves the top address
		}
		$addr = $patientRole->addChild('addr');
		if ($address->addressId > 0) {
			$addr->addAttribute('use','HP');
			$addr->addChild('streetAddressLine',html_convert_entities((strlen($address->line2) > 0)?$address->line1.' '.$address->line2:$address->line1));
			$addr->addChild('city',html_convert_entities($address->city));
			$addr->addChild('state',html_convert_entities($address->state));
			$addr->addChild('postalCode',html_convert_entities($address->zipCode));
		}
		// Telecom
		$phone = null;
		$phoneNumber = new PhoneNumber();
		$phoneNumber->personId = $this->_patientId;
		foreach ($phoneNumber->getPhoneNumbers(false) as $phone) {
			break; // retrieves the top phone
		}
		$telecom = $patientRole->addChild('telecom');
		if ($phone && strlen($phone['number']) > 0) {
			$telecom->addAttribute('use','HP');
			$telecom->addAttribute('value','tel:'.html_convert_entities($phone['number']));
		}
		// Patient
		$patient = $patientRole->addChild('patient');
		$name = $patient->addChild('name');
		$name->addChild('given',html_convert_entities($patientName['given']));
		$name->addChild('family',html_convert_entities($patientName['family']));
		$name->addChild('suffix',html_convert_entities($patientName['suffix']));

		$genderCode = $patient->addChild('administrativeGenderCode');
		$genderCode->addAttribute('code',html_convert_entities($this->patient->person->gender));
		$genderCode->addAttribute('displayName',html_convert_entities($this->patient->person->displayGender));
		$genderCode->addAttribute('codeSystem','2.16.840.1.113883.5.1');
		$genderCode->addAttribute('codeSystemName','HL7 AdministrativeGender');
		$birthTime = $patient->addChild('birthTime');
		$birthTime->addAttribute('value',date('Ymd',strtotime($this->patient->person->dateOfBirth)));
		/*$maritalStatusCode = $patient->addChild('maritalStatusCode');
		$maritalStatusCode->addAttribute('code','');
		$maritalStatusCode->addAttribute('displayName','');
		$maritalStatusCode->addAttribute('codeSystem','2.16.840.1.113883.5.2');
		$maritalStatusCode->addAttribute('codeSystemName','HL7 Marital status');*/

		/*$languageCommunication = $patient->addChild('languageCommunication');
		$templateId = $languageCommunication->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.3.88.11.83.2');
		$templateId->addAttribute('assigningAuthorityName','HITSP/C83');
		$templateId = $languageCommunication->addChild('templateId');
		$templateId->addAttribute('root','1.3.6.1.4.1.19376.1.5.3.1.2.1');
		$templateId->addAttribute('assigningAuthorityName','IHE/PCC');
		$languageCode = $languageCommunication->addChild('languageCode');
		$languageCode->addAttribute('code','en-US');*/

		// AUTHOR
		$author = $xml->addChild('author');
		$time = $author->addChild('time');
		$timeValue = self::formatDate();
		$time->addAttribute('value',$timeValue);
		$assignedAuthor = $author->addChild('assignedAuthor');
		$id = $assignedAuthor->addChild('id');
		$id->addAttribute('root','20cf14fb-b65c-4c8c-a54d-b0cca834c18c');
		$addr = $assignedAuthor->addChild('addr');
		$addr->addAttribute('use','HP');
		$addr->addChild('streetAddressLine',html_convert_entities((strlen($building->line2) > 0)?$building->line1.' '.$building->line2:$building->line1));
		$addr->addChild('city',html_convert_entities($building->city));
		$addr->addChild('state',html_convert_entities($building->state));
		$addr->addChild('postalCode',html_convert_entities($building->zipCode));
		$telecom = $assignedAuthor->addChild('telecom');
		if (strlen($building->phoneNumber) > 0) {
			//$telecom->addAttribute('use','HP');
			$telecom->addAttribute('value','tel:'.html_convert_entities($building->phoneNumber));
		}
		$assignedPerson = $assignedAuthor->addChild('assignedPerson');
		$name = $assignedPerson->addChild('name');
		$name->addChild('prefix',html_convert_entities($providerName['prefix']));
		$name->addChild('given',html_convert_entities($providerName['given']));
		$name->addChild('family',html_convert_entities($providerName['family']));
		$representedOrg = $assignedAuthor->addChild('representedOrganization');
		$id = $representedOrg->addChild('id');
		$id->addAttribute('root','2.16.840.1.113883.19.5');
		$representedOrg->addChild('name',html_convert_entities($buildingName));
		$address = $building->practice->primaryAddress;
		$telecom = $representedOrg->addChild('telecom');
		if (strlen($building->practice->mainPhone->number) > 0) {
			//$telecom->addAttribute('use','HP');
			$telecom->addAttribute('value','tel:'.html_convert_entities($building->practice->mainPhone->number));
		}
		$addr = $representedOrg->addChild('addr');
		if ($address->addressId > 0) {
			$addr->addAttribute('use','HP');
			$addr->addChild('streetAddressLine',html_convert_entities((strlen($address->line2) > 0)?$address->line1.' '.$address->line2:$address->line1));
			$addr->addChild('city',html_convert_entities($address->city));
			$addr->addChild('state',html_convert_entities($address->state));
			$addr->addChild('postalCode',html_convert_entities($address->zipCode));
		}

		// CUSTODIAN
		$custodian = $xml->addChild('custodian');
		$assignedCustodian = $custodian->addChild('assignedCustodian');
		$representedOrg = $assignedCustodian->addChild('representedCustodianOrganization');
		$id = $representedOrg->addChild('id');
		$id->addAttribute('root','2.16.840.1.113883.19.5');
		$representedOrg->addChild('name','NIST Registry');
		$telecom = $representedOrg->addChild('telecom');
		$telecom->addAttribute('value','tel:+1-301-975-3251');
		$addr = $representedOrg->addChild('addr');
		$addr->addChild('streetAddressLine','100 Bureau Drive');
		$addr->addChild('city','Gaithersburg');
		$addr->addChild('state','MD');
		$addr->addChild('postalCode','20899');

		// PARTICIPANT
		$participant = $xml->addChild('participant');
		$participant->addAttribute('typeCode','IND');
		$associatedEntity = $participant->addChild('associatedEntity');
		$associatedEntity->addAttribute('classCode','GUAR');
		$id = $associatedEntity->addChild('id');
		$id->addAttribute('root','4ff51570-83a9-47b7-91f2-93ba30373141');
		$addr = $associatedEntity->addChild('addr');
		//$addr->addChild('streetAddressLine','17 Daws Rd.');
		//$addr->addChild('city','Blue Bell');
		//$addr->addChild('state','MA');
		//$addr->addChild('postalCode','02368');
		$telecom = $associatedEntity->addChild('telecom');
		//$telecom->addAttribute('value','tel:(888)555-1212');
		$associatedPerson = $associatedEntity->addChild('associatedPerson');
		$name = $associatedPerson->addChild('name');
		//$name->addChild('given','Kenneth');
		//$name->addChild('family','Ross');

		// DOCUMENTATION OF
		$documentationOf = $xml->addChild('documentationOf');
		$serviceEvent = $documentationOf->addChild('serviceEvent');
		$serviceEvent->addAttribute('classCode','PCPR');
		$effectiveTime = $serviceEvent->addChild('effectiveTime');
		$low = $effectiveTime->addChild('low');
		$lowValue = date('Ymd');
		$low->addAttribute('value',$lowValue);
		$high = $effectiveTime->addChild('high');
		$highValue = date('Ymd',strtotime('+1 month'));
		$high->addAttribute('value',$highValue);

		// Performer
		foreach ($this->performers as $provider) {
			$performer = $serviceEvent->addChild('performer');
			$performer->addAttribute('typeCode','PRF');
			$templateId = $performer->addChild('templateId');
			$templateId->addAttribute('root','2.16.840.1.113883.3.88.11.83.4');
			$templateId->addAttribute('assigningAuthorityName','HITSP C83');
			$templateId = $performer->addChild('templateId');
			$templateId->addAttribute('root','1.3.6.1.4.1.19376.1.5.3.1.2.3');
			$templateId->addAttribute('assigningAuthorityName','IHE PCC');
			$functionCode = $performer->addChild('functionCode');
			$functionCode->addAttribute('code','PP');
			$functionCode->addAttribute('displayName','Primary Care Provider');
			$functionCode->addAttribute('codeSystem','2.16.840.1.113883.12.443');
			$functionCode->addAttribute('codeSystemName','Provider Role');
			$functionCode->addChild('originalText','Primary Care Provider');
			$time = $performer->addChild('time');
			$low = $time->addChild('low');
			$lowValue = date('Y');
			$low->addAttribute('value',$lowValue);
			$high = $time->addChild('high');
			$highValue = date('Ymd',strtotime('+1 month'));
			$high->addAttribute('value',$highValue);

			$assignedEntity = $performer->addChild('assignedEntity');
			$id = $assignedEntity->addChild('id');
			$id->addAttribute('extension','PseudoMD-'.$provider->personId);
			$id->addAttribute('root','2.16.840.1.113883.3.72.5.2');
			$id = $assignedEntity->addChild('id');
			$id->addAttribute('extension','999999999');
			$id->addAttribute('root','2.16.840.1.113883.4.6');
			// <code code="200000000X" displayName="Allopathic and Osteopathic Physicians" codeSystemName="Provider Codes" codeSystem="2.16.840.1.113883.6.101"/>
			$addr = $assignedEntity->addChild('addr');
			$address = new Address();
			$address->personId = $provider->personId;
			$addressIterator = $address->getIteratorByPersonId();
			foreach ($addressIterator as $address) {
				break; // retrieves the top address
			}
			if ($address->addressId > 0) {
				$addr->addAttribute('use','HP');
				$addr->addChild('streetAddressLine',html_convert_entities((strlen($address->line2) > 0)?$address->line1.' '.$address->line2:$address->line1));
				$addr->addChild('city',html_convert_entities($address->city));
				$addr->addChild('state',html_convert_entities($address->state));
				$addr->addChild('postalCode',html_convert_entities($address->zipCode));
			}
			$telecom = $assignedEntity->addChild('telecom');
			$phoneNumber = new PhoneNumber();
			$phoneNumber->personId = $provider->personId;
			foreach ($phoneNumber->getPhoneNumbers(false) as $phone) {
				break; // retrieves the top phone
			}
			if (strlen($phone['number']) > 0) {
				$telecom->addAttribute('use','HP');
				$telecom->addAttribute('value','tel:'.html_convert_entities($phone['number']));
			}

			$assignedPerson = $assignedEntity->addChild('assignedPerson');
			$name = $assignedPerson->addChild('name');

			$name->addChild('prefix',html_convert_entities($provider->person->prefix));
			$name->addChild('given',html_convert_entities($provider->person->firstName));
			$name->addChild('family',html_convert_entities($provider->person->lastName));
			$representedOrg = $assignedEntity->addChild('representedOrganization');
			$id = $representedOrg->addChild('id');
			$id->addAttribute('root','2.16.840.1.113883.3.72.5');
			$representedOrg->addChild('name');
			$telecom = $representedOrg->addChild('telecom');
			$addr = $representedOrg->addChild('addr');
			/*$representedOrg->addChild('name',$buildingName);
			$telecom = $representedOrg->addChild('telecom');
			if (strlen($building->practice->mainPhone->number) > 0) {
				$telecom->addAttribute('use','HP');
				$telecom->addAttribute('value','tel:'.$building->practice->mainPhone->number);
			}
			$addr = $representedOrg->addChild('addr');
			if ($address->addressId > 0) {
				$addr->addAttribute('use','HP');
				$addr->addChild('streetAddressLine',(strlen($address->line2) > 0)?$address->line1.' '.$address->line2:$address->line1);
				$addr->addChild('city',$address->city);
				$addr->addChild('state',$address->state);
				$addr->addChild('postalCode',$address->zipCode);
			}*/
		}
	}

	public function populateBody(SimpleXMLElement $xml) {
		$component = $xml->addChild('component');
		$structuredBody = $component->addChild('structuredBody');

		$this->populatePurpose($structuredBody);
		$this->populatePayers($structuredBody);
		$this->populateAdvanceDirectives($structuredBody);
		$this->populateFunctionalStatus($structuredBody);
		CCDProblems::populate($this,$structuredBody);
		$this->populateFamilyHistory($structuredBody);
		$this->populateSocialHistory($structuredBody);
		CCDAllergies::populate($this,$structuredBody);
		CCDMedications::populate($this,$structuredBody);
		$this->populateMedicalEquipment($structuredBody);
		$this->populateImmunizations($structuredBody);
		$this->populateVitalSigns($structuredBody);
		CCDResults::populate($this,$structuredBody);
		$this->populateProcedures($structuredBody);
		$this->populateEncounters($structuredBody);
		$this->populateCarePlan($structuredBody);
	}

	public function populatePurpose(SimpleXMLElement $xml) {
		$component = $xml->addChild('component');
		$section = $component->addChild('section');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.10.20.1.13');
		$code = $section->addChild('code');
		$code->addAttribute('code','48764-5');
		$code->addAttribute('codeSystem','2.16.840.1.113883.6.1');
		$section->addChild('title','Summary Purpose');
		$section->addChild('text','Transfer of care');
	}

	public function populatePayers(SimpleXMLElement $xml) {
		$component = $xml->addChild('component');
		$section = $component->addChild('section');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.10.20.1.9');
		// <!-- Payers section template -->
		$code = $section->addChild('code');
		$code->addAttribute('code','48768-6');
		$code->addAttribute('codeSystem','2.16.840.1.113883.6.1');
		$section->addChild('title','Payers');

		$rows = array();
		$insurancePrograms = InsuranceProgram::getInsurancePrograms();
		$insuredRelationship = new InsuredRelationship();
		$insuredRelationshipIterator = $insuredRelationship->getIteratorByPersonId($this->_patientId);
		foreach ($insuredRelationshipIterator as $item) {
			$company = '';
			$program = '';
			if (isset($insurancePrograms[$item->insuranceProgramId])) {
				$exp = explode('->',$insurancePrograms[$item->insuranceProgramId]);
				$company = html_convert_entities($exp[0]);
				$program = html_convert_entities($exp[1]);
			}
			$rows[] = array(
				'company'=>$company,
				'program'=>$program,
				'groupNumber'=>html_convert_entities($item->groupNumber),
			);
		}
		$text = $section->addChild('text');
		if ($rows) {
			$table = $text->addChild('table');
			$thead = $table->addChild('thead');
			$tr = $thead->addChild('tr');
			$tr->addChild('th','Payer name');
			$tr->addChild('th','Policy type / Coverage type');
			$tr->addChild('th','Covered party ID');
			$tr->addChild('th','Authorization(s)');
			$tbody = $table->addChild('tbody');
			foreach ($rows as $row) {
				$tr = $tbody->addChild('tr');
				$tr->addChild('td',$row['company']);
				$tr->addChild('td',$row['program']);
				$tr->addChild('td',$row['groupNumber']);
				$tr->addChild('td','');
			}
		}
	}

	public function populateAdvanceDirectives(SimpleXMLElement $xml) {
	}

	public function populateFunctionalStatus(SimpleXMLElement $xml) {
	}

	public function populateFamilyHistory(SimpleXMLElement $xml) {
	}

	public function populateSocialHistory(SimpleXMLElement $xml) {
	}

	public function populateMedicalEquipment(SimpleXMLElement $xml) {
	}

	public function populateImmunizations(SimpleXMLElement $xml) {
		$component = $xml->addChild('component');
		$section = $component->addChild('section');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.10.20.1.6');
		// <!-- Immunizations section template -->
		$code = $section->addChild('code');
		$code->addAttribute('code','11369-6');
		$code->addAttribute('codeSystem','2.16.840.1.113883.6.1');
		$section->addChild('title','Immunizations');
		$rows = array();
		$iterator = new PatientImmunizationIterator();
		$this->setFiltersDateRange($filters);
		$iterator->setFilter(array('patientId'=>$this->_patientId));
		foreach ($iterator as $immunization) {
			$status = 'Completed'; // TODO: where to get the status?
			$rows[] = array(
				'vaccine'=>html_convert_entities($immunization->immunization),
				'date'=>date('M d, Y',strtotime($immunization->dateAdministered)),
				'status'=>html_convert_entities($status),
			);
		}
		$text = $section->addChild('text');
		if ($rows) {
			$table = $text->addChild('table');
			$thead = $table->addChild('thead');
			$tr = $thead->addChild('tr');
			$tr->addChild('th','Vaccine');
			$tr->addChild('th','Date');
			$tr->addChild('th','Status');
			$tbody = $table->addChild('tbody');
			foreach ($rows as $row) {
				$tr = $tbody->addChild('tr');
				$tr->addChild('td',$row['vaccine']);
				$tr->addChild('td',$row['date']);
				$tr->addChild('td',$row['status']);
			}
		}
	}

	public function populateVitalSigns(SimpleXMLElement $xml) {
		$component = $xml->addChild('component');
		$section = $component->addChild('section');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.10.20.1.16');
		// <!-- Vital signs section template -->
		$code = $section->addChild('code');
		$code->addAttribute('code','8716-3');
		$code->addAttribute('codeSystem','2.16.840.1.113883.6.1');
		$section->addChild('title','Vital Signs');

		$filters = array('personId'=>$this->_patientId);
		$this->setFiltersDateRange($filters);
		$iterator = new VitalSignGroupsIterator();
		$iterator->setFilter($filters);
		$headers = array('Date / Time:');
		$vitals = array();
		foreach ($iterator as $vsGroup) {
			$headers[$vsGroup->dateTime] = date('M d, Y',strtotime($vsGroup->dateTime));
			foreach ($vsGroup->vitalSignValues as $vital) {
				$vitals[$vital->vital][$vsGroup->dateTime] = $vital;
			}
		}
		$rows = array();
		$labelKeyValues = VitalSignTemplate::generateVitalSignsTemplateKeyValue();
		foreach ($labelKeyValues as $key=>$value) {
			if (!isset($vitals[$key])) continue;
			$row = array(
				'value'=>html_convert_entities($value),
				'data'=>array(),
			);
			foreach ($vitals[$key] as $dateTime=>$vital) {
				$row['data'][] = html_convert_entities($vital->value.' '.$vital->units);
			}
			$rows[] = $row;
		}
		$text = $section->addChild('text');
		if ($rows) {
			$table = $text->addChild('table');
			$thead = $table->addChild('thead');
			$tr = $thead->addChild('tr');
			$align = 'right';
			foreach ($headers as $header) {
				$th = $tr->addChild('th',$header);
				$th->addAttribute('align',$align);
				$align = 'left';
			}
			$tbody = $table->addChild('tbody');
			foreach ($rows as $row) {
				$tr = $tbody->addChild('tr');
				$tr->addChild('th',$row['value']);
				foreach ($row['data'] as $data) {
					$tr->addChild('td',$data);
				}
			}
		}
	}

	public function populateProcedures(SimpleXMLELement $xml) {
		$component = $xml->addChild('component');
		$section = $component->addChild('section');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.10.20.1.12');
		// <!-- Procedures section template -->
		$code = $section->addChild('code');
		$code->addAttribute('code','47519-4');
		$code->addAttribute('codeSystem','2.16.840.1.113883.6.1');
		$section->addChild('title','Procedures');
		$rows = array();
		$filters = array('patientId'=>$this->_patientId);
		$this->setFiltersDateRange($filters);
		$iterator = new PatientProcedureIterator(null,false);
		$iterator->setFilters($filters);
		$ctr = 1;
		foreach ($iterator as $procedure) {
			$rows[] = array(
				'contents'=>array('id'=>'Proc'.$ctr++,'value'=>html_convert_entities($procedure->procedure)),
				'date'=>date('M d, Y',strtotime($procedure->dateTime)),
			);
		}
		$text = $section->addChild('text');
		if ($rows) {
			$table = $text->addChild('table');
			$thead = $table->addChild('thead');
			$tr = $thead->addChild('tr');
			$tr->addChild('th','Procedure');
			$tr->addChild('th','Date');
			$tbody = $table->addChild('tbody');
			foreach ($rows as $row) {
				$tr = $tbody->addChild('tr');
				$td = $tr->addChild('td');
				$content = $td->addChild('content',$row['contents']['value']);
				$content->addAttribute('ID',$row['contents']['id']);
				$tr->addChild('td',$row['date']);
			}
		}
	}

	public function populateEncounters(SimpleXMLElement $xml) {
		$component = $xml->addChild('component');
		$section = $component->addChild('section');
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root','2.16.840.1.113883.10.20.1.3');
		// <!-- Encounters section template -->
		$code = $section->addChild('code');
		$code->addAttribute('code','46240-8');
		$code->addAttribute('codeSystem','2.16.840.1.113883.6.1');
		$section->addChild('title','Encounters');

		if ($this->visit !== null) {
			$visitIterator = array($this->visit);
		}
		else {
			$visitIterator = new VisitIterator();
			$visitIterator->setFilters(array('patientId'=>$this->_patientId));
		}
		$rows = array();
		foreach ($visitIterator as $visit) {
			$building = new Building();
			$building->buildingId = $visit->buildingId;
			$building->populate();
			$appointment = new Appointment();
			$appointment->appointmentId = $visit->appointmentId;
			$appointment->populate();
			$rows[] = array(
				'encounter'=>html_convert_entities($appointment->title),
				'location'=>html_convert_entities($building->displayName),
				'date'=>date('M d, Y',strtotime($visit->dateOfTreatment)),
			);
		}

		$text = $section->addChild('text');
		if ($rows) {
			$table = $text->addChild('table');
			$thead = $table->addChild('thead');
			$tr = $thead->addChild('tr');
			$tr->addChild('th','Encounter');
			$tr->addChild('th','Location');
			$tr->addChild('th','Date');
			$tbody = $table->addChild('tbody');
			foreach ($rows as $row) {
				$tr = $tbody->addChild('tr');
				$tr->addChild('td',$row['encounter']);
				$tr->addChild('td',$row['location']);
				$tr->addChild('td',$row['date']);
			}
		}
	}

	public function populateCarePlan(SimpleXMLElement $xml) {
	}

}

function html_convert_entities($string) {
	$string = htmlentities($string);
	return preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/','convert_entity', $string);
}

/* Swap HTML named entity with its numeric equivalent. If the entity
 * isn't in the lookup table, this function returns a blank, which
 * destroys the character in the output - this is probably the 
 * desired behaviour when producing XML. */
function convert_entity($matches) {
	static $table = array(
		'quot'    => '&#34;',
		'amp'      => '&#38;',
		'lt'       => '&#60;',
		'gt'       => '&#62;',
		'OElig'    => '&#338;',
		'oelig'    => '&#339;',
		'Scaron'   => '&#352;',
		'scaron'   => '&#353;',
		'Yuml'     => '&#376;',
		'circ'     => '&#710;',
		'tilde'    => '&#732;',
		'ensp'     => '&#8194;',
		'emsp'     => '&#8195;',
		'thinsp'   => '&#8201;',
		'zwnj'     => '&#8204;',
		'zwj'      => '&#8205;',
		'lrm'      => '&#8206;',
		'rlm'      => '&#8207;',
		'ndash'    => '&#8211;',
		'mdash'    => '&#8212;',
		'lsquo'    => '&#8216;',
		'rsquo'    => '&#8217;',
		'sbquo'    => '&#8218;',
		'ldquo'    => '&#8220;',
		'rdquo'    => '&#8221;',
		'bdquo'    => '&#8222;',
		'dagger'   => '&#8224;',
		'Dagger'   => '&#8225;',
		'permil'   => '&#8240;',
		'lsaquo'   => '&#8249;',
		'rsaquo'   => '&#8250;',
		'euro'     => '&#8364;',
		'fnof'     => '&#402;',
		'Alpha'    => '&#913;',
		'Beta'     => '&#914;',
		'Gamma'    => '&#915;',
		'Delta'    => '&#916;',
		'Epsilon'  => '&#917;',
		'Zeta'     => '&#918;',
		'Eta'      => '&#919;',
		'Theta'    => '&#920;',
		'Iota'     => '&#921;',
		'Kappa'    => '&#922;',
		'Lambda'   => '&#923;',
		'Mu'       => '&#924;',
		'Nu'       => '&#925;',
		'Xi'       => '&#926;',
		'Omicron'  => '&#927;',
		'Pi'       => '&#928;',
		'Rho'      => '&#929;',
		'Sigma'    => '&#931;',
		'Tau'      => '&#932;',
		'Upsilon'  => '&#933;',
		'Phi'      => '&#934;',
		'Chi'      => '&#935;',
		'Psi'      => '&#936;',
		'Omega'    => '&#937;',
		'alpha'    => '&#945;',
		'beta'     => '&#946;',
		'gamma'    => '&#947;',
		'delta'    => '&#948;',
		'epsilon'  => '&#949;',
		'zeta'     => '&#950;',
		'eta'      => '&#951;',
		'theta'    => '&#952;',
		'iota'     => '&#953;',
		'kappa'    => '&#954;',
		'lambda'   => '&#955;',
		'mu'       => '&#956;',
		'nu'       => '&#957;',
		'xi'       => '&#958;',
		'omicron'  => '&#959;',
		'pi'       => '&#960;',
		'rho'      => '&#961;',
		'sigmaf'   => '&#962;',
		'sigma'    => '&#963;',
		'tau'      => '&#964;',
		'upsilon'  => '&#965;',
		'phi'      => '&#966;',
		'chi'      => '&#967;',
		'psi'      => '&#968;',
		'omega'    => '&#969;',
		'thetasym' => '&#977;',
		'upsih'    => '&#978;',
		'piv'      => '&#982;',
		'bull'     => '&#8226;',
		'hellip'   => '&#8230;',
		'prime'    => '&#8242;',
		'Prime'    => '&#8243;',
		'oline'    => '&#8254;',
		'frasl'    => '&#8260;',
		'weierp'   => '&#8472;',
		'image'    => '&#8465;',
		'real'     => '&#8476;',
		'trade'    => '&#8482;',
		'alefsym'  => '&#8501;',
		'larr'     => '&#8592;',
		'uarr'     => '&#8593;',
		'rarr'     => '&#8594;',
		'darr'     => '&#8595;',
		'harr'     => '&#8596;',
		'crarr'    => '&#8629;',
		'lArr'     => '&#8656;',
		'uArr'     => '&#8657;',
		'rArr'     => '&#8658;',
		'dArr'     => '&#8659;',
		'hArr'     => '&#8660;',
		'forall'   => '&#8704;',
		'part'     => '&#8706;',
		'exist'    => '&#8707;',
		'empty'    => '&#8709;',
		'nabla'    => '&#8711;',
		'isin'     => '&#8712;',
		'notin'    => '&#8713;',
		'ni'       => '&#8715;',
		'prod'     => '&#8719;',
		'sum'      => '&#8721;',
		'minus'    => '&#8722;',
		'lowast'   => '&#8727;',
		'radic'    => '&#8730;',
		'prop'     => '&#8733;',
		'infin'    => '&#8734;',
		'ang'      => '&#8736;',
		'and'      => '&#8743;',
		'or'       => '&#8744;',
		'cap'      => '&#8745;',
		'cup'      => '&#8746;',
		'int'      => '&#8747;',
		'there4'   => '&#8756;',
		'sim'      => '&#8764;',
		'cong'     => '&#8773;',
		'asymp'    => '&#8776;',
		'ne'       => '&#8800;',
		'equiv'    => '&#8801;',
		'le'       => '&#8804;',
		'ge'       => '&#8805;',
		'sub'      => '&#8834;',
		'sup'      => '&#8835;',
		'nsub'     => '&#8836;',
		'sube'     => '&#8838;',
		'supe'     => '&#8839;',
		'oplus'    => '&#8853;',
		'otimes'   => '&#8855;',
		'perp'     => '&#8869;',
		'sdot'     => '&#8901;',
		'lceil'    => '&#8968;',
		'rceil'    => '&#8969;',
		'lfloor'   => '&#8970;',
		'rfloor'   => '&#8971;',
		'lang'     => '&#9001;',
		'rang'     => '&#9002;',
		'loz'      => '&#9674;',
		'spades'   => '&#9824;',
		'clubs'    => '&#9827;',
		'hearts'   => '&#9829;',
		'diams'    => '&#9830;',
		'nbsp'     => '&#160;',
		'iexcl'    => '&#161;',
		'cent'     => '&#162;',
		'pound'    => '&#163;',
		'curren'   => '&#164;',
		'yen'      => '&#165;',
		'brvbar'   => '&#166;',
		'sect'     => '&#167;',
		'uml'      => '&#168;',
		'copy'     => '&#169;',
		'ordf'     => '&#170;',
		'laquo'    => '&#171;',
		'not'      => '&#172;',
		'shy'      => '&#173;',
		'reg'      => '&#174;',
		'macr'     => '&#175;',
		'deg'      => '&#176;',
		'plusmn'   => '&#177;',
		'sup2'     => '&#178;',
		'sup3'     => '&#179;',
		'acute'    => '&#180;',
		'micro'    => '&#181;',
		'para'     => '&#182;',
		'middot'   => '&#183;',
		'cedil'    => '&#184;',
		'sup1'     => '&#185;',
		'ordm'     => '&#186;',
		'raquo'    => '&#187;',
		'frac14'   => '&#188;',
		'frac12'   => '&#189;',
		'frac34'   => '&#190;',
		'iquest'   => '&#191;',
		'Agrave'   => '&#192;',
		'Aacute'   => '&#193;',
		'Acirc'    => '&#194;',
		'Atilde'   => '&#195;',
		'Auml'     => '&#196;',
		'Aring'    => '&#197;',
		'AElig'    => '&#198;',
		'Ccedil'   => '&#199;',
		'Egrave'   => '&#200;',
		'Eacute'   => '&#201;',
		'Ecirc'    => '&#202;',
		'Euml'     => '&#203;',
		'Igrave'   => '&#204;',
		'Iacute'   => '&#205;',
		'Icirc'    => '&#206;',
		'Iuml'     => '&#207;',
		'ETH'      => '&#208;',
		'Ntilde'   => '&#209;',
		'Ograve'   => '&#210;',
		'Oacute'   => '&#211;',
		'Ocirc'    => '&#212;',
		'Otilde'   => '&#213;',
		'Ouml'     => '&#214;',
		'times'    => '&#215;',
		'Oslash'   => '&#216;',
		'Ugrave'   => '&#217;',
		'Uacute'   => '&#218;',
		'Ucirc'    => '&#219;',
		'Uuml'     => '&#220;',
		'Yacute'   => '&#221;',
		'THORN'    => '&#222;',
		'szlig'    => '&#223;',
		'agrave'   => '&#224;',
		'aacute'   => '&#225;',
		'acirc'    => '&#226;',
		'atilde'   => '&#227;',
		'auml'     => '&#228;',
		'aring'    => '&#229;',
		'aelig'    => '&#230;',
		'ccedil'   => '&#231;',
		'egrave'   => '&#232;',
		'eacute'   => '&#233;',
		'ecirc'    => '&#234;',
		'euml'     => '&#235;',
		'igrave'   => '&#236;',
		'iacute'   => '&#237;',
		'icirc'    => '&#238;',
		'iuml'     => '&#239;',
		'eth'      => '&#240;',
		'ntilde'   => '&#241;',
		'ograve'   => '&#242;',
		'oacute'   => '&#243;',
		'ocirc'    => '&#244;',
		'otilde'   => '&#245;',
		'ouml'     => '&#246;',
		'divide'   => '&#247;',
		'oslash'   => '&#248;',
		'ugrave'   => '&#249;',
		'uacute'   => '&#250;',
		'ucirc'    => '&#251;',
		'uuml'     => '&#252;',
		'yacute'   => '&#253;',
		'thorn'    => '&#254;',
		'yuml'     => '&#255;'
	);

	// Entity not found? Destroy it.
	return isset($table[$matches[1]]) ? $table[$matches[1]] : '';
}
