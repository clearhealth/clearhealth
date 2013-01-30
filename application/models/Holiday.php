<?php
/*****************************************************************************
*       Holiday.php
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


class Holiday extends WebVista_Model_ORM {

	protected $holidayId;
	protected $date;
	protected $description;
	protected $_table = 'holidays';
	protected $_primaryKeys = array('holidayId');

	public function getIteratorByYear($year = null) {
		if ($year === null) {
			$year = $this->year;
		}
		if (!$year > 0) {
			$year = date('Y');
		}
		$dateFrom = $year.'-01-01';
		$dateTo = $year.'-12-31';
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where("date BETWEEN '{$dateFrom}' AND '{$dateTo}'")
				->order('date ASC');
		return $this->getIterator($sqlSelect);
	}

	public function getIterator($sqlSelect = null) {
		if ($sqlSelect === null) {
			$db = Zend_Registry::get('dbAdapter');
			$sqlSelect = $db->select()
					->from($this->_table)
					->order('date ASC');
		}
		return parent::getIterator($sqlSelect);
	}

	public static function isDayHoliday($date) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('holidays')
				->where('date = ?',$date)
				->limit(1);
		return $db->fetchRow($sqlSelect);
	}

}
