<?php
/*****************************************************************************
*       MedicationIterator.php
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

 
class MedicationIterator extends WebVista_Model_ORMIterator implements Iterator {
 
	public function __construct($dbSelect = null) {
		parent::__construct('Medication',$dbSelect);
	}
 
	public function setFilter($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
				->from('medications')
				//->order('eSignatureId ASC')
				//->order('transmit DESC')
				->order('datePrescribed ASC');
		$where = array();
		$separator = 'OR';
		foreach ($filters as $filter => $value) {
			switch($filter) {
				case 'patientId':
					$dbSelect->where('personId = ?',(int)$value);
					break;
				case 'active':
					$operator = ((int)$value)?'!=':'=';
					$where[] = 'daysSupply '.$operator.' -1';
					if (isset($filters['patientReported']) && !$filters['patientReported']) $separator = 'AND';
					break;
				case 'dateRange':
					$dateRange = explode(';',$value);
					$start = isset($dateRange[0])?date('Y-m-d 00:00:00',strtotime($dateRange[0])):date('Y-m-d 00:00:00');
					$end = isset($dateRange[1])?date('Y-m-d 23:59:59',strtotime($dateRange[1])):date('Y-m-d 23:59:59',strtotime($start));
					$dbSelect->where("datePrescribed BETWEEN '{$start}' AND '{$end}'");
					break;
				case 'patientReported':
					if (!$value && isset($filters['active']) && !$filters['active']) $separator = 'AND';
					if (!isset($filters['active']) && isset($filters['flag']) && $value) break;
					$where[] = 'patientReported = '.(int)$value;
					break;
			}
		}
		if (isset($where[0])) $dbSelect->where(implode(' '.$separator.' ',$where));
		$this->_dbSelect = $dbSelect;
		trigger_error($this->_dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbStmt = $db->query($this->_dbSelect);
	}
}
