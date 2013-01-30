<?php
/*****************************************************************************
*       FilterState.php
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


class FilterState extends WebVista_Model_ORM {
	protected $filterStateId;
	protected $tabName;
	protected $providerId;
	protected $roomId;
	protected $dateFilter;
	protected $showCancelledAppointments;
	protected $userId;

	protected $_table = "filterStates";
	protected $_primaryKeys = array("filterStateId");

	public function delete($key,$val) {
		$key[0] = strtolower($key[0]);
		if (!in_array($key,$this->ORMFields())) {
			return;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sql = "DELETE FROM `{$this->_table}` WHERE `$key`='{$val}'";
		return $db->query($sql);
	}

	public function __call($name,$args) {
		preg_match('/deleteBy(.*)/',$name,$matches);
		if (isset($matches[1])) {
			array_unshift($args,$matches[1]);
			call_user_func_array(array($this,'delete'),$args);
		}
	}
}
