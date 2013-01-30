<?php
/*****************************************************************************
*       MiscCharge.php
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


class MiscCharge extends WebVista_Model_ORM {

	protected $misc_charge_id;
	protected $encounter_id;
	protected $amount;
	protected $charge_date;
	protected $title;
	protected $note;
	protected $chargeType;
	protected $personId;
	protected $appointmentId;
	protected $claimLineId;
	protected $claimFileId;

	protected $_table = 'misc_charge';
	protected $_primaryKeys = array('misc_charge_id');
	protected $_legacyORMNaming = true;

	public function getIteratorByVisitId($visitId = null) {
		if ($visitId === null) {
			$visitId = $this->encounterId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('encounter_id = ?',(int)$visitId);
		return $this->getIterator($sqlSelect);
	}

	public function getUnpaidCharges() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from(array('cc'=>'clearhealth_claim'))
				->joinLeft(array('fc'=>'fbclaim'),'fc.claim_id=cc.claim_id')
				->where('total_billed > total_paid');
		// identifier = note?
		$ret = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$ret[$row['claim_id']] = array(
					'date'=>date('Y-m-d',strtotime($row['timestamp'])),
					'type'=>'',
					'amount'=>($row['total_billed'] - $row['total_paid']),
					'note'=>'Encounter DOS: '.date('m/d/Y',strtotime($row['date_sent'])).' '.$row['identifier'],
				);
			}
		}
		return $ret;
	}

	public function getVisitId() {
		return $this->encounter_id;
	}

	public function setVisitId($id) {
		$this->encounter_id = $id;
	}

	public function getIteratorByAppointmentId($appointmentId = null) {
		if ($appointmentId === null) {
			$appointmentId = $this->appointmentId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('appointmentId = ?',(int)$appointmentId)
				->order('charge_date DESC');
		return $this->getIterator($sqlSelect);
	}

	public function getUnpaidChargesByVisit($visitId) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from(array('cc'=>'clearhealth_claim'))
				->joinLeft(array('fc'=>'fbclaim'),'fc.claim_id=cc.claim_id')
				->where('total_billed > total_paid')
				->where('cc.encounter_id = ?',(int)$visitId);
		// identifier = note?
		$ret = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$ret[$row['claim_id']] = array(
					'date'=>date('Y-m-d',strtotime($row['timestamp'])),
					'type'=>'',
					'amount'=>($row['total_billed'] - $row['total_paid']),
					'note'=>'Encounter DOS: '.date('m/d/Y',strtotime($row['date_sent'])).' '.$row['identifier'],
				);
			}
		}

		// misc charges
		foreach ($this->getIteratorByVisitId($visitId) as $row) {
			$ret[$row->miscChargeId] = array(
				'date'=>date('Y-m-d',strtotime($row->chargeDate)),
				'type'=>$row->chargeType,
				'amount'=>(float)$row->amount,
				'note'=>$row->note,
			);
		}
		return $ret;
	}

	public static function updateClaimIdByClaimFile(ClaimFile $claimFile) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$table = $orm->_table;
		$sql = 'UPDATE '.$table.' SET claimFileId = '.(int)$claimFile->claimFileId.' WHERE claimFileId = 0 AND encounter_id = '.(int)$claimFile->visitId;
		return $db->query($sql);
	}

	public static function total(Array $filters) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table,array('SUM(amount) AS total'));
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'visitId':
					$sqlSelect->where('encounter_id = ?',(int)$value);
					break;
				case 'personId':
				case 'claimLineId':
				case 'claimFileId':
				case 'appointmentId':
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

	// can be used to get all misc charges if visit is open
	public static function getIteratorByIds(Visit $visit) {
		$orm = new self();
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($orm->_table)
				->where('(encounter_id = '.(int)$visit->visitId.') OR (encounter_id = 0 AND appointmentId = '.(int)$visit->appointmentId.' AND personId = '.(int)$visit->patientId.')');
		return $orm->getIterator($sqlSelect);
	}

	public function getEnteredBy() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('audits','userId')
				->where('objectClass = ?','MiscCharge')
				->where('objectId = ?',$this->misc_charge_id)
				->order('dateTime DESC')
				->limit(1);
		$ret = '';
		if ($row = $db->fetchRow($sqlSelect)) {
			$userId = (int)$row['userId'];
			if ($userId > 0) {
				$user = new User();
				$user->userId = $userId;
				$user->populate();
				$ret = $user->username;
			}
		}
		return $ret;
	}

}
