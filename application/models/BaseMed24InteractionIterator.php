<?php
/*****************************************************************************
*       BaseMed24InteractionIterator.php
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


class BaseMed24InteractionIterator extends WebVista_Model_ORMIterator implements Iterator {

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
		$dbSelect = $db->select(null)
			->from('chmed.basemed24')
			->join('chmed.basemed24interactions', 'basemed24interactions.md5 = basemed24.md5')
			->order("basemed24interactions.notice ASC")
			->order("basemed24.tradename ASC")
			->group("basemed24.md5");
		foreach($filters as $filter => $value) {
			switch ($filter) {
				case 'personId':
					if ($value > 0) {
						$dbSelect->join($dbName.'.medications', 'basemed24.hipaa_ndc = medications.hipaaNDC')
							->where('medications.personId = ?',$value);
					}
					break;
				case 'md5':
                                       	$dbSelect->where('basemed24interactions.interact_md5 = ?',$value);
					break;
				//default:
				//	$dbSelect->where("false");
			}
		}
		//echo $dbSelect->__toString();exit;
		trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
