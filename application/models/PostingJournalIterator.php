<?php
/*****************************************************************************
*       PostingJournalIterator.php
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


class PostingJournalIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null,$autoLoad = false) {
		$this->_ormClass = 'PostingJournal';
		if ($dbSelect !== null) $this->_dbSelect = $dbSelect;
		// autoLoad gives an option to query the entire rows which takes time if more data in the table
		if ($autoLoad) {
			parent::__construct($this->_ormClass,$dbSelect);
		}
	}

	public function setFilters($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$ormClass = $this->_ormClass;
		$orm = new $ormClass();
		$sqlSelect = $db->select(null)
				->from($orm->_table)
				->order('dateTime DESC','datePosted DESC');
		foreach($filters as $key=>$value) {
			switch ($key) {
				case 'visitId':
				case 'claimLineId':
				case 'claimFileId':
					if (is_array($value)) {
						$tmp = array();
						foreach ($value as $val) $tmp[] = $db->quote($val);
						if (isset($tmp[0])) $sqlSelect->where($key.' IN ('.implode(',',$tmp).')');
					}
					else {
						$sqlSelect->where($key.' = ?',(int)$value);
					}
					break;
			}
		}
		$this->_dbSelect = $sqlSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
