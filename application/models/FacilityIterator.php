<?php
/*****************************************************************************
*       FacilityIterator.php
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


class FacilityIterator extends WebVista_Model_ORMIterator implements Iterator {

	protected $_rows = array();

	const ENUM_NAME = 'Facilities';

	public function __construct() {
	}

	public function setFilter($filters) {
		return $this->setFilters($filters);
	}

	public function setFilters(Array $filters) {
		if (!is_array($filters) || empty($filters)) {
			throw new Exception(__('Invalid filters'));
		}
		$filtersArray = array();
		foreach ($filters as $filter) {
			if (!class_exists($filter)) {
				$msg = __("Filter {$filter} does not exists");
				throw new Exception($msg);
			}
			$class = new $filter();
			if (!$class instanceof WebVista_Model_ORM) {
				$msg = __("Filter {$filter} is not an instance of WebVista_Model_ORM");
				throw new Exception($msg);
			}
			$filtersArray[$filter] = $filter;
		}
		$this->_rows = array();
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName(self::ENUM_NAME);
		$enumerationClosure = new EnumerationClosure();
		if (isset($filtersArray['Practice'])) {
			$practices = $enumerationClosure->getAllDescendants($enumeration->enumerationId,1);
			foreach ($practices as $enumPractice) {
				if (!$enumPractice->ormId > 0 || $enumPractice->ormClass != 'Practice') continue;
				$practice = new Practice();
				$practice->practiceId = $enumPractice->ormId;
				$practice->populate();
				if (isset($filtersArray['Building'])) {
					$buildings = $enumerationClosure->getAllDescendants($enumPractice->enumerationId,1);
					foreach ($buildings as $enumBuilding) {
						if (!$enumBuilding->ormId > 0 || $enumBuilding->ormClass != 'Building') continue;
						$building = new Building();
						$building->buildingId = $enumBuilding->ormId;
						$building->populate();
						if (isset($filtersArray['Room'])) {
							$rooms = $enumerationClosure->getAllDescendants($enumBuilding->enumerationId,1);
							foreach ($rooms as $enumRoom) {
								if (!$enumRoom->ormId > 0 || $enumRoom->ormClass != 'Room') continue;
								$room = new Room();
								$room->roomId = $enumRoom->ormId;
								$room->populate();
								$this->_rows[] = array('Practice'=>$practice,'Building'=>$building,'Room'=>$room);
							}
						}
						else {
							$this->_rows[] = array('Practice'=>$practice,'Building'=>$building);
						}
					}
				}
				else {
					$this->_rows[] = $practice;
				}
			}
		}
		else if (isset($filtersArray['Building'])) {
			$buildings = $enumerationClosure->getAllDescendants($enumPractice->enumerationId,1);
			foreach ($buildings as $enumBuilding) {
				if (!$enumBuilding->ormId > 0 || $enumBuilding->ormClass != 'Building') continue;
				$building = new Building();
				$building->buildingId = $enumBuilding->ormId;
				$building->populate();
				if (isset($filtersArray['Room'])) {
					$rooms = $enumerationClosure->getAllDescendants($enumBuilding->enumerationId,1);
					foreach ($rooms as $enumRoom) {
						if (!$enumRoom->ormId > 0 || $enumRoom->ormClass != 'Room') continue;
						$room = new Room();
						$room->roomId = $enumRoom->ormId;
						$room->populate();
						$this->_rows[] = array('Building'=>$building,'Room'=>$room);
					}
				}
				else {
					$this->_rows[] = $building;
				}
			}
		}
		else if (isset($filtersArray['Room'])) {
			$rooms = $enumerationClosure->getAllDescendants($enumBuilding->enumerationId,1);
			foreach ($rooms as $enumRoom) {
				if (!$enumRoom->ormId > 0 || $enumRoom->ormClass != 'Room') continue;
				$room = new Room();
				$room->roomId = $enumRoom->ormId;
				$room->populate();
				$this->_rows[] = $room;
			}
		}
		return $this;
	}

	public function __destroy() {
	}

	public function first() {
		return isset($this->_rows[0])?$this->_rows[0]:null;
	}

	public function valid() {
		return isset($this->_rows[$this->_offset]);
	}

	public function current() {
		return $this->_rows[$this->_offset];
	}

}
