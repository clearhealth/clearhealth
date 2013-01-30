<?php
/*****************************************************************************
*       BaseMed24DrugAllergyIterator.php
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


class BaseMed24DrugAllergyIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null,$autoLoad = true) {
		$this->_ormClass = 'BaseMed24';
		// autoLoad gives an option to query the entire rows which takes time if more data in the table
		if ($autoLoad) {
			parent::__construct($this->_ormClass,$dbSelect);
		}
	}

	public function setFilters($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$dbName = Zend_Registry::get('config')->database->params->dbname;
		$value = $filters['value'];
		$formulary = $filters['formulary'];
		if (strlen($value) > 1) $value  = $value . "%";
		$dbSelect = $db->select(null)
			->from('chmed.basemed24',null)
			->distinct('vaclass')
			->join($dbName.'.drugCodeClass AS drugCodeClass', 'drugCodeClass.code = basemed24.vaclass',null)
			->columns(array(
				'basemed24.vaclass',
				'basemed24.md5',
				"if (drugCodeClass.code != '',CONCAT(drugCodeClass.code,' - ',drugCodeClass.textShort),basemed24.vaclass) AS notice",
			))
			->orWhere("basemed24.vaclass LIKE ?", $value)
			->orWhere('basemed24.tradename LIKE ?', $value)
			->orWhere('basemed24.fda_drugname LIKE ?', $value)
			->orWhere('basemed24.rxnorm LIKE ?', $value)
			->orWhere("drugCodeClass.code LIKE ?", $value)
			->orWhere("drugCodeClass.textShort LIKE ?",'%'.$value)
			->order("vaclass ASC")
			->group('vaclass');

		$tablePostfix = preg_replace('/[^a-zA-Z0-9]+/','',ucfirst($formulary));
		$dbSelect->joinLeft($dbName.'.formulary'.$tablePostfix.' AS formulary', 'formulary.fullNDC = basemed24.full_ndc',null);
		$dbSelect->columns(array(
			"IF (formulary.vaclass != '',formulary.vaclass,(IF (drugCodeClass.code != '',CONCAT(drugCodeClass.code,' - ',drugCodeClass.textShort),basemed24.vaclass))) AS notice"
		));
		if (strlen($value) > 1) {
			$dbSelect->orWhere('formulary.vaclass LIKE ?',$value);
		}

		trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
