<?php
/*****************************************************************************
*       Room.php
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


class Room extends WebVista_Model_ORM {
	protected $id;
	protected $description;
	protected $number_seats;
	protected $building_id;
	protected $building;
	protected $name;
	protected $color;
	protected $routing_station;

	protected $_table = 'rooms';
	protected $_primaryKeys = array('id');
	protected $_cascadePopulate = false; // disable to prevent assigning roomId as buildingId since rooms.id != buildings.id
	protected $_legacyORMNaming = true;
	protected $_cascadePersist = false;

	const ENUM_COLORS_NAME = 'Color Preferences';

	public function __construct() {
		parent::__construct();
		$this->_init();
	}

	protected function _init() {
		$this->building = new Building();
		$this->building->_cascadePersist = false;
	}

	public function populate() {
		$ret = parent::populate();
		if ($this->building->buildingId > 0) {
			$this->building->populate();
		}
		else {
			$this->_init();
		}
		return $ret;
	}

	public function setBuilding_id($val) {
		$this->setBuildingId($val);
	}

	public function setBuildingId($val) {
		$this->building_id = (int)$val;
		$this->building->buildingId = $this->building_id;
	}

	public function getRoomId() {
		return $this->id;
	}

	public function setRoom_id($id) {
		$this->setRoomId($id);
	}

	public function setRoomId($id) {
		$this->id = $id;
	}

	public static function getArray() {
		$ret = array();
		$provider = new Provider();
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
				->from('rooms', array('id', 'name'))
				->order('rooms.name ASC');
		$data = $db->fetchAll($dbSelect);
		foreach ($data as $row) {
			$ret[$row['id']] = $row['name'];
		}
		return $ret;
	}

	public static function getRoomArray() {
		$ret = array();
		$provider = new Provider();
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			 	->from(array('r'=>'rooms'),null)
			 	->join(array('e'=>'enumerations'),"(e.ormId = r.id AND e.ormClass = 'Room' AND e.active = 1)",null)
			 	->join(array('b'=>'buildings'),'b.id = r.building_id',null)
			 	->join(array('p'=>'practices'),'p.id = b.practice_id',null)
				->columns(array('r.id AS id',"CONCAT(p.name,'->',b.name,'->',r.name) AS name"));
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

		$params = array();
		if ($isAdd) {
			$params['parentId'] = $enumerationId;
			unset($_GET['enumerationId']); // remove enumerationId from params list
			$params['grid'] = 'enumItemsGrid';
			return $view->action('edit','enumerations-manager',null,$params);
		}
		else {
			$params['enumerationId'] = $enumerationId;
			$params['id'] = $ormId;
			$view = Zend_Layout::getMvcInstance()->getView();
			return $view->action('edit-room','facilities',null,$params);
		}
	}

	public static function getColorList() {
		$name = self::ENUM_COLORS_NAME;
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		$enumeration->populate();
		$enumerationsClosure = new EnumerationsClosure();
		$descendants = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$colors = array();
		foreach ($descendants as $descendant) {
			$colors[$descendant->key] = $descendant->name;
		}
		return $colors;
	}

	public static function location($roomId,$raw=false) {
		$location = '';
		if ($raw) {
			$location = array(
				'practice'=>'',
				'building'=>'',
				'room'=>'',
			);
		}
		$roomId = (int)$roomId;
		if ($roomId > 0) {
			$room = new self();
			$room->roomId = (int)$roomId;
			$room->populate();
			if ($raw) {
				$location['practice'] = $room->building->practice->name;
				$location['building'] = $room->building->name;
				$location['room'] = $room->name;
			}
			else {
				$location = $room->building->practice->name.'->'.$room->building->name.'->'.$room->name;
			}
		}
		return $location;
	}

	public function getDisplayName() {
		if (!strlen($this->building->name) > 0 && $this->building_id > 0) $this->building->populate();
		return $this->building->displayName.'->'.$this->name;
	}

	public function getIteratorByBuildingId($buildingId = null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($buildingId === null) $buildingId = $this->building_id;
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('building_id = ?',(int)$buildingId)
				->order('name');
		return $this->getIterator($sqlSelect);
	}

}
