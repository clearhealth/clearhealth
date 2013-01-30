<?php
/*****************************************************************************
*       BaseMed24Iterator.php
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


class BaseMed24Iterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null,$autoLoad = true) {
		$this->_ormClass = 'BaseMed24';
		// autoLoad gives an option to query the entire rows which takes time if more data in the table
		if ($autoLoad) {
			parent::__construct($this->_ormClass,$dbSelect);
		}
	}

	public function setFilters($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$config = Zend_Registry::get('config');
		$dbname = $config->database->params->dbname;
		$dbSelect = $db->select(null)
			->from('chmed.basemed24')
			->joinLeft('chmed.basemed24labels', 'basemed24labels.pkey = basemed24.pkey',null)
                        ->columns(array(
					"basemed24.pkey as id",
					"if(chmed.basemed24labels.pkey IS NOT NULL, 1, 0) as hasLabel", 
					"if (formulary.fullNDC != '',concat('*',basemed24.ndc),basemed24.ndc) as ndc",
					"if (formulary.fullNDC != '',1,0) as inFormulary"
				))
			->order("inFormulary DESC","chmed.basemed24.tradename DESC")
			->group("basemed24.pkey");
			foreach($filters as $filter => $value) {
				switch ($filter) {
					case 'tradename':
						$dbSelect->where("full_ndc like ?", $value);
						if (strlen($value) > 1) $value  = $value . "%";
						$dbSelect->orWhere("tradename like ?", $value);
						break;
					case 'limit':
						$dbSelect->limit((int)$value);
						break;
					case 'formulary':
                                        	$dbSelect->joinLeft($dbname.'.formulary' . preg_replace('/[^a-zA-Z0-9]+/','',ucfirst($value)) . ' as formulary', 'formulary.fullNDC = basemed24.full_ndc');
						//if (isset($filters['tradename'])) $dbSelect->orWhere('formulary.keywords LIKE ?',$filters['tradename'].'%');
						break;
					case 'pkey':
						if (!isset($filters['formulary'])) {
							$dbSelect->joinLeft($dbname.'.formularyDefault AS formulary', 'formulary.fullNDC = basemed24.full_ndc');
						}
						$dbSelect->where('basemed24.pkey = ?',(string)$value);
						$dbSelect->limit(1);
						break;
					//default:
					//	$dbSelect->where("false");
				}
			}
		//echo $dbSelect->__toString();exit;
		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
