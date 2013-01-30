<?php
/*****************************************************************************
*       Person.php
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


class Person extends WebVista_Model_ORM implements NSDRMethods {
	protected $person_id;
	protected $salutation;
	protected $last_name;
	protected $first_name;
	protected $suffix;
	protected $middle_name;
	protected $gender;
	protected $initials;
	protected $date_of_birth;
	protected $summary;
	protected $title;
	protected $notes;
	protected $email;
	protected $secondary_email;
	protected $has_photo;
	protected $identifier;
	protected $identifier_type;
	protected $marital_status;
	protected $inactive;
	protected $active;
	protected $primary_practice_id;
	protected $activePhoto;
	protected $_table = "person";
	protected $_primaryKeys = array("person_id");
	protected $_legacyORMNaming = true;
	public static $_nsdrNamespace = 'com.clearhealth.person';	

	public function __construct() {
		parent::__construct();
	}

	public function getMiddleInitial() {
		if (strlen($this->middle_name) > 0) {
			return substr($this->middle_name,0,1);
		}
		return "";
	}

	public function getDisplayName() {
		return $this->last_name . ", " . $this->first_name . " " . $this->middle_name;
	}
	public static function getControllerName() {
		return "ProviderDashboardController";
	}

	public function getDisplayGender() {
		$gender = $this->gender;
		switch ($gender) {
			case '1':
				$gender = 'M';
				break;
			case '2':
				$gender = 'F';
				break;
			case '3':
			case 'O':
				$gender = 'U';
				break;
		}
		return $gender;
	}

	function getAge() {
                if ($this->date_of_birth == '0000-00-00') return '';
		return self::calculateAge($this->date_of_birth);
		$now = time();
		$dob = strtotime($this->date_of_birth);
                $age = ($dob < 0)? ($now + ($dob * -1)): $now-$dob;
		$year = 60*60*24*365;
		$age = floor($age/$year);
		return $age;
        }

	public static function calculateAge($dateOfBirth) {
		list($bYear,$bMonth,$bDay) = explode('-',date('Y-m-d',strtotime($dateOfBirth)));
		list($cYear,$cMonth,$cDay) = explode('-',date('Y-m-d'));
		return (($cMonth >= $bMonth && $cDay >= $bDay) || ($cMonth > $bMonth))?($cYear - $bYear):($cYear - $bYear - 1);
	}

	public function nsdrPersist($tthis,$context,$data) {
		$ret = false;
		//debug_print_backtrace();
		$context = (int)$context;
		if ($context > 0) {
			$this->personId = $context;
			$this->populate();
		}

		$array = array();
		if (is_array($data) && !array_key_exists(0,$data)) { // assign data to array only if data does not contains an index 0
			$array = $data;
		}
		if (preg_match('/.*'.$this->_nsdrNamespace.'\.([a-zA-Z0-9]+)/',$tthis->_aliasedNamespace,$matches) && isset($matches[1])) {
			if ($this->_legacyORMNaming == true && strpos($matches[1],'_') === false) {
				$newKey = strtolower(preg_replace('/([A-Z]{1})/','_\1',$matches[1]));
				if (strpos($newKey,'_') !== false && in_array($newKey,$this->ORMFields())) {
					if (is_array($data) && array_key_exists(0,$data)) { // extract only one value, discard the rest
						$data = $data[0];
					}
					$array[$newKey] = $data;
				}
			}
		}
		$this->populateWithArray($array);
		$this->persist();
		return true;
	}
	public function nsdrMostRecent($tthis,$context,$data) {
		$msg = __('Most recent not implemented for this ORM: Person');
                throw new Exception($msg);
	}

	public function nsdrPopulate($tthis,$context,$data) {
		$ret = array();
		//debug_print_backtrace();
		$context = (int)$context;
		if ($context > 0) {
			$this->personId = $context;
			if (!$this->populate()) {
				//throw error, populate failed with supplied non-zero context
				$msg = __('populate failed with supplied non-zero context');
				throw new Exception($msg);
			}
			if (preg_match('/.*'.$this->_nsdrNamespace.'\.([a-zA-Z0-9]+)/',$tthis->_aliasedNamespace,$matches)) {
				if (isset($matches[1]) && $this->$matches[1] !== null) {
					return $this->$matches[1];
				}
				else {
					$msg = __('Populate failed, request namespace: ' . $tthis->_aliasedNamespace . " item: '" . $matches[1] . "' could not be answered by this namespace: " . $this->_nsdrNamespace);
					// temporarily comment out... namespace request could be com.clearhealth.person
					//throw new Exception($msg);
				}
			}
			$ret = $this->toArray();
		}
		return $ret;
        }

	public static function checkDuplicatePerson(self $person) {
		$db = Zend_Registry::get('dbAdapter');
		$lastName = $person->lastName;
		$firstName = $person->firstName;
		$firstInitial = substr($firstName,0,1);
		$gender = $person->gender;
		$dob = $person->dateOfBirth;

		$sqlSelect = $db->select()
				->from($person->_table) //,array('person_id','last_name','first_name','middle_name','gender','date_of_birth','identifier','identifier_type'))
				->where('last_name LIKE '.$db->quote($lastName).' OR last_name LIKE '.$db->quote($lastName.'%'))
				->where('first_name LIKE '.$db->quote($firstName).' OR first_name LIKE '.$db->quote($firstName.'%').' OR (SUBSTRING(first_name,1,1) LIKE '.$db->quote($firstInitial). ' AND gender='.$db->quote($gender).') OR date_of_birth='.$db->quote($dob))
				->order('last_name')
				->order('first_name')
				->order('middle_name')
				->order('date_of_birth');
		$duplicates = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$p = new self();
				$p->populateWithArray($row);
				$tmp = array();
				$tmp['personId'] = $p->personId;
				$tmp['name'] = $p->displayName;
				$tmp['dateOfBirth'] = $p->dateOfBirth;
				$tmp['gender'] = $p->displayGender;
				$tmp['ssn'] = $p->identifier;
				$duplicates[] = $tmp;
			}
		}
		return $duplicates;
	}

	public static function getListIdentifierTypes() {
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName('Identifier Type');

		$enumerationsClosure = new EnumerationsClosure();
		$ret = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1)->toArray('key','name');
		return $ret;
	}

	public function getGender() { // 2.x to 3.x conversion
		static $detailsView2x = null;
		$gender = $this->gender;
		if ($detailsView2x === null) {
			$config = Zend_Registry::get('config');
			$detailsView2x = strtolower((string)$config->patient->detailsView2x);
		}
		if ($detailsView2x != 'true') {
			return $gender;
		}
		switch ($gender) {
			case '1':
				$gender = 'M';
				break;
			case '2':
				$gender = 'F';
				break;
			case '3':
			case 'O':
			default:
				$gender = 'U';
				break;
		}
		return $gender;
	}

	public function getPhoneNumber($autoFix=true) {
		if ($this->person_id > 0)
		foreach ($this->getPhoneNumbers($autoFix) as $phoneNumber) {
			break;
		}
		if (!isset($phoneNumber)) {
			$phoneNumber = new PhoneNumber();
			$phoneNumber->personId = $this->person_id;
			$phoneNumber->populateWithPersonId();
		}
		return $phoneNumber;
	}

	public function getPhoneNumbers($autoFix=true) {
		$phoneNumber = new PhoneNumber();
		$phoneNumber->personId = $this->person_id;
		return $phoneNumber->getIteratorByPersonId();
	}

	public function getAddress() {
		if ($this->person_id > 0)
		foreach ($this->getAddresses() as $address) {
			break; // retrieves the top address
		}
		if (!isset($address)) {
			$address = new Address();
			$address->personId = $this->person_id;
			$address->populateWithPersonId();
		}
		return $address;
	}

	public function getAddresses() {
		$address = new Address();
		$address->personId = $this->person_id;
		return $address->getIteratorByPersonId();
	}

	public function getAgeTitle() {
		$dateOfBirth = $this->date_of_birth;
		if ($dateOfBirth == '' || $dateOfBirth == '0000-00-00') return '';

		list($bYear,$bMonth,$bDay) = explode('-',date('Y-m-d',strtotime($dateOfBirth)));
		list($cYear,$cMonth,$cDay) = explode('-',date('Y-m-d'));
		$numOfDays = cal_days_in_month(CAL_GREGORIAN,$bMonth,$bYear);

		$y = $cYear - $bYear;
		$m = $cMonth - $bMonth;
		$d = $cDay - $bDay;
		if ($y > 0 || ($y == 0 && ($m > 0 || ($m == 0 && $d > 0)))) {
			if ($m < 0) {
				$y--;
				$m += 12;
			}
			if ($d < 0) {
				$m--;
				$d += $numOfDays;
			}
		}
		$m += $y * 12;

		$ret = $d.' day';
		if ($d > 1) $ret .= 's';
		$ret .= ', '.$m.' month';
		if ($m > 1) $ret .= 's';
		return $ret;
	}

	public function getPhoto() {
		$picture = '';
		if ($this->activePhoto > 0) {
			$attachment = new Attachment();
			$attachment->attachmentId = (int)$this->activePhoto;
			$attachment->populate();
			$picture = base64_encode($attachment->rawData);
		}
		return $picture;
	}
 
	public function getDisplayMaritalStatus() {
		static $maritalStatuses = null;
		if ($maritalStatuses === null) $maritalStatuses = Enumeration::getEnumArray('Marital Status','key');
		$maritalStatus = $this->marital_status;
		if (isset($maritalStatuses[$maritalStatus])) $maritalStatus = $maritalStatuses[$maritalStatus];
		return $maritalStatus;
	}

}
