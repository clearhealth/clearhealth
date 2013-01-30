<?php
/*****************************************************************************
*       ClaimLine.php
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


class ClaimLine extends WebVista_Model_ORM {

	protected $claimLineId;
	protected $claimId;
	protected $visitId;
	protected $insuranceProgramId;
	protected $procedureCode;
	protected $units;
	protected $diagnosisCode1;
	protected $diagnosisCode2;
	protected $diagnosisCode3;
	protected $diagnosisCode4;
	protected $diagnosisCode5;
	protected $diagnosisCode6;
	protected $diagnosisCode7;
	protected $diagnosisCode8;
	protected $modifier1;
	protected $modifier2;
	protected $modifier3;
	protected $modifier4;
	protected $excludeFromDiscount;
	protected $excludeFromClaim;
	protected $mappedCode;
	protected $baseFee;
	protected $adjustedFee;
	protected $unitsDoesNotEffectFee;
	protected $linkedMedicationId;
	protected $ndc;
	protected $dateTime;
	protected $note;

	protected $_table = 'claimLines';
	protected $_primaryKeys = array('claimLineId');

	public function persist() {
		if (!$this->dateTime || $this->dateTime == '0000-00-00 00:00:00') {
			$this->dateTime = date('Y-m-d H:i:s');
		}
		return parent::persist();
	}

	public static function doesVisitProcedureRowExist($visitId,$procedureCode) {
		$orm = new self();
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($orm->_table,'claimLineId')
				->where('visitId = ?',(int)$visitId)
				->where('procedureCode = ?',$procedureCode.'');
		$ret = false;
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		return $ret;
	}

	public function getProcedure() {
		$db = Zend_Registry::get('dbAdapter');
		$procedure = new PatientProcedure();
		$sqlSelect = $db->select()
				->from($procedure->_table,'procedure')
				->where('code = ?',$this->procedureCode)
				->where('visitId = ?',(int)$this->visitId)
				->limit(1);
		$ret = '';
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = $row['procedure'];
		}
		return $ret;
	}

	public function setUnsetDiagnosis($code,$state) {
		return $this->_setUnsetDiagnosisModifier('diagnosisCode',8,$code,$state);
	}

	public function setUnsetModifier($code,$state) {
		return $this->_setUnsetDiagnosisModifier('modifier',4,$code,$state);
	}

	protected function _setUnsetDiagnosisModifier($prefix,$ctr,$code,$state) {
		$ret = false;
		for ($i = 1; $i <= $ctr; $i++) {
			$field = $prefix.$i;
			if ($state) { // add
				if (!strlen($this->$field) > 0) {
					$this->$field = $code;
					$ret = true;
					break;
				}
			}
			else { // remove
				if ($this->$field == $code) {
					$this->$field = '';
					$ret = true;
					break;
				}
			}
		}
		return $ret;
	}

	public function reorderDiagnosis($from,$to) {
		return $this->_reorderDiagnosisModifier('diagnosisCode',8,$from,$to);
	}

	public function reorderModifier($from,$to) {
		return $this->_reorderDiagnosisModifier('modifier',4,$from,$to);
	}

	protected function _reorderDiagnosisModifier($prefix,$ctr,$from,$to) {
		$ret = false;
		$indexFrom = 0;
		$indexTo = 0;
		for ($i = 1; $i <= $ctr; $i++) {
			$field = $prefix.$i;
			if ($this->$field == $from) {
				$indexFrom = $i;
			}
			else if ($this->$field == $to) {
				$indexTo = $i;
			}
		}
		if ($indexFrom != 0 && $indexTo != 0) {
			if ($indexFrom > $indexTo) { // bottom to top
				$field = $prefix.($indexTo+1);
				$val = $this->$field;
				$this->$field = $from;
				for ($i = ($indexTo+2); $i <= $indexFrom; $i++) {
					$field = $prefix.$i;
					$tmp = $this->$field;
					$this->$field = $val;
					$val = $tmp;
				}
			}
			else { // top to bottom
				for ($i = $indexFrom; $i < $indexTo; $i++) {
					$field = $prefix.$i;
					$nextField = $prefix.($i+1);
					$this->$field = $this->$nextField;
				}
				$field = $prefix.$indexTo;
				$this->$field = $from;
			}
		}
		return $ret;
	}

	public function totalPaid($claimFileId) {
		$filters = array();
		$filters['visitId'] = (int)$this->visitId;
		$filters['claimFileId'] = (int)$claimFileId;
		return Payment::total($filters);
	}

	public function totalWriteOff($claimFileId) {
		$db = Zend_Registry::get('dbAdapter');
		$writeOff = new WriteOff();
		$sqlSelect = $db->select()
				->from($writeOff->_table,'SUM(amount) AS total')
				->where('visitId = ?',(int)$this->visitId)
				->where('claimFileId = ?',(int)$claimFileId);
		$ret = 0.0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = (float)$row['total'];
		}
		return $ret;
	}

	public function getId() {
		return $this->claimLineId;
	}

	public function setId($id) {
		$this->claimLineId = (int)$id;
	}

	public function populateWithPatientProcedure(PatientProcedure $patientProcedure,Visit $visit=null) {
		$db = Zend_Registry::get('dbAdapter');
		$visitId = $patientProcedure->visitId;
		if ($visit === null) {
			$visit = new Visit();
			$visit->visitId = $visitId;
			$visit->populate();
		}
		$this->visitId = $visitId;
		$this->procedureCode = $patientProcedure->code;
		// populate with the latest claim line
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('visitId = ?',$visitId)
				->where('procedureCode = ?',$this->procedureCode)
				->order('claimId DESC')
				->limit(1);
		$this->populateWithSql($sqlSelect->__tostring());
		$this->insuranceProgramId = (int)$visit->activePayerId;
		$this->units = $patientProcedure->quantity;
		$this->diagnosisCode1 = $patientProcedure->diagnosisCode1;
		$this->diagnosisCode2 = $patientProcedure->diagnosisCode2;
		$this->diagnosisCode3 = $patientProcedure->diagnosisCode3;
		$this->diagnosisCode4 = $patientProcedure->diagnosisCode4;
		$this->diagnosisCode5 = $patientProcedure->diagnosisCode5;
		$this->diagnosisCode6 = $patientProcedure->diagnosisCode6;
		$this->diagnosisCode7 = $patientProcedure->diagnosisCode7;
		$this->diagnosisCode8 = $patientProcedure->diagnosisCode8;
		$this->modifier1 = $patientProcedure->modifier1;
		$this->modifier2 = $patientProcedure->modifier2;
		$this->modifier3 = $patientProcedure->modifier3;
		$this->modifier4 = $patientProcedure->modifier4;
	}

	public function getAmountBilled() {
		$amountBilled = (float)$this->baseFee;
		$adjustedFee = (float)$this->adjustedFee;
		if ($amountBilled > 0 && $adjustedFee > 0) $amountBilled -= $adjustedFee;
		return $amountBilled;
	}

	public function getPaid() {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new PostingJournal();
		$sqlSelect = $db->select()
				->from($orm->_table,'SUM(amount) AS paid')
				->where('claimLineId = ?',(int)$this->claimLineId);
		$paid = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$paid = (float)$row['paid'];
		}
		return $paid;
	}

	public function getWriteOff() {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new WriteOff();
		$sqlSelect = $db->select()
				->from($orm->_table,'SUM(amount) AS writeOff')
				->where('claimLineId = ?',(int)$this->claimLineId);
		$writeOff = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$writeOff = (float)$row['writeOff'];
		}
		return $writeOff;
	}

	public function populateByClaimId($claimId=null) {
		if ($claimId === null) $claimId = $this->claimId;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('claimId = ?',(int)$claimId)
				->limit(1);
		return $this->populateWithSql($sqlSelect->__toString());
	}

	public function getUniqueCheckNumbers() {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new Payment();
		$sqlSelect = $db->select()
				->from($orm->_table,array('ref_num AS chkNo','SUM(amount - allocated)  AS unallocated'))
				->where('(amount - allocated) > 0')
				->where("payment_type = 'CHECK'")
				->where("ref_num != ''")
				->group('ref_num');
		$ret = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$ret[] = $row;
			}
		}
		return $ret;
	}

	public function getCheckNumbers() {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new Payment();
		$sqlSelect = $db->select()
				->from($orm->_table,array('payment_id AS paymentId','ref_num AS checkNo','(amount - allocated)  AS unallocated'))
				->where('(amount - allocated) > 0')
				->where("payment_type = 'CHECK'")
				->where("ref_num != ''");
		$ret = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$ret[] = $row;
			}
		}
		return $ret;
	}

	public static function listAllMostRecentClaimIds(Array $visitIds) {
		// sanitized visit ids
		$sanitizedIds = array();
		foreach ($visitIds as $id) {
			$sanitizedIds[] = (int)$id;
		}
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table,array('MAX(claimId) AS claimId'))
				->where('visitId IN ('.implode(',',$sanitizedIds).')')
				->group('visitId');
		$ret = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$ret[] = $row['claimId'];
			}
		}
		return $ret;
	}

	public static function mostRecentClaim($visitId,$idOnly=false) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$fields = array('claimId');
		$sqlSelect = $db->select()
				->where('visitId = ?',(int)$visitId)
				->order('claimId DESC')
				->group('claimId')
				->limit(1);
		if ($idOnly) {
			$sqlSelect->from($orm->_table,array('claimId'));
		}
		else {
			$sqlSelect->from($orm->_table);
		}
		$claimId = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$claimId = (int)$row['claimId'];
			if (!$idOnly) $orm->populateWithArray($row);
		}
		if ($idOnly) return $claimId;
		return $orm;
	}

	public static function mostRecentClaims($visitId) {
		$db = Zend_Registry::get('dbAdapter');
		$claimId = self::mostRecentClaim($visitId,true);
		if (!$claimId > 0) return array();
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table)
				->where('claimId = ?',(int)$claimId);
		return new ClaimLineIterator($sqlSelect);
	}

	public static function getClaimLineIds($claimId) {
		static $claimLineIds = array();
		$claimId = (int)$claimId;
		if (!isset($claimLineIds[$claimId])) {
			$db = Zend_Registry::get('dbAdapter');
			$orm = new self();
			$sqlSelect = $db->select()
					->from($orm->_table,array('claimLineId'))
					->where('claimId = ?',$claimId);
			$claimLineIds[$claimId] = array();
			if ($rows = $db->fetchAll($sqlSelect)) {
				foreach ($rows as $row) {
					$claimLineIds[$claimId][] = (int)$row['claimLineId'];
				}
			}
		}
		return $claimLineIds[$claimId];
	}

	public function getTotal($includeAdjustedFee=false) {
		$ret = self::total(array('claimId'=>$this->claimId));
		if (!$includeAdjustedFee) $ret = $ret['baseFee'];
		return $ret;
	}

	public static function total(Array $filters) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table,array('SUM(baseFee) AS baseFee','SUM(adjustedFee) AS adjustedFee'));
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'claimId':
				case 'visitId':
					$sqlSelect->where($key.' = ?',(int)$value);
					break;
				case 'payerId':
					$sqlSelect->where('insuranceProgramId = ?',(int)$value);
					break;
			}
		}
		$ret = array(
			'baseFee'=>0,
			'adjustedFee'=>0,
		);
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret['baseFee'] = (float)$row['baseFee'];
			$ret['adjustedFee'] = (float)$row['adjustedFee'];
		}
		return $ret;
	}

	public function getTotalMiscCharge() {
		$db = Zend_Registry::get('dbAdapter');
		$claimLineIds = self::getClaimLineIds($this->claimId);
		$orm = new MiscCharge();
		$sqlSelect = $db->select()
				->from($orm->_table,array('SUM(amount) AS miscCharge'))
				->where('claimLineId IN ('.implode(',',$claimLineIds).')');
		$miscCharge = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$miscCharge = (float)$row['miscCharge'];
		}
		return $miscCharge;
	}

	public function getTotalPaid() {
		$filters = array();
		$filters['claimLineId'] = self::getClaimLineIds($this->claimId);
		return Payment::total($filters);
	}

	public function getTotalWriteOff() {
		$db = Zend_Registry::get('dbAdapter');
		$claimLineIds = self::getClaimLineIds($this->claimId);
		$orm = new WriteOff();
		/*$sqlSelect = $db->select()
				->from($orm->_table,array('SUM(amount) AS writeOff'))
				->where('claimLineId IN (?)',implode(',',$claimLineIds));*/
		$table = $orm->_table;
		$sqlSelect = $db->select()
				->from($this->_table,array($this->_table.'.procedureCode','SUM('.$table.'.amount) AS writeOff'))
				->join($table,$table.'.claimLineId = '.$this->_table.'.claimLineId')
				->where($this->_table.'.claimLineId IN ('.implode(',',$claimLineIds).')')
				->group($this->_table.'.claimId');
		$writeOff = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$writeOff = (float)$row['writeOff'];
		}
		return $writeOff;
	}

	public function recalculateBaseFee(Visit $visit) {
		$fee = 0;
		$retFee = FeeSchedule::checkFee($this->insuranceProgramId,substr($visit->dateOfTreatment,0,10),$this->procedureCode);
		if ($retFee !== false && (float)$retFee['fee'] != 0) {
			$fee = (float)$retFee['fee'];
			$tmpFee = 0;
			for ($i = 1; $i <= 4; $i++) {
				$modifier = 'modifier'.$i;
				if (!strlen($this->$modifier) > 0) continue;
				switch ($this->$modifier) {
					case $retFee['modifier1']:
						$tmpFee += (float)$retFee['modifier1fee'];
						break;
					case $retFee['modifier2']:
						$tmpFee += (float)$retFee['modifier2fee'];
						break;
					case $retFee['modifier3']:
						$tmpFee += (float)$retFee['modifier3fee'];
						break;
					case $retFee['modifier4']:
						$tmpFee += (float)$retFee['modifier4fee'];
						break;
				}
			}
			if ($tmpFee > 0) $fee = $tmpFee;
		}
		$units = (int)$this->units;
		if ($units > 0) {
			$fee *= $units;
		}
		$this->baseFee = $fee;
	}

	public static function listCharges(Array $filters) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('claimLines')
				->join('encounter','encounter.encounter_id = claimLines.visitId',array('SUM(claimLines.baseFee) AS totalBaseFee','SUM(claimLines.adjustedFee) AS totalAdjustedFee'))
				->order('claimLines.dateTime DESC')
				->order('claimLines.claimId DESC')
				->order('encounter.appointmentId')
				->group('encounter.encounter_id');
		$sqlSelect = $db->select()
				->from('encounter')
				->order('encounter.date_of_treatment DESC');
		$mscSelect = $db->select()
				->from('misc_charge',array(
					'misc_charge.misc_charge_id AS id',
					'misc_charge.amount AS billed',
					'CONCAT(\'0\') AS paid',
					'CONCAT(\'0\') AS writeOff',
					'CONCAT(\'Misc Charge\') AS payer',
					'encounter.date_of_treatment AS dateOfTreatment',
					'misc_charge.charge_date AS dateBilled',
					'CONCAT(patient.last_name,\', \',patient.first_name,\' \',patient.middle_name) AS patientName',
					'CONCAT(\'\') AS facility',
					'CONCAT(provider.last_name,\', \',provider.first_name,\' \',provider.middle_name) AS providerName',
				))
				->join('encounter','encounter.encounter_id = misc_charge.encounter_id')
				->join(array('patient'=>'person'),'patient.person_id = encounter.patient_id')
				->join(array('provider'=>'person'),'provider.person_id = encounter.treating_person_id')
				->order('misc_charge.charge_date DESC');
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'dateRange':
					$sqlSelect->where("encounter.date_of_treatment BETWEEN '{$value['start']} 00:00:00' AND '{$value['end']} 23:59:59'");
					$mscSelect->where("encounter.date_of_treatment BETWEEN '{$value['start']} 00:00:00' AND '{$value['end']} 23:59:59'");
					break;
				case 'facilities':
					// practice, building, room
					if (!is_array($value)) $value = array($value);
					$facilities = array();
					foreach ($value as $val) {
						$facilities[] = 'encounter.practice_id = '.(int)$val['practice'].' AND encounter.building_id = '.(int)$val['building'].' AND encounter.room_id = '.(int)$val['room'];
					}
					$sqlSelect->where(implode(' OR ',$facilities));
					$mscSelect->where(implode(' OR ',$facilities));
					break;
				case 'payers':
					$payers = array();
					foreach ($value as $payerId) {
						$payers[] = (int)$payerId;
					}
					$payers = implode(',',$payers);
					$sqlSelect->where('encounter.activePayerId IN ('.$payers.')');
					$mscSelect->where('encounter.activePayerId IN ('.$payers.')');
					break;
				case 'facility':
					// practice, building, room
					$sqlSelect->where('encounter.practice_id = ?',(int)$value['practice']);
					$sqlSelect->where('encounter.building_id = ?',(int)$value['building']);
					$sqlSelect->where('encounter.room_id = ?',(int)$value['room']);

					// practice, building, room
					$mscSelect->where('encounter.practice_id = ?',(int)$value['practice']);
					$mscSelect->where('encounter.building_id = ?',(int)$value['building']);
					$mscSelect->where('encounter.room_id = ?',(int)$value['room']);
					break;
				case 'insurer':
					$sqlSelect->where('encounter.activePayerId = ?',(int)$value);
					$mscSelect->where('encounter.activePayerId = ?',(int)$value);
					break;
				case 'visitId':
					$sqlSelect->where('encounter.encounter_id = ?',(int)$value);
					$mscSelect->where('encounter.encounter_id = ?',(int)$value);
					break;
				case 'provider':
					$value = array($value);
				case 'providers':
					$providers = array();
					foreach ($value as $providerId) {
						$providers[] = (int)$providerId;
					}
					$providers = implode(',',$providers);
					$sqlSelect->where('encounter.treating_person_id IN ('.$providers.')');
					$mscSelect->where('encounter.treating_person_id IN ('.$providers.')');
					break;
				case 'closed':
					$sqlSelect->where('encounter.closed = ?',(int)$value);
					$mscSelect->where('encounter.closed = ?',(int)$value);
					break;
				case 'void':
					$sqlSelect->where('encounter.void = ?',(int)$value);
					$mscSelect->where('encounter.void = ?',(int)$value);
					break;
			}
		}

		$rows = array();
		$visits = array();
		$payers = array();
		$facilities = array();
		$patients = array();
		$providers = array();
		$stmt = $db->query($sqlSelect);
		$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
		while ($row = $stmt->fetch()) {
			$visit = new Visit();
			$visit->populateWithArray($row);
			$visitId = (int)$visit->visitId;

			$acct = $visit->accountSummary;
			$paid = $acct['payment'];
			$writeoff = $acct['writeoff'];
			$total = $acct['total'];
			$billed = $acct['billed'];
			$balance = $acct['balance'];

			$payerId = isset($acct['claimLine'])?$acct['claimLine']->insuranceProgramId:$visit->activePayerId;
			if (!isset($payers[$payerId])) $payers[$payerId] = InsuranceProgram::getInsuranceProgram($payerId);
			$patientId = (int)$visit->patientId;
			if (!isset($patients[$patientId])) {
				$patient = new Patient();
				$patient->personId = $patientId;
				$patient->populate();
				$patients[$patientId] = $patient;
			}
			$facilityId = (int)$visit->roomId;
			if (!isset($facilities[$facilityId])) {
				$facilities[$facilityId] = $visit->facility;
			}
			$providerId = (int)$visit->providerId;
			if (!isset($providers[$providerId])) {
				$provider = new Provider();
				$provider->personId = $providerId;
				$provider->populate();
				$providers[$providerId] = $provider;
			}

			$tmp = array();
			$tmp['id'] = $visitId;
			$tmp['total'] = $total;
			$tmp['billed'] = $billed;
			$tmp['paid'] = $paid;
			$tmp['writeOff'] = $writeoff;
			$tmp['balance'] = $balance;
			$tmp['payer'] = $payers[$payerId];
			$tmp['dateOfTreatment'] = $visit->dateOfTreatment;
			$tmp['dateBilled'] = isset($acct['claimLine'])?$acct['claimLine']->dateTime:$visit->dateOfTreatment;
			$patientName = ($patients[$patientId]->personId > 0)?$patients[$patientId]->person->displayName:'';
			$tmp['patientName'] = $patientName;
			$tmp['facility'] = $facilities[$facilityId];
			$providerName = ($providers[$providerId]->personId > 0)?$providers[$providerId]->person->displayName:'';
			$tmp['providerName'] = $providerName;
			$rows[] = $tmp;
		}

		$stmt = $db->query($mscSelect);
		$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
		while ($row = $stmt->fetch()) {
			$row['balance'] = (float)$row['billed'] - ((float)$row['paid'] + (float)$row['writeOff']);
			$rows[] = $row;
		}
		return $rows;
	}

	public static function getPaymentHistory(Array $filters) {
		$ret = array();
		$iterator = new PostingJournalIterator();
		$iterator->setFilters($filters);
		$ret[] = $iterator;
		$iterator = new PaymentIterator();
		$iterator->setFilters($filters);
		$ret[] = $iterator;
		$iterator = new WriteOffIterator();
		$iterator->setFilters($filters);
		$ret[] = $iterator;
		return $ret;
	}

	public function getOverallPayment() {
		$db = Zend_Registry::get('dbAdapter');
		$claim = new self();
		$orm = new PostingJournal();
		$sqlSelect = $db->select()
				->from(array('p'=>$orm->_table),'SUM(p.amount) AS paid')
				->join(array('c'=>$claim->_table),'c.claimLineId = p.claimLineId')
				->where('c.visitId = ?',$this->visitId)
				->where('c.procedureCode = ?',$this->procedureCode);
		$paid = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$paid = (float)$row['paid'];
		}
		return $paid;
	}

	public function getOverallWriteOff() {
		$db = Zend_Registry::get('dbAdapter');
		$claim = new self();
		$orm = new WriteOff();
		$sqlSelect = $db->select()
				->from(array('w'=>$orm->_table),'SUM(w.amount) AS writeOff')
				->join(array('c'=>$claim->_table),'c.claimLineId = w.claimLineId')
				->where('c.visitId = ?',$this->visitId)
				->where('c.procedureCode = ?',$this->procedureCode);
		$writeOff = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$writeOff = (float)$row['writeOff'];
		}
		return $writeOff;
	}

	public function getEnteredBy() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('audits','userId')
				->where('objectClass = ?','ClaimLine')
				->where('objectId = ?',$this->claimLineId)
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

	public function getBilledFee() {
		$billable = $this->baseFee;
		$discount = 0;
		if ($this->adjustedFee > 0) $discount = $this->baseFee - $this->adjustedFee;
		if ($discount < 0) $discount = 0;
		if ($discount > 0) $billable -= $discount;
		if ($billable < 0) $billable = 0;
		return $billable;
	}

}
