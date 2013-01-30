<?php
/*****************************************************************************
*       GeneralAlert.php
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


class GeneralAlert extends WebVista_Model_ORM {

	protected $generalAlertId;
	protected $message;
	protected $urgency;
	protected $status; // new, processing, processed, inactivated
	protected $dateTime;
	protected $teamId;
	protected $userId;
	protected $objectId;
	protected $objectClass;
	protected $forwardedBy;
	protected $comment;
	protected $_table = 'generalAlerts';
	protected $_primaryKeys = array('generalAlertId');

	public function getIteratorByTeam($teamId = null) {
		if ($teamId === null) {
			$teamId = $this->teamId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
				->from($this->_table)
				->where("status = 'new' AND (userId = ".(int)Zend_Auth::getInstance()->getIdentity()->personId.")")
				->order('dateTime DESC');
		if (strlen($teamId) > 0) {
			$dbSelect->orWhere("teamId = ?",$teamId);
		}
		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		return parent::getIterator($dbSelect);
	}

	public function populateOpenedAlertByFilters(Array $filters) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where("status = 'new'")
				->limit(1);
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'objectClass':
				case 'objectId':
				case 'teamId':
				case 'userId':
					$sqlSelect->where($key.' = ?',(string)$value);
					break;
			}
		}
		return $this->populateWithSql($sqlSelect->__toString());
	}

}
