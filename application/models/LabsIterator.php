<?php
/*****************************************************************************
*       LabsIterator.php
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


class LabsIterator extends WebVista_Model_ORMIterator implements Iterator {

	protected $_columnMeta = array();

	public function __construct($dbSelect = null) {
		if ($dbSelect === null) {
			$db = Zend_Registry::get('dbAdapter');
			$dbSelect = $db->select()
				       ->from('lab_result')
				       ->joinLeft('lab_test','lab_test.lab_test_id=lab_result.lab_test_id')
				       ->joinLeft('lab_order','lab_order.lab_order_id=lab_test.lab_order_id')
				       ->order('lab_result.observation_time DESC')
					->where('0');
		}
		parent::__construct("LabResult",$dbSelect);
	}

	public function setFilters($filters) {
		if (empty($filters)) {
			throw new Exception(__('Filter must not be empty'));
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from('lab_result')
			       ->joinLeft('lab_test','lab_test.lab_test_id=lab_result.lab_test_id')
			       ->joinLeft('lab_order','lab_order.lab_order_id=lab_test.lab_order_id')
			       ->order('lab_result.observation_time DESC');
		foreach ($filters as $filter => $val) {
			switch($filter) {
				case 'patientId':
					$dbSelect->where('lab_order.patient_id = ?',(int)$val);
					break;
				case 'orderId':
					$dbSelect->where('lab_order.lab_order_id = ?',(int)$val);
					break;
				case 'dateEnd':
				if (strtotime($val) > 100000 && $val != '*') {
					$dateBegin = date('Y-m-d H:i:s',strtotime($filters['dateBegin']));
					if ($filters['dateBegin'] == $val) {
						// date range are the same
						$dateEnd = date('Y-m-d H:i:s',strtotime("+1 day",strtotime($filters['dateEnd'])));
					}
					else {
						$dateEnd = date('Y-m-d H:i:s',strtotime($val));
					}
					$dbSelect->where("lab_result.observation_time BETWEEN '{$dateBegin}' AND '{$dateEnd}'");
				}
				break;
				case 'description':
					$dbSelect->where('lab_result.description like ?',$val);
					break;
				case 'limit':
					$dbSelect->limit((int)$val);
					break;
				case 'dateRange':
					$dateRange = explode(';',$val);
					$start = isset($dateRange[0])?date('Y-m-d 00:00:00',strtotime($dateRange[0])):date('Y-m-d 00:00:00');
					$end = isset($dateRange[1])?date('Y-m-d 23:59:59',strtotime($dateRange[1])):date('Y-m-d 23:59:59',strtotime($start));
					$dbSelect->where("lab_result.observation_time BETWEEN '{$start}' AND '{$end}'");
					break;
				case 'orders':
					foreach ($val as $order) $dbSelect->order("{$order[0]} {$order[1]}");
					break;
			}
		}
		//echo $dbSelect->__toString();exit;
		//trigger_error($dbSelect->__toString(),E_USER_WARNING);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
		return $this;
	}

	public function current() {
		$className = $this->_ormClass;
		$ormObj = new $className();
		$data = array();
		$row = $this->_dbStmt->fetch(Zend_Db::FETCH_NUM,null,$this->_offset);
		if ($this->_offset == 0) {
			for($i=0,$ctr=count($row); $i<$ctr;$i++) {
				$this->_columnMeta[$i] = $this->_dbStmt->getColumnMeta($i);
			}
		}

		$labResult = array();
		$labTest = array();
		$labOrder = array();
		$ctr = count($this->_columnMeta);
		for ($i=0; $i<$ctr;$i++) {
			if ($this->_columnMeta[$i]['table'] == $ormObj->_table) {
				$labResult[$this->_columnMeta[$i]['name']] = $row[$i];
			}
			else if ($this->_columnMeta[$i]['table'] == $ormObj->labTest->_table) {
				$labTest[$this->_columnMeta[$i]['name']] = $row[$i];
			}
			else if ($this->_columnMeta[$i]['table'] == $ormObj->labTest->labOrder->_table) {
				$labOrder[$this->_columnMeta[$i]['name']] = $row[$i];
			}
		}
		$ormObj->populateWithArray($labResult);
		$ormObj->labTest->populateWithArray($labTest);
		$ormObj->labTest->labOrder->populateWithArray($labOrder);
		return $ormObj;
	}

}
