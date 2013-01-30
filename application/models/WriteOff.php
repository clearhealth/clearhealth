<?php
/*****************************************************************************
*       WriteOff.php
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


class WriteOff extends WebVista_Model_ORM {

	protected $writeOffId;
	protected $personId;
	protected $claimLineId;
	protected $claimFileId;
	protected $visitId;
	protected $appointmentId;
	protected $amount;
	protected $userId;
	protected $timestamp;
	protected $title;
	protected $payerId;

	protected $_table = 'writeOffs';
	protected $_primaryKeys = array('writeOffId');

	public function getIteratorByVisit($visit) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('personId = ?',(int)$visit->patientId)
				->where('visitId = '.(int)$visit->visitId.' OR appointmentId = ?',(int)$visit->appointmentId)
				->order('timestamp DESC');
		return new WriteOffIterator($sqlSelect,true);
	}

	public static function updateClaimIdByClaimFile(ClaimFile $claimFile) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$table = $orm->_table;
		$sql = 'UPDATE '.$table.' SET claimFileId = '.(int)$claimFile->claimFileId.' WHERE claimFileId = 0 AND visitId = '.(int)$claimFile->visitId;
		return $db->query($sql);
	}

	public function getIteratorByVisitId($visitId=null) {
		if ($visitId === null) $visitId = $this->visitId;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('visitId = ?',(int)$visitId)
				->order('timestamp DESC');
		return new WriteOffIterator($sqlSelect,true);
	}

	public function getEnteredBy() {
		$ret = '';
		$userId = (int)$this->userId;
		if ($userId > 0) {
			$user = new User();
			$user->userId = $userId;
			$user->populate();
			$ret = $user->username;
		}
		return $ret;
	}

	public static function total(Array $filters) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table,array('SUM(amount) AS total'));
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'personId':
				case 'claimLineId':
				case 'claimFileId':
				case 'visitId':
				case 'appointmentId':
				case 'payerId':
				case 'userId':
					$sqlSelect->where($key.' = ?',(int)$value);
					break;
			}
		}
		$total = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$total = (float)$row['total'];
		}
		return $total;
	}

}
