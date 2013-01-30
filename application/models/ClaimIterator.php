<?php
/*****************************************************************************
*       ClaimIterator.php
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


class ClaimIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null,$autoLoad = false) {
		$this->_ormClass = 'Claim';
		// autoLoad gives an option to query the entire rows which takes time if more data in the table
		if ($autoLoad) {
			parent::__construct($this->_ormClass,$dbSelect);
		}
	}

	public function setFilters(Array $filters) {
		$db = Zend_Registry::get('dbAdapter');
		$ormClass = $this->_ormClass;
		$orm = new $ormClass();
		$sqlSelect = $db->select()
				->from($orm->_table);
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'DOSDateRange':
					$start = date('Y-m-d 00:00:00',strtotime($value['start']));
					$end = date('Y-m-d 23:59:59',strtotime($value['end']));
					$sqlSelect->where('dateDOS BETWEEN '.$db->quote($start).' AND '.$db->quote($end));
					break;
				case 'facility':
					$sqlSelect->where('practiceId = ?',(int)$value['practice'])
						->where('buildingId = ?',(int)$value['building'])
						->where('roomId = ?',(int)$value['room']);
					break;
				case 'insurer':
					$sqlSelect->where('insuranceProgramId = ?',(int)$value);
					break;
				case 'total':
					$sqlSelect->where('total = ?',(float)$value);
					break;
				case 'paid':
					$sqlSelect->where('paid = ?',(float)$value);
					break;
				case 'writeoff':
					$sqlSelect->where('writeoff = ?',(float)$value);
					break;
				case 'balance':
					if ($value['operator'] == 'between') {
						$sqlSelect->where('balance BETWEEN '.$db->quote((float)$value['operand1']).' AND '.$db->quote((float)$value['operand2']));
					}
					else {
						$sqlSelect->where('balance '.$value['operator'].' '.$db->quote((float)$value['operand1']));
					}
					break;
			}
		}
		trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		//$this->_dbSelect = $sqlSelect;
		//$this->_dbStmt = $db->query($this->_sqlSelect);
	}

}
