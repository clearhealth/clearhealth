<?php
/*****************************************************************************
*       Patient.php
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


class Patient extends WebVista_Model_ORM {

	protected $person_id;
        protected $person;
        //protected $address;
	protected $homeAddress;
	protected $billingAddress;
        //protected $phone_number;
        protected $default_provider;
        protected $record_number;
        protected $confidentiality;
	protected $defaultPharmacyId;
        //protected $registration_location_id;
	protected $signedHipaaDate;
	protected $teamId;
        protected $_vitals;

        protected $_primaryKeys = array('person_id');
        protected $_table = "patient";
        protected $_legacyORMNaming = true;
	protected $_cascadePersist = false;

	function __construct() {
		parent::__construct();
		$this->person = new Person();
		$this->homeAddress = new Address();
		$this->homeAddress->_cascadePersist = false;
		$this->billingAddress = new Address();
		$this->billingAddress->_cascadePersist = false;
		//$this->phoneNumber = new PhoneNumber();
	}

	public function persist() {
		$this->homeAddress->type = 'HOME';
		$this->billingAddress->type = 'BILL';
		parent::persist();
		$this->person->persist();
	}

	public function setPerson_id($key) {
		$this->setPersonId($key);
	}

	function setPersonId($key) {
		$id = (int)$key;
		if ($id != $this->person_id) { // personId has been changed
			if ($this->person->personId > 0) {
				$this->person = new Person();
			}
			if ($this->homeAddress->personId > 0) {
				$this->homeAddress = new Address();
				$this->homeAddress->_cascadePersist = false;
			}
			if ($this->billingAddress->personId > 0) {
				$this->billingAddress = new Address();
				$this->billingAddress->_cascadePersist = false;
			}
		}
		$this->person_id = $id; // person_id MUST be the same name as declared
		$this->person->personId = $id;
		$this->homeAddress->personId = $id;
		$this->billingAddress->personId = $id;
		//$this->address->personId = (int)$key;
		//$this->phoneNumber->personId = (int)$key;
	}

	function __get($key) {
                if (in_array($key,$this->ORMFields())) {
                        return $this->$key;
                }
                elseif (in_array($key,$this->person->ORMFields())) {
                        return $this->person->__get($key);
                }
                elseif (!is_null(parent::__get($key))) {
                        return parent::__get($key);
                }
                elseif (!is_null($this->person->__get($key))) {
                        return $this->person->__get($key);
                }
                return parent::__get($key);
        }
	function getDefaultPharmacyId() {
		return $this->defaultPharmacyId;
	}
	function setDefaultPharmacyId($value) {
		$this->defaultPharmacyId = $value;
	}
	function getWeight() {
		//return "141 lb.";
		if (count($this->_vitals) == 0) $this->_loadVitals();
		foreach ($this->_vitals as $vital) {
			if ($vital['vital'] == "weight") return $vital['value'] . strtolower($vital['units']);
		}
	}
	
	function getHeight() {
		//return "5' 4\" (64\")";
		if (count($this->_vitals) == 0) $this->_loadVitals();
		foreach ($this->_vitals as $vital) {
			if ($vital['vital'] == "height") return $vital['value'] . strtolower($vital['units']);
		}
	}

	private function _loadAddresses() {
                $addressIterator = $this->homeAddress->getIterator();
		if (!($addressIterator instanceof WebVista_Model_ORMIterator) || !method_exists($addressIterator,'setFilters')) return;
                $addressIterator->setFilters(array('personId' => $this->personId,'class'=>'person'));
                foreach($addressIterator as $address) {
			switch ($address->type) {
				case Address::TYPE_HOME:
					$this->homeAddress = $address;
					break;
				case Address::TYPE_BILLING:
					$this->billingAddress = $address;
					break;
			}
                }
	}

	function populate() {
		$retval = parent::populate();
		$this->_loadAddresses();
		return $retval;
	}

	public function getBMI() {
		if (count($this->_vitals) == 0) $this->_loadVitals();
		foreach ($this->_vitals as $vital) {
			if ($vital['vital'] == 'BMI') return $vital['value'];
		}

		return '0.00';
	}

	public function getBSA() {
		if (count($this->_vitals) == 0) $this->_loadVitals();
		foreach ($this->_vitals as $vital) {
			if ($vital['vital'] == 'BSA') return sprintf('%.2f',$vital['value']);
		}

		return '0.00';
	}

	function _loadVitals() {
		$this->_vitals = VitalSignGroup::getBMIVitalsForPatientId($this->personId);
	}

	public function populateWithMRN($mrn = null) {
		if ($mrn === null) {
			$mrn = $this->recordNumber;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('patient','person_id')
				->where('record_number = ?',$mrn);
		$ret = false;
		if ($row = $db->fetchRow($sqlSelect)) {
			$this->personId = $row['person_id'];
			$this->populate();
			$ret = true;
		}
		return $ret;
	}

	public function ssCheck() {
		$ret = array();

		// required SS: Name (last and first), Gender, Date of Birth
		$person = $this->person;
		$lastNameLen = strlen($person->lastName);
		if (!$lastNameLen > 0 || $lastNameLen > 35) {
			$ret[] = 'Last Name field must be supplied and not more than 35 characters';
		}
		$firstNameLen = strlen($person->firstName);
		if (!$firstNameLen > 0 || $firstNameLen > 35) {
			$ret[] = 'First Name field must be supplied and not more than 35 characters';
		}

		$gender = $person->gender;
		// Gender options = M, F, U
		$genderList = array('M'=>'Male','F'=>'Female','U'=>'Unknown',1=>'Male',2=>'Female',3=>'Unknown');
		if (!isset($genderList[$gender])) {
			$ret[] = 'Gender is invalid';
		}
		// Patient DOB must not be future
		$date = date('Y-m-d');
		$dateOfBirth = date('Ymd',strtotime($person->dateOfBirth));
		if ($person->dateOfBirth == '0000-00-00' || strtotime($dateOfBirth) > strtotime($date)) {
			$ret[] = 'Date of birth is invalid';
		}

		// Have appropriate validation on patient address/phone as required by SS docs
		$address = new Address();
		$address->personId = $this->personId;
		$addressIterator = $address->getIteratorByPersonId();
		foreach ($addressIterator as $address) {
			break; // retrieves the top address
		}
		//$address->populateWithType('MAIN');
		$line1Len = strlen($address->line1);
		if (!$line1Len > 0 || $line1Len > 35) {
			$ret[] = 'Address line1 field must be supplied and not more than 35 characters';
		}
		$line2Len = strlen($address->line2);
		if ($line2Len > 0 && $line2Len > 35) {
			$ret[] = 'Address line2 must not be more than 35 characters';
		}
		$cityLen = strlen($address->city);
		if (!$cityLen > 0 || $cityLen > 35) {
			$ret[] = 'Address city field must be supplied and not more than 35 characters';
		}
		if (strlen($address->state) != 2) {
			$ret[] = 'Address state field must be supplied and not more than 2 characters';
		}
		$zipCodeLen = strlen($address->zipCode);
		if ($zipCodeLen != 5 && $zipCodeLen != 9) {
			$ret[] = 'Address zipcode must be supplied and must be 5 or 9 digit long';
		}

		$phoneNumber = new PhoneNumber();
		$phoneNumber->personId = $person->personId;
		$phones = $phoneNumber->phoneNumbers;
		$hasTE = false;
		foreach ($phones as $phone) {
			if ($phone['type'] == 'TE') {
				$hasTE = true;
				break;
			}
			if (strlen($phone['number']) < 11) {
				$ret[] = 'Phone number \''.$phone['number'].'\' is invalid';
			}
		}
		if (!$hasTE) {
			$ret[] = 'Phone must have at least one Emergency, Employer or Billing';
		}

		return $ret;
	}

	public function getPatientId() {
		return $this->person_id;
	}

	protected static function generateRowData($patientId) {
		$patient = new Patient();
		$patient->personId = (int)$patientId;
		$patient->populate();
		$person = $patient->person;
		$row = array();
		$row['MRN'] = $patient->recordNumber;
		$row['lastName'] = $person->lastName;
		$row['firstName'] = $person->firstName;
		$row['middleName'] = $person->middleName;
		return $row;
	}

	public static function listProblems(Array $filters) {
		if (!$filters) return array();
		// code => Text
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('problemLists',array('codeTextShort AS problem','*'))
				->group('personId');
		foreach ($filters as $key=>$value) {
			$sqlSelect->orWhere('code = ?',(string)$key);
		}
		//trigger_error($sqlSelect->__toString());
		$rows = array();
		$dbStmt = $db->query($sqlSelect);
		while ($row = $dbStmt->fetch()) {
			$personId = (int)$row['personId'];
			$rows[$personId] = self::generateRowData($personId);
			if (!isset($rows[$personId]['problems'])) $rows[$personId]['problems'] = array();
			$rows[$personId]['problems'][] = $row['problem'];
			if (!isset($rows[$personId]['problemList'])) $rows[$personId]['problemList'] = array();
			$rows[$personId]['problemList'][] = $row;
		}
		return $rows;
	}

	public static function listMedications(Array $filters) {
		if (!$filters) return array();
		// pkey => Text
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('medications',array('personId','description AS medication'))
				->group('personId');
		foreach ($filters as $key=>$value) {
			$sqlSelect->where('pkey = ?',(string)$key);
		}
		//trigger_error($sqlSelect->__toString());
		$rows = array();
		$dbStmt = $db->query($sqlSelect);
		while ($row = $dbStmt->fetch()) {
			$personId = (int)$row['personId'];
			$rows[$personId] = self::generateRowData($personId);
			if (!isset($rows[$personId]['medications'])) $rows[$personId]['medications'] = array();
			$rows[$personId]['medications'][] = $row['medication'];
		}
		return $rows;
	}

	public static function listDemographics(Array $filters) {
		if (!$filters) return array();
		// key => array('[key]-enabled','name','type','operator','operand1','operand2')
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('patient','person_id AS patientPersonId')
				->join('person','person.person_id = patient.person_id',array('gender','marital_status',"(DATE_FORMAT(NOW(),'%Y') - DATE_FORMAT(person.date_of_birth,'%Y') - (DATE_FORMAT(NOW(),'00-%m-%d') < DATE_FORMAT(person.date_of_birth,'00-%m-%d'))) AS age"))
				->joinLeft('patientStatistics','person.person_id = patientStatistics.personId')
				->group('patient.person_id');
		$keys = array();
		foreach ($filters as $key=>$value) {
			if ($key == 'reminders') {
				$config = Zend_Registry::get('config');
				$sqlSelect->joinLeft('address','address.person_id = patient.person_id','address_id AS addressId');
				$sqlSelect->joinLeft('number','number.person_id = patient.person_id','number_id AS numberId');
				if (strtolower($config->patient->detailsView2x) == 'true') {
					$sqlSelect->joinLeft('person_address','person_address.address_id=address.address_id',null);
					$sqlSelect->joinLeft('person_number','person_number.number_id=number.number_id',null);
				}
				$sqlSelect->where("(address.type = 'REMINDERS' OR number.type = 'REMINDERS')");
				continue;
			}
			if (!$value['enabled']) continue;
			$keys[] = $key;
			$operand1 = isset($value['operand1'])?$value['operand1']:'';
			$operator = isset($value['operator'])?$value['operator']:'';
			switch ($operator) {
				case '>':
				case '>=':
				case '<':
				case '<=':
					$where = $value['operator'].' '.$db->quote($operand1);
					break;
				case 'between':
					$operand2 = isset($value['operand2'])?$value['operand2']:'';
					$where = 'BETWEEN '.$db->quote($operand1).' AND '.$db->quote($operand2);
					break;
				case '=':
				default;
					$where = '= '.$db->quote($operand1);
					break;
			}
			switch ($key) {
				case 'gender':
				case 'marital_status':
					$sqlSelect->where('person.'.$key.' '.$where);
					break;
				case 'age':
					$sqlSelect->having("age {$where}");
					break;
				default: // patient statistics
					$sqlSelect->where('patientStatistics.`'.$key.'` '.$where);
					break;
			}
		}
		//trigger_error($sqlSelect->__toString());
		$rows = array();
		$dbStmt = $db->query($sqlSelect);
		while ($row = $dbStmt->fetch()) {
			$personId = (int)$row['patientPersonId'];
			$tmp = self::generateRowData($personId);
			if (isset($filters['reminders'])) {
				$addressId = isset($row['addressId'])?(int)$row['addressId']:0;
				$numberId = isset($row['numberId'])?(int)$row['numberId']:0;
				if (!$addressId > 0 && !$numberId > 0) continue;
				$tmp['addressId'] = $addressId;
				$tmp['numberId'] = $numberId;
			}
			$rows[$personId] = $tmp;
			if (!isset($rows[$personId]['demographics'])) $rows[$personId]['demographics'] = array();
			foreach ($keys as $key) {
				if (!isset($row[$key])) continue;
				$rows[$personId]['demographics'][] = GrowthChartBase::prettyName($key).': '.$row[$key];
			}
		}
		return $rows;
	}

	public static function listLabTestResults(Array $filters) {
		if (!$filters) return array();
		// LOINC_NUM => array('labTest','operator','operand1','operand2','unit','OR')
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('lab_result',"CONCAT(description,': ',value,' ',units) AS result")
				->join('lab_test','lab_test.lab_test_id=lab_result.lab_test_id',null)
				->join('lab_order','lab_order.lab_order_id=lab_test.lab_order_id','lab_order.patient_id AS personId')
				->join('orderLabTests','orderLabTests.orderId = lab_order.lab_order_id',null)
				->group('lab_order.patient_id');
		foreach ($filters as $key=>$value) {
			$where = array('orderLabTests.labTest = '.$db->quote($key));
			$operand1 = isset($value['operand1'])?$value['operand1']:'';
			$operator = isset($value['operator'])?$value['operator']:'';
			$tmp = '(CAST(lab_result.value AS SIGNED) ';
			switch ($operator) {
				case '>':
				case '>=':
				case '<':
				case '<=':
					$tmp .= $value['operator'].' '.$db->quote($operand1);
					break;
				case 'between':
					$operand2 = isset($value['operand2'])?$value['operand2']:'';
					$tmp .= 'BETWEEN '.$db->quote($operand1).' AND '.$db->quote($operand2);
					break;
				case '=':
				default;
					$tmp .= '= '.$db->quote($operand1);
					break;
			}
			$tmp .= ')';
			$where[] = $tmp;
			if (isset($value['unit']) && strlen($value['unit']) > 0) $where[] = 'lab_result.units = '.$db->quote($value['unit']);
			if (isset($value['OR']) && $value['OR']) {
				$sqlSelect->orWhere(implode(' AND ',$where));
			}
			else {
				$sqlSelect->where(implode(' AND ',$where));
			}
		}
		//trigger_error($sqlSelect->__toString());
		$rows = array();
		$dbStmt = $db->query($sqlSelect);
		while ($row = $dbStmt->fetch()) {
			$personId = (int)$row['personId'];
			$rows[$personId] = self::generateRowData($personId);
			if (!isset($rows[$personId]['labTestResults'])) $rows[$personId]['labTestResults'] = array();
			$rows[$personId]['labTestResults'][] = $row['result'];
		}
		return $rows;
	}

	public static function listAllergies(Array $filters) {
		if (!$filters) return array();
		// key = value
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('patientAllergies',array('patientId AS personId','causativeAgent AS allergy'))
				->group('patientId');
		foreach ($filters as $key=>$value) {
			$sqlSelect->where('causativeAgent = ?',(string)$value);
		}
		//trigger_error($sqlSelect->__toString());
		$rows = array();
		$dbStmt = $db->query($sqlSelect);
		while ($row = $dbStmt->fetch()) {
			$personId = (int)$row['personId'];
			$rows[$personId] = self::generateRowData($personId);
			if (!isset($rows[$personId]['allergies'])) $rows[$personId]['allergies'] = array();
			$rows[$personId]['allergies'][] = $row['allergy'];
		}
		return $rows;
	}

	public static function listHSA(Array $filters) {
		if (!$filters) return array();
		// LOINC_NUM => array('hsa','operator','operand1','operand2','OR')
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('healthStatusAlerts',array('personId','message AS alert'))
				->group('personId');
		foreach ($filters as $key=>$value) {
			$hsa = (string)$value['hsa'];
			$sqlSelect->where('message LIKE ?','%'.$hsa.'%');
			$operand1 = isset($value['operand1'])?date('Y-m-d',strtotime($value['operand1'])):'';
			$operator = isset($value['operator'])?$value['operator']:'';
			switch ($operator) {
				case '>':
				case '>=':
				case '<':
				case '<=':
					$where = $value['operator'].' '.$db->quote($operand1);
					break;
				case 'between':
					$operand2 = isset($value['operand2'])?date('Y-m-d',strtotime($value['operand2'])):'';
					$where = 'BETWEEN '.$db->quote($operand1).' AND '.$db->quote($operand2);
					break;
				case '=':
				default;
					$where = '= '.$db->quote($operand1);
					break;
			}
			if (isset($value['OR']) && $value['OR']) {
				$sqlSelect->orWhere('dateDue '.$where);
			}
			else {
				$sqlSelect->where('dateDue '.$where);
			}
		}
		//trigger_error($sqlSelect->__toString());
		$rows = array();
		$dbStmt = $db->query($sqlSelect);
		while ($row = $dbStmt->fetch()) {
			$personId = (int)$row['personId'];
			$rows[$personId] = self::generateRowData($personId);
			if (!isset($rows[$personId]['hsa'])) $rows[$personId]['hsa'] = array();
			$rows[$personId]['hsa'][] = $row['alert'];
		}
		return $rows;
	}

	protected function _addChild(SimpleXMLElement $xml,$key,$value,$checked=true) {
		if (is_object($value)) trigger_error($key.'='.get_class($value));
		if (!$checked || (strlen($key) > 0 && strlen($value) > 0)) $xml->addChild($key,htmlentities($value));
	}

	public function populateXML(SimpleXMLElement $xml=null,$checked=true) {
		if ($xml === null) $xml = new SimpleXMLElement('<data/>');
		$personId = (int)$this->person_id;
		$person = $this->person;
		$picture = '';
		if ($person->activePhoto > 0) {
			$attachment = new Attachment();
			$attachment->attachmentId = (int)$person->activePhoto;
			$attachment->populate();
			$picture = base64_encode($attachment->rawData);
		}

		$xmlPatient = $xml->addChild('patient');
		$xmlPerson = $xmlPatient->addChild('person');
		$this->_addChild($xmlPerson,'picture',$picture,$checked);
		$this->_addChild($xmlPerson,'lastName',$person->lastName,$checked);
		$this->_addChild($xmlPerson,'firstName',$person->firstName,$checked);
		$this->_addChild($xmlPerson,'middleName',$person->middleName,$checked);
		$identifier = '';
		if ($person->identifierType == 'SSN') $identifier = $person->identifier;
		$this->_addChild($xmlPerson,'identifier',$identifier,$checked);
		$this->_addChild($xmlPerson,'gender',$person->gender,$checked);
		$dateOfBirth = explode(' ',date('m d Y',strtotime($person->dateOfBirth)));
		$this->_addChild($xmlPerson,'dobMonth',$dateOfBirth[0],$checked);
		$this->_addChild($xmlPerson,'dobDay',$dateOfBirth[1],$checked);
		$this->_addChild($xmlPerson,'dobYear',$dateOfBirth[2],$checked);
		$statistics = PatientStatisticsDefinition::getPatientStatistics($personId);
		$race = '';
		if (isset($statistics['Race'])) $race = $statistics['Race'];
		else if (isset($statistics['race'])) $race = $statistics['race'];
		$this->_addChild($xmlPerson,'race',$race,$checked);
		$maritalStatus = ($person->maritalStatus)?$person->maritalStatus:'Other';
		$this->_addChild($xmlPerson,'maritalStatus',$maritalStatus,$checked);

		$addresses = Address::listAddresses($personId);
		foreach ($addresses as $address) {
			switch ($address->type) {
				case Address::TYPE_MAIN:
					$type = 'mainAddress';
					break;
				case Address::TYPE_SEC:
					$type = 'secondaryAddress';
					break;
				case Address::TYPE_HOME:
					$type = 'homeAddress';
					break;
				case Address::TYPE_EMPLOYER:
					$type = 'employerAddress';
					break;
				case Address::TYPE_BILLING:
					$type = 'billingAddress';
					break;
				case Address::TYPE_OTHER:
				default:
					$type = 'otherAddress';
					break;
			}
			$xmlAddress = $xmlPatient->addChild($type);
			$this->_addChild($xmlAddress,'line1',$address->line1,$checked);
			$this->_addChild($xmlAddress,'city',$address->city,$checked);
			$this->_addChild($xmlAddress,'state',$address->state,$checked);
			$this->_addChild($xmlAddress,'zip',$address->postalCode,$checked);
		}

		$phoneNumbers = PhoneNumber::listPhoneNumbers($personId);
		foreach ($phoneNumbers as $phoneNumber) {
			switch ($phoneNumber->type) {
				case PhoneNumber::TYPE_HOME:
					$type = 'homePhone';
					break;
				case PhoneNumber::TYPE_WORK:
					$type = 'workPhone';
					break;
				case PhoneNumber::TYPE_BILLING:
					$type = 'billingPhone';
					break;
				case PhoneNumber::TYPE_EMPLOYER:
					$type = 'employerPhone';
					break;
				case PhoneNumber::TYPE_MOBILE:
					$type = 'mobilePhone';
					break;
				case PhoneNumber::TYPE_EMERGENCY:
					$type = 'emergencyPhone';
					break;
				case PhoneNumber::TYPE_FAX:
					$type = 'faxPhone';
					break;
				case PhoneNumber::TYPE_HOME_EVE:
					$type = 'homeEvePhone';
					break;
				case PhoneNumber::TYPE_HOME_DAY:
					$type = 'homeDayPhone';
					break;
				case PhoneNumber::TYPE_BEEPER:
					$type = 'beeperPhone';
					break;
				default:
					$type = 'otherPhone';
					break;
			}
			$xmlPhone = $xmlPatient->addChild($type);
			$this->_addChild($xmlPhone,'number',$phoneNumber->number,$checked);
		}

		if ($person->primaryPracticeId > 0) {
			$practice = new Practice();
			$practice->practiceId = (int)$person->primaryPracticeId;
			$practice->populate();
			$address = $practice->primaryAddress;
			$xmlPractice = $xmlPatient->addChild('practice');
			$this->_addChild($xmlPractice,'name',$practice->name,$checked);
			$xmlPrimaryAddress = $xmlPractice->addChild('primaryAddress');
			$this->_addChild($xmlPrimaryAddress,'line1',$address->line1,$checked);
			$this->_addChild($xmlPrimaryAddress,'city',$address->city,$checked);
			$this->_addChild($xmlPrimaryAddress,'state',$address->state,$checked);
			$this->_addChild($xmlPrimaryAddress,'zip',$address->postalCode,$checked);
			$this->_addChild($xmlPractice,'mainPhone',$practice->mainPhone->number,$checked);
			$this->_addChild($xmlPractice,'faxNumber',$practice->fax->number,$checked);
		}

		$insuredRelationship = new InsuredRelationship();
		$insuredRelationshipIterator = $insuredRelationship->getIteratorByPersonId($personId);
		$primary = null;
		$secondary = null;
		foreach ($insuredRelationshipIterator as $item) {
			if (!$item->active) continue;
			if ($primary === null) $primary = $item;
			else if ($secondary === null) $secondary = $item;
			else break;
		}

		$xmlPayer = $xmlPatient->addChild('payer');
		if ($primary !== null) $this->_addChild($xmlPayer,'medicareNumber',$primary->insuranceProgram->payerIdentifier,$checked);
		if ($secondary !== null) $this->_addChild($xmlPayer,'medicaidNumber',$secondary->insuranceProgram->payerIdentifier,$checked);

		return $xml;
	}

	public function hasMRNDuplicates() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table,'person_id')
				->where('record_number = ?',$this->record_number)
				->where('person_id != ?',(int)$this->person_id);
		$ret = false;
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		return $ret;
	}

}
