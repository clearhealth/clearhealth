<?php
/*****************************************************************************
*       ScheduleEventIterator.php
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


class ScheduleEventIterator extends WebVista_Model_ORMIterator {

	public function __construct($dbSelect = null,$autoLoad = true) {
		$this->_ormClass = 'ScheduleEvent';
		// autoLoad gives an option to query the entire rows which takes time if more data in the table
		if ($autoLoad) {
			parent::__construct($this->_ormClass,$dbSelect);
		}
	}

	public function current() {
		$row = $this->_dbStmt->fetch(PDO::FETCH_NUM,null,$this->_offset);
		if ($this->_offset == 0) {
			for($i=0,$ctr=count($row);$i<$ctr;$i++) {
				$this->_columnMeta[$i] = $this->_dbStmt->getColumnMeta($i);
			}
		}

		$col = 0;
		$colMetaLen = count($this->_columnMeta);
		$ormObj = new $this->_ormClass();
		$ormObj->populateWithArray($row);
		$orModels = array();
		$orModels[] = $ormObj;
		$orModels[] = $ormObj->provider;
		$orModels[] = $ormObj->room;

		$columnMeta = $this->_columnMeta;
		foreach ($orModels as $orm) {
			$data = array();
			foreach ($columnMeta as $i=>$meta) {
				if ($orm->_table == $meta['table']) {
					$data[$meta['name']] = $row[$i];
					unset($columnMeta[$i]);
				}
			}
			$orm->populateWithArray($data);
		}
		return $ormObj;
	}

	public function setFilters($filters) {
		$this->setFilter($filters);
	}

	public function setFilter($filter) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()->from('scheduleEvents');
		$dbSelect->joinLeft("provider","scheduleEvents.providerId=provider.person_id");
		if ((int)$filter['roomId'] > 0 && (int)$filter['providerId'] > 0) {
			$dbSelect->joinLeft("buildings","scheduleEvents.buildingId=buildings.id");
			$dbSelect->joinLeft("rooms","rooms.building_id=buildings.id");
			$dbSelect->where("rooms.id = ?",(int)$filter['roomId']);
			$dbSelect->where("providerId = ?",(int)$filter['providerId']);
		}
		else {
			$dbSelect->joinLeft("rooms","scheduleEvents.roomId=rooms.id");
			$dbSelect->where("roomId = ?",(int)$filter['roomId']);
			$dbSelect->where("providerId = ?",(int)$filter['providerId']);
		}
		if (isset($filter['start'])) {
			$dbSelect->where("start >= ?", $filter['start']);
		}
		if (isset($filter['end'])) {
			$dbSelect->where("end <= ?", $filter['end']);
		}
		$dbSelect->order("start ASC");
		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}
}
