<?php
/*****************************************************************************
*       NSDRDefinitionIterator.php
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


/**
 * WebVista_Model_ORMIterator
 */
require_once 'WebVista/Model/ORMIterator.php';

class NSDRDefinitionIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null) {
		if ($dbSelect === null) {
			$db = Zend_Registry::get('dbAdapter');
			$dbSelect = $db->select()
				       ->from('nsdrDefinitions')
				       ->joinLeft('nsdrDefinitionMethods', 'nsdrDefinitionMethods.uuid = nsdrDefinitions.uuid', array('methodName','method'));
		}
		parent::__construct("NSDRDefinition",$dbSelect);
	}	

	public function current() {
		$ormObj = new $this->_ormClass();
		$row = $this->_dbStmt->fetch(null,null,$this->_offset);
		$ormObj->populateWithArray($row);
		return $ormObj;
	}

	public function setFilters(Array $filter) {
		$regExp = '[a-zA-Z0-9_]{1}[^\.]*';
		if (isset($filter['id']) && $filter['id'] !== '0') {
			$regExp = $filter['id'] . '[\.]*' . $regExp;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from('nsdrDefinitions')
			       ->joinLeft('nsdrDefinitionMethods', 'nsdrDefinitionMethods.uuid = nsdrDefinitions.uuid', array('methodName','method'))
			       ->where("namespace REGEXP '^{$regExp}$'");
		if (isset($filter['id'])) {
			$dbSelect->group('namespace');
		}
		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
