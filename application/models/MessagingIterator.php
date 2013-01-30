<?php
/*****************************************************************************
*       MessagingIterator.php
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


class MessagingIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null,$autoLoad = true) {
		$this->_ormClass = 'Messaging';

		if ($dbSelect === null) {
			$db = Zend_Registry::get('dbAdapter');
			$dbSelect = $db->select()
					->from('messaging')
					->order('dateStatus DESC')
					->order('objectType ASC')
					->order('objectClass ASC');
		}
		// autoLoad gives an option to query the entire rows which takes time if more data in the table
		if ($autoLoad) {
			parent::__construct($this->_ormClass,$dbSelect);
		}
	}

	public function setFilters($filters) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('messaging')
				->order('dateStatus DESC')
				->order('objectType ASC')
				->order('objectClass ASC');
		$orWhere = array();
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'optionGroup':
					$sqlSelect->group($value);
					break;
				case 'status':
					$x = explode(',',$value);
					foreach ($x as $v) {
						$orWhere[] = '`status` = '.$db->quote($v);
					}
					break;
				case 'message':
					$messageOptions = array('EPrescribes'=>Messaging::TYPE_EPRESCRIBE,'InboundFaxes'=>Messaging::TYPE_INBOUND_FAX,'OutboundFaxes'=>Messaging::TYPE_OUTBOUND_FAX);
					$x = explode(',',$value);
					$optWhere = array();
					foreach ($x as $v) {
						if (!isset($messageOptions[$v])) continue;
						$optWhere[] = '`objectType` = '.$messageOptions[$v];
					}
					if (isset($optWhere[0])) {
						$sqlSelect->where(implode(' OR ',$optWhere));
					}
					break;
				case 'dateStatus':
					$x = explode(',',$value);
					if (!isset($x[1])) {
						$x[1] = $x[0];
					}
					$x[1] = date('Y-m-d 23:59:59',strtotime($x[1]));
					$sqlSelect->where("`dateStatus` BETWEEN '".date('Y-m-d H:i:s',strtotime($x[0]))."' AND '".date('Y-m-d H:i:s',strtotime($x[1]))."'");
					break;
				case 'resolution':
					$value = (int)$value;
					if ($value == 2) break;
					$sqlSelect->where('unresolved = ?',$value);
					break;
			}
		}
		if (isset($orWhere[0])) {
			$sqlSelect->where(implode(' OR ',$orWhere));
		}
		trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $sqlSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
