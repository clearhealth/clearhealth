<?php
/*****************************************************************************
*       Payment.php
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


class Payment extends WebVista_Model_ORM {

	protected $payment_id;
	protected $foreign_id;
	protected $encounter_id;
	protected $payment_type;
	protected $ref_num;
	protected $amount;
	protected $writeoff;
	protected $user_id;
	protected $timestamp;
	protected $payer_id;
	protected $payment_date;
	protected $title;
	protected $personId;
	protected $appointmentId;
	protected $claimLineId;
	protected $claimFileId;
	protected $allocated;

	protected $_table = 'payment';
	protected $_primaryKeys = array('payment_id');
	protected $_legacyORMNaming = true;

	public function getIteratorByPayerId($payerId = null) {
		if ($payerId === null) {
			$payerId = $this->payerId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('payer_id = ?',(int)$payerId);
		return $this->getIterator($sqlSelect);
	}

	public function getIteratorByVisitId($visitId = null) {
		if ($visitId === null) {
			$visitId = $this->encounterId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where("payment_date BETWEEN '".date('Y-m-d',strtotime('-30 days'))."' AND '".date('Y-m-d')."'")
				->where('encounter_id = ?',(int)$visitId)
				->order('timestamp DESC');
		return $this->getIterator($sqlSelect);
	}

	public function getMostRecentPayments() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where("payment_date BETWEEN '".date('Y-m-d',strtotime('-30 days'))."' AND '".date('Y-m-d')."'");
		return $this->getIterator($sqlSelect);
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
				->order('timestamp DESC');
		return $this->getIterator($sqlSelect);
	}

	public static function updateClaimIdByClaimFile(ClaimFile $claimFile) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$table = $orm->_table;
		$sql = 'UPDATE '.$table.' SET claimFileId = '.(int)$claimFile->claimFileId.' WHERE claimFileId = 0 AND encounter_id = '.(int)$claimFile->visitId;
		return $db->query($sql);
	}

	public function getIterator($sqlSelect=null) {
		return new PaymentIterator($sqlSelect);
	}

	public function getEnteredBy() {
		$ret = '';
		$userId = (int)$this->user_id;
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
		$journal = new PostingJournal();
		$sqlSelect = $db->select()
				->from($journal->_table,array('SUM(amount) AS total'));
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'paymentId':
				case 'patientId':
				case 'payerId':
				case 'visitId':
				case 'claimLineId':
				case 'claimFileId':
				case 'userId':
					if (is_array($value)) {
						$tmp = array();
						foreach ($value as $val) {
							$tmp[] = $db->quote($val);
						}
						$sqlSelect->where($key.' IN ('.implode(',',$tmp).')');
					}
					else {
						$sqlSelect->where($key.' = ?',(int)$value);
					}
					break;
			}
		}
		$total = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$total = (float)$row['total'];
		}
		return $total;
	}

	public static function unpostedTotal($visitId) {
		$db = Zend_Registry::get('dbAdapter');
		$payment = new self();
		$sqlSelect = $db->select()
				->from($payment->_table,array('SUM(amount) AS total'))
				->where('encounter_id = ?',(int)$visitId)
				->where('allocated = 0')
				->where('claimLineId = 0');
		$total = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$total = (float)$row['total'];
		}
		return $total;
	}

	public static function listAccounts(Array $filters) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('payment',array(
					'payment.payment_id AS id',
					'CONCAT(\'0\') AS billed',
					'payment.amount AS paid',
					'CONCAT(\'0\') AS writeOff',
					'CONCAT(\'Payment\') AS payer',
					'payment.payment_date AS dateOfTreatment',
					'CONCAT(\'\') AS dateBilled',
					'CONCAT(patient.last_name,\', \',patient.first_name,\' \',patient.middle_name) AS patientName',
					'CONCAT(\'\') AS facility',
					'CONCAT(provider.last_name,\', \',provider.first_name,\' \',provider.middle_name) AS providerName',
				))
				->join('encounter','encounter.encounter_id = payment.encounter_id')
				->join(array('patient'=>'person'),'patient.person_id = encounter.patient_id')
				->join(array('provider'=>'person'),'provider.person_id = encounter.treating_person_id')
				->where('payment.claimLineId = 0')
				->order('payment.payment_date DESC');
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'dateRange':
					$sqlSelect->where("encounter.date_of_treatment BETWEEN '{$value['start']} 00:00:00' AND '{$value['end']} 23:59:59'");
					break;
				case 'facilities':
					// practice, building, room
					if (!is_array($value)) $value = array($value);
					$facilities = array();
					foreach ($value as $val) {
						$facilities[] = 'encounter.practice_id = '.(int)$val['practice'].' AND encounter.building_id = '.(int)$val['building'].' AND encounter.room_id = '.(int)$val['room'];
					}
					$sqlSelect->where(implode(' OR ',$facilities));
					break;
				case 'payers':
					$payers = array();
					foreach ($value as $payerId) {
						$payers[] = (int)$payerId;
					}
					$sqlSelect->where('encounter.activePayerId IN ('.implode(',',$payers).')');
					break;
				case 'facility':
					// practice, building, room
					$sqlSelect->where('encounter.practice_id = ?',(int)$value['practice']);
					$sqlSelect->where('encounter.building_id = ?',(int)$value['building']);
					$sqlSelect->where('encounter.room_id = ?',(int)$value['room']);
					break;
				case 'insurer':
					$sqlSelect->where('encounter.activePayerId = ?',(int)$value);
					break;
				case 'visitId':
					$sqlSelect->where('encounter.encounter_id = ?',(int)$value);
					break;
				case 'provider':
					$sqlSelect->where('encounter.treating_person_id = ?',(int)$value);
					break;
				case 'providers':
					$providers = array();
					foreach ($value as $providerId) {
						$providers[] = (int)$providerId;
					}
					$sqlSelect->where('encounter.treating_person_id IN ('.implode(',',$providers).')');
					break;
			}
		}

		$rows = array();
		$stmt = $db->query($sqlSelect);
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rows[] = $row;
		}
		return $rows;
	}

	public function getIsPosted() {
		$db = Zend_Registry::get('dbAdapter');
		$ret = false;
		$sqlSelect = $db->select()
				->from($this->_table,array('payment_id'))
				->where('allocated > 0')
				->where('payment_id = ?',(int)$this->payment_id);
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		return $ret;
	}

	public function getUnallocated() {
		return (float)$this->amount - (float)$this->allocated;
	}

	public static function listCheckFunds($checkNo) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table)
				->where('(amount - allocated) > 0')
				->where("payment_type = 'CHECK'")
				->where('ref_num = ?',(string)$checkNo);
		$total = 0;
		$details = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$payment = new Payment();
				$payment->populateWithArray($row);
				$total += $payment->unallocated;
				$details[$row['payment_id']] = $payment;
			}
		}
		return array(
			'total'=>$total,
			'details'=>$details,
		);
	}

	public static function listUnallocatedFunds($personId) {
		// payments with appointmentId OR payments assigned ONLY to person
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('payment')
				->where('personId = ?',(int)$personId)
				->where('(amount - allocated) > 0')
				->where('encounter_id = 0')
				->where('appointmentId = 0');
		$total = 0;
		$details = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$payment = new Payment();
				$payment->populateWithArray($row);
				$total += $payment->unallocated;
				$details[$row['payment_id']] = $payment;
			}
		}
		return array(
			'total'=>$total,
			'details'=>$details,
		);
	}

	// can be used to get all payments if visit is open
	public static function getIteratorByIds(Visit $visit) {
		$orm = new self();
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($orm->_table)
				->where('(encounter_id = '.(int)$visit->visitId.') OR (encounter_id = 0 AND appointmentId = '.(int)$visit->appointmentId.' AND personId = '.(int)$visit->patientId.')');
		return $orm->getIterator($sqlSelect);
	}

	public function getInsuranceDisplay() {
		$payerId = (int)$this->payer_id;
		$ret = '';
		if ($payerId > 0) $ret = InsuranceProgram::getInsuranceProgram($payerId);
		return $ret;
	}

}
