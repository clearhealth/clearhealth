<?php
/*****************************************************************************
*       Building.php
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


class Building extends WebVista_Model_ORM {
	protected $id;
	protected $description;
	protected $name;
	protected $practice_id;
	protected $practice;
	protected $identifier;
	protected $facility_code_id;
	protected $phone_number;
	protected $fax;
	protected $line1;
	protected $line2;
	protected $city;
	protected $state;
	protected $postalCode;
	protected $_table = 'buildings';
	protected $_primaryKeys = array('id');
	protected $_cascadePopulate = false; // disable to prevent assigning buildingId as practiceId since buildings.id != practices.id
	protected $_legacyORMNaming = true;
	protected $_cascadePersist = false;

	public function __construct() {
		parent::__construct();
		$this->practice = new Practice();
		$this->practice->_cascadePersist = false;
	}

	public function populate() {
		$ret = parent::populate();
		$this->practice->populate();
		return $ret;
	}

	public function setPractice_id($val) {
		$this->setPracticeId($val);
	}

	public function setPracticeId($val) {
		$this->practice_id = (int)$val;
		$this->practice->practiceId = $this->practice_id;
	}

	public function getBuildingId() {
		return $this->id;
	}

	public function setBuilding_id($val) {
		$this->setBuildingId($val);
	}

	public function setBuildingId($id) {
		$this->id = $id;
	}

	public static function getBuildingArray() {
		$ret = array();
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			 	->from(array('b'=>'buildings'))
			 	->join(array('p'=>'practices'),'p.id = b.practice_id')
				->columns(array('b.id AS id',"CONCAT(p.name,'->',b.name) AS name"));
		$data = $db->fetchAll($dbSelect);
		foreach ($data as $row) {
			$ret[$row['id']] = $row['name'];
		}
		return $ret;
	}

	public function ormEditMethod($ormId,$isAdd) {
		$controller = Zend_Controller_Front::getInstance();
		$request = $controller->getRequest();
		$enumerationId = (int)$request->getParam('enumerationId');

		$view = Zend_Layout::getMvcInstance()->getView();
		$params = array();
		if ($isAdd) {
			$params['parentId'] = $enumerationId;
			unset($_GET['enumerationId']); // remove enumerationId from params list
			$params['grid'] = 'enumItemsGrid';
			$params['ormClass'] = 'Room';
			return $view->action('edit','enumerations-manager',null,$params);
		}
		else {
			$params['enumerationId'] = $enumerationId;
			$params['id'] = $ormId;
			return $view->action('edit-building','facilities',null,$params);
		}
	}

	public function getZipCode() {
		return preg_replace('/[^0-9]*/','',$this->postalCode);
	}

	public function getPhoneNumbers() {
		$ret = array();
		$phoneNumber = PhoneNumber::autoFixNumber($this->phone_number); // TE
		$ret[] = array('number'=>$phoneNumber,'type'=>'TE');
		$fax = PhoneNumber::autoFixNumber($this->fax); // FX
		if (strlen($fax) > 0) {
			$ret[] = array('number'=>$fax,'type'=>'FX');
		}
		return $ret;
	}

	public static function getBuildingDefaultLocation($personId,$defaultLocationId=null) { // get default building given user's person id, if $defaultLocationId is defined then room is returned
		$user = new User();
		$user->personId = $personId;
		$user->populateWithPersonId();
		$user->populate();
		$building = null;
		$room = null;
		if (strlen($user->preferences) > 0) {
			$xmlPreferences = new SimpleXMLElement($user->preferences);
			$room = new Room();
			$room->roomId = (int)$xmlPreferences->currentLocation;
			if ($room->populate()) {
				$building = $room->building;
			}
		}
		if ($defaultLocationId === null) {
			if ($building === null) {
				$building = new Building();
				$building->buildingId = (int)$user->defaultBuildingId;
				$building->populate();
			}
			return $building;
		}
		else {
			if ($room === null) {
				$room = new Room();
				$room->roomId = (int)$defaultLocationId;
				$room->populate();
			}
			return $room;
		}
	}

	public function getDisplayName() {
		if (!strlen($this->practice->name) > 0 && $this->practice_id > 0) $this->practice->populate();
		return $this->practice->name.'->'.$this->name;
	}

	public function getIteratorByPracticeId($practiceId = null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($practiceId === null) $practiceId = $this->practice_id;
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('practice_id = ?',(int)$practiceId)
				->order('name');
		return $this->getIterator($sqlSelect);
	}

}
