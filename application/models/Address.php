<?php
/*****************************************************************************
*       Address.php
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


class Address extends WebVista_Model_ORM {

	protected $address_id;
	protected $person_id;
	protected $name;
	protected $type;
	protected $active;
	protected $line1;
	protected $line2;
	protected $city;
	protected $region;
	protected $county;
	protected $state;
	protected $postal_code;
	protected $notes;
	protected $practiceId;
	protected $displayOrder;
	protected $_table = 'address';
	protected $_primaryKeys = array('address_id');
	protected $_legacyORMNaming = true;

	const ENUM_STATES_NAME = 'States';
	const ENUM_COUNTRIES_NAME = 'Countries';

	const TYPE_MAIN = 'MAIN';
	const TYPE_SEC = 'SEC';
	const TYPE_HOME = 'HOME';
	const TYPE_EMPLOYER = 'EMPL';
	const TYPE_BILLING = 'BILL';
	const TYPE_OTHER = 'OTHER';

	public function populateWithPersonId() {
		$db = Zend_Registry::get('dbAdapter');
		//address_type 4 is main
		$sql = "SELECT * from " . $this->_table  
			." INNER JOIN person_address per2add on per2add.address_id = address.address_id WHERE 1 and per2add.address_type = 4  and per2add.person_id = " . (int) $db->quote($this->person_id);
		$this->populateWithSql($sql);
	}

	public function getPrintState() {
		$db = Zend_Registry::get('dbAdapter');
		$sql = "select * from enumeration_definition enumDef 
			inner join enumeration_value enumVal on enumVal.enumeration_id = enumDef.enumeration_id
			where enumDef.name = 'state' and enumVal.key = " . (int) $this->state;
		$ret = '';
		if ($row = $db->query($sql)->fetchAll()) {
			$ret = $row[0]['value'];
		}
		return $ret;
	}

	public function getDisplayCounty() {
		$enumeration = new Enumeration();
		$enumeration->populateByFilter('key',$this->county);
		$ret = '';
		if (strlen($enumeration->name) > 0) {
			$ret = $enumeration->name;
		}
		return $ret;
	}

	public function getDisplayState() {
		$enumeration = new Enumeration();
		$enumeration->populateByFilter('key',$this->state);
		$ret = '';
		if (strlen($enumeration->name) > 0) {
			$ret = $enumeration->name;
		}
		return $ret;
	}

	public static function getCountriesList() {
		$name = self::ENUM_COUNTRIES_NAME;
		$enumerationIterator = self::_getEnumerationIterator($name);
		$ret = array();
		foreach ($enumerationIterator as $enumeration) {
			$ret[$enumeration->key] = $enumeration->name;
		}
		return $ret;
	}

	public static function getStatesList() {
		$name = self::ENUM_STATES_NAME;
		$enumerationIterator = self::_getEnumerationIterator($name);
		$ret = array();
		foreach ($enumerationIterator as $enumeration) {
			$ret[$enumeration->key] = $enumeration->name;
		}
		return $ret;
	}

	protected static function _getEnumerationIterator($name) {
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		$enumeration->populate();

		$enumerationsClosure = new EnumerationsClosure();
		return $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
	}

	public function populateWithPracticeIdType($practiceId=null,$type=null) {
		if ($practiceId === null) {
			$practiceId = (int)$this->practiceId;
		}
		if ($type === null) {
			$type = $this->getType();
		}
		if (!$practiceId > 0) return false;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('practiceId = ?',(int)$practiceId)
				->where('type = ?',$type)
				->limit(1);
		return $this->populateWithSql($sqlSelect->__toString());
	}

	public function populateWithType($type,$forced=false) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('person_id = ?',(int)$this->person_id)
//				->where('type = ?',$type) // temporarily comment out
				->where('active = 1')
				->limit(1);
		if ($forced) {
			$sqlSelect->where('type = ?',$type);
		}
		$this->populateWithSql($sqlSelect->__toString());
	}

	public static function getListAddressTypes() {
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName('Contact Preferences');

		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ret = array();
		foreach ($enumerationIterator as $enum) {
			if ($enum->name != 'Address Types') continue;
			$ret = $enumerationsClosure->getAllDescendants($enum->enumerationId,1)->toArray('key','name');
			break;
		}
		return $ret;
	}

	public static function nextDisplayOrder($personId) {
		$orm = new self();
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($orm->_table,'MAX(displayOrder) AS displayOrder')
				->where('person_id = ?',(int)$personId);
		$ret = 1;
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = $row['displayOrder'] + 1;
		}
		return $ret;
	}

	protected function _isv2x($addressId=null) {
		$ret = false;
		$config = Zend_Registry::get('config');
		if (strtolower($config->patient->detailsView2x) != 'true') {
			return $ret;
		}
		if ($addressId === null) {
			$addressId = $this->address_id;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('person_address')
				->where('address_id = ?',(int)$addressId);
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		return $ret;
	}

	public function persist() {
		if ($this->address_id > 0 && $this->_isv2x()) { // check if 2.x
			WebVista::debug('Unable to edit because 2.x address detected');
			return false;
		}
		if ($this->_persistMode != WebVista_Model_ORM::DELETE && (int)$this->displayOrder <= 0) {
			$this->displayOrder = self::nextDisplayOrder($this->personId);
		}
		return parent::persist();
	}

	public function getZipCode() {
		return preg_replace('/[^0-9]*/','',$this->postal_code);
	}

	public function getIteratorByPersonId($personId=null) {
		if ($personId === null) {
			$personId = $this->person_id;
		}
		$config = Zend_Registry::get('config');
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from(array('a'=>$this->_table))
				->order('a.displayOrder ASC');
		$orWhere = '';
		if (strtolower($config->patient->detailsView2x) == 'true') {
			$sqlSelect->joinLeft(array('pa'=>'person_address'),'pa.address_id=a.address_id',array('address_type'));
			$orWhere = ' OR pa.person_id = '.$personId;
		}
		$personId = (int)$personId;
		$sqlSelect->where('a.person_id = '.$personId.$orWhere);
		return new AddressIterator($sqlSelect);
	}

	public function getType() { // 2.x to 3.x conversion
		static $detailsView2x = null;
		$type = $this->type;
		if ($detailsView2x === null) {
			$config = Zend_Registry::get('config');
			$detailsView2x = strtolower((string)$config->patient->detailsView2x);
		}
		if ($detailsView2x != 'true') {
			return $type;
		}
		switch ($type) {
			case '1': // mailing
				$type = self::TYPE_BILLING;
				break;
			case '2': // home
				$type = self::TYPE_HOME;
				break;
			case '3': // other
				$type = self::TYPE_OTHER;
				break;
			case '4': // main
				$type = self::TYPE_MAIN;
				break;
			case '5': // secondary
				$type = self::TYPE_SEC;
				break;
			case '6': // employer
				$type = self::TYPE_EMPLOYER;
				break;
			case '7': // employer2
				$type = self::TYPE_EMPLOYER;
				break;
		}
		return $type;
	}

	public function getState() {
		static $detailsView2x = null;
		static $stateList = array(
			'1'=>'AL', '2'=>'AK', '3'=>'AZ', '4'=>'AR', '5'=>'CA',
			'6'=>'CO', '7'=>'CT', '8'=>'DE', '9'=>'DC', '10'=>'FL',
			'11'=>'GA', '12'=>'HI', '13'=>'ID', '14'=>'IL', '15'=>'IN',
			'16'=>'IA', '17'=>'KS', '18'=>'KY', '19'=>'LA', '20'=>'ME',
			'21'=>'MD', '22'=>'MA', '23'=>'MI', '24'=>'MN', '25'=>'MS',
			'35'=>'ND',
			'36'=>'OH', '37'=>'OK', '38'=>'OR', '39'=>'PA', '40'=>'RI',
			'41'=>'SC', '42'=>'SD', '43'=>'TN', '44'=>'TX', '45'=>'NV',
			'46'=>'VT', '47'=>'VA', '48'=>'WA', '49'=>'WV', '50'=>'WI',
			'51'=>'WY', '52'=>'PR', '53'=>'MO',
		);
		$state = $this->state;
		if ($detailsView2x === null) {
			$config = Zend_Registry::get('config');
			$detailsView2x = strtolower((string)$config->patient->detailsView2x);
		}
		if ($detailsView2x != 'true') {
			return $state;
		}
		if (isset($stateList[$state])) {
			$state = $stateList[$state];
		}
		return $state;
	}

	public static function listAddresses($personId) {
		$ret = array();
		$orm = new self();
		$orm->personId = (int)$personId;
		foreach ($orm->getIteratorByPersonId() as $row) {
			$ret[$row->type] = $row;
		}
		return $ret;
	}

}
