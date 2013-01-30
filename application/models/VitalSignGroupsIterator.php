<?php
/*****************************************************************************
*       VitalSignGroupsIterator.php
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


class VitalSignGroupsIterator extends WebVista_Model_ORMIterator implements Iterator {

	private $_currentGroupId = null;
	private $_currentRow = null;

	public function __construct($dbSelect = null) {
		parent::__construct('VitalSignGroup',$dbSelect);
	}

	public function setFilter(Array $filter) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from("vitalSignGroups")
				->join("vitalSignValues","vitalSignValues.vitalSignGroupId = vitalSignGroups.vitalSignGroupId")
				->order("vitalSignGroups.dateTime DESC")
				->order("vitalSignValues.vital ASC");
		foreach ($filter as $key=>$value) {
			switch ($key) {
				case 'dateRange':
					$dateRange = explode(';',$value);
					$start = isset($dateRange[0])?date('Y-m-d 00:00:00',strtotime($dateRange[0])):date('Y-m-d 00:00:00');
					$end = isset($dateRange[1])?date('Y-m-d 23:59:59',strtotime($dateRange[1])):date('Y-m-d 23:59:59',strtotime($start));
					$dbSelect->where("dateTime BETWEEN '{$start}' AND '{$end}'");
					break;
				case 'personId':
					$dbSelect->where('personId = ?',(int)$value);
					break;
			}
		}
		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

	public function current() {
		$ormObj = new $this->_ormClass();
		//echo $this->_dbSelect->__toString();exit;
		//echo $this->_offset . "offset<br>\n";
		if (isset($this->_currentRow)) {
			$row = $this->_currentRow;
			$this->_currentRow = null;
			$this->_offset--;
		}
		else  {
			$row = $this->_dbStmt->fetch(null,null,$this->_offset);
		}
		if (is_null($this->_currentGroupId)) {
			$this->_currentGroupId = $row['vitalSignGroupId'];
		}
		//echo "start: " . $this->_currentGroupId . "<br>";
		//echo "start: " . $row['vital'] . "<br>";
		$ormObj->populateWithArray($row);
		$values = array();
		$vitalSignValue = new VitalSignValue();
		$vitalSignValue->populateWithArray($row);
		$values[] = $vitalSignValue;
		while ($this->_offset+1 < $this->_dbStmt->rowCount()) {
			$row = $this->_dbStmt->fetch(null,null,$this->_offset);
			$this->_offset++;
			$this->_currentRow = $row;
			if ($row['vitalSignGroupId'] === $this->_currentGroupId) {
				$vitalSignValue = new VitalSignValue();
				$vitalSignValue->populateWithArray($row);
				$values[] = $vitalSignValue;
			}
			else {
				break;
			}
		}
		$this->_currentGroupId = null;
		$ormObj->setVitalSignValues($values);
		//echo $ormObj->toString();
		return $ormObj;
	}

}
