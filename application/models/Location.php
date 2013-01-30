<?php
/*****************************************************************************
*       Location.php
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


class Location extends WebVista_Model_ORM {
	protected $locationId;
	protected $name;
	protected $_table = "locations";
	protected $_primaryKeys = array('locationId');
	
	public function __construct() {
		parent::__construct();
	}
	
	static public function getIterByLocationId($locationId) {
		$locationId = (int) $locationId;
		$location = new Location();
		$db = Zend_Registry::get('dbAdapter');
		$locSelect = $db->select()
			->from($location->_table)
			->where('parentId = ' . $locationId);
		$iter = $location->getIterator($locSelect);
		return $iter;
		
	}

	static public function getIterByLocationName($name) {
                $location = new Location();
                $db = Zend_Registry::get('dbAdapter');
                $locSelect = $db->select()
                        ->from($location->_table)
                        ->where('lft > (select lft from locations where name=' . $db->quote($name) .')')
                        ->where('rgt < (select rgt from locations where name=' . $db->quote($name) .')')
			->order($location->_table . '.lft ASC ');
                $iter = $location->getIterator($locSelect);
                return $iter;

        }
	static public function getIterByLocationType($type) {
                $location = new Location();
                $db = Zend_Registry::get('dbAdapter');
                $locSelect = $db->select()
                        ->from($location->_table)
                        ->where('type = '.  $db->quote($type))
                        ->order($location->_table . '.lft ASC ');
                $iter = $location->getIterator($locSelect);
                return $iter;

        }
	static public function getLocationArray($name = "Installation",$key = "locationId", $value = "name") {
                $iter = Location::getIterByLocationName($name);
                return $iter->toArray($key, $value);

        }
	static public function getLocationArrayByType($name = "INSTALLATION",$key = "locationId", $value = "name") {
                $iter = Location::getIterByLocationType($name);
                return $iter->toArray($key, $value);

        }
}
