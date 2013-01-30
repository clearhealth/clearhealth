<?php
/*****************************************************************************
*       Visit.php
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


class Visit extends WebVista_Model_ORM {
	protected $encounter_id;
	protected $encounter_reason;
	protected $patient_id;
	protected $building_id;
	protected $date_of_treatment;
	protected $treating_person_id;
	protected $timestamp;
	protected $last_change_user_id;
	protected $status;
	protected $occurence_id;
	protected $created_by_user_id;
	protected $payer_group_id;
	protected $current_payer;
	protected $room_id;
	protected $practice_id;
	protected $activePayerId;
	protected $closed;
	protected $void;
	protected $appointmentId;
	protected $_providerDisplayName = ''; //placeholder for use in visit list iterator
	protected $_locationName = ''; //placeholder for use in visit list iterator
	protected $_claimRule = array(); // placeholder for claim rule's warning or block message and event type

	protected $_legacyORMNaming = true;
	protected $_table = "encounter";
	protected $_primaryKeys = array("encounter_id");

	public function persist() {
		$visit = new self();
		$visit->visitId = $this->encounter_id;
		$visit->populate();
		$oldClosed = $visit->closed;
		$ret = parent::persist();
		$newClosed = $this->closed;
		if ($newClosed) {
			$newClaim = false;
			if ($newClosed && $oldClosed !== $newClosed && !ClaimLine::mostRecentClaim($this->encounter_id,true) > 0) { // recalculate claim lines if closed visit is new/reopened
				$newClaim = true;
			}
			$ret = self::recalculateClaims($this,$newClaim);
		}
		return $ret;
	}

	public static function recalculateClaims(self $visit,$newClaim=false) {
		$fees = $visit->calculateFees(true);
		$hasProcedure = false;
		if ($newClaim) $claimId = WebVista_Model_ORM::nextSequenceId('claimSequences');
		$copay = $visit->getCopay();

		$totalPaid = 0;
		$personId = (int)$visit->patientId;
		$userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$visitId = (int)$visit->visitId;
		$discountPayerId = InsuranceProgram::lookupSystemId('Discounts'); // ID of System->Discounts
		$creditPayerId = InsuranceProgram::lookupSystemId('Credit'); // ID of System->Credit
		$payerId = InsuranceProgram::lookupSystemId('Self Pay'); // ID of System->Self Pay
		foreach ($fees['details'] as $id=>$values) {
			// update claim or create if not exists
			$fee = (float)$values['fee'];
			$feeDiscounted = (float)$values['feeDiscounted'];
			$claimLine = new ClaimLine();
			$claimLine->populateWithPatientProcedure($values['orm'],$visit);
			if ($newClaim) {
				$claimLine->claimLineId = 0;
				$claimLine->claimId = $claimId;
			}
			$claimLine->baseFee = $fee;
			$claimLine->adjustedFee = $feeDiscounted;
			$claimLine->persist();

			$claimLineId = (int)$claimLine->claimLineId;

			$billable = $feeDiscounted;
			/*$discount = 0;
			if ($feeDiscounted > 0) $discount = $fee - $feeDiscounted;
			if ($discount < 0) $discount = 0;*/
			$discount = (float)$values['writeoff'];
			if ($newClaim && $discount > 0) {
				// add writeoffs
				$writeOff = new WriteOff();
				$writeOff->personId = $personId;
				$writeOff->claimLineId = $claimLineId;
				$writeOff->visitId = $visitId;
				$writeOff->appointmentId = $visit->appointmentId;
				$writeOff->amount = $discount;
				$writeOff->userId = $userId;
				$writeOff->timestamp = date('Y-m-d H:i:s');
				$writeOff->title = 'discount';
				$writeOff->payerId = $discountPayerId;
				$writeOff->persist();
				$billable -= $discount;
			}
			if ($newClaim && $billable > 0) {
				foreach ($copay['details'] as $paymentId=>$payment) {
					$amount = (float)$payment->unallocated;
					if (!$amount > 0) {
						unset($copay['details'][$paymentId]);
						continue;
					}
					if ($amount > $billable) $amount = $billable;
					$payment->allocated += $amount;
					$payment->payerId = $payerId;
					$payment->persist();
					$copay['details'][$paymentId] = $payment;
					$totalPaid += $amount;

					$postingJournal = new PostingJournal();
					$postingJournal->paymentId = (int)$payment->paymentId;
					$postingJournal->patientId = $personId;
					$postingJournal->payerId = $payerId;
					$postingJournal->claimLineId = $claimLineId;
					$postingJournal->visitId = $visitId;
					$postingJournal->amount = $amount;
					$postingJournal->note = 'copay posting';
					$postingJournal->userId = $userId;
					$dateTime = date('Y-m-d H:i:s');
					$postingJournal->datePosted = $dateTime;
					$postingJournal->dateTime = $dateTime;
					$postingJournal->persist();
					$billable -= $amount;
					if ($billable <= 0) break;
				}
			}

			$hasProcedure = true;
		}
		if ($newClaim && $copay['total'] > $totalPaid) { // if copay is greater than all claimlines reamining dollars are posted to credit program
			foreach ($copay['details'] as $paymentId=>$payment) {
				$amount = (float)$payment->unallocated;
				$payment->allocated += $amount;
				$payment->persist();

				$postingJournal = new PostingJournal();
				$postingJournal->paymentId = (int)$payment->paymentId;
				$postingJournal->patientId = $personId;
				$postingJournal->payerId = $creditPayerId;
				$postingJournal->visitId = $visitId;
				$postingJournal->amount = $amount;
				$postingJournal->note = 'remaining copay balance';
				$postingJournal->userId = $userId;
				$dateTime = date('Y-m-d H:i:s');
				$postingJournal->datePosted = $dateTime;
				$postingJournal->dateTime = $dateTime;
				$postingJournal->persist();
			}
		}
		if (!$hasProcedure) {
			$visitId = $visit->visitId;
			$payment = new Payment();
			foreach ($payment->getIteratorByVisitId($visitId) as $row) {
				// If visit has copay then at closing copay should be turned into unallocated payment (not associated with visit).
				$row->visitId = 0;
				$row->persist();
			}
		}
		else {
			$visit = ClaimRule::checkRules($visit,$fees);
		}
		return $visit;
	}

	function getIterator($objSelect = null) {
		return new VisitIterator($objSelect);
	}
	function setLocationName($locationName) {
		$this->_locationName = $locationName;
	}
	function getLocationName() {
		if (!strlen($this->_locationName) > 0 && $this->buildingId > 0) {
			$building = new Building();
			$building->buildingId = $this->buildingId;
			$building->populate();
			$this->_locationName = $building->name;
		}
		return $this->_locationName;
	}
	function setProviderDisplayName($providerDisplayName) {
		$this->_providerDisplayName = $providerDisplayName;
	}
	function getProviderDisplayName() {
		$provider = new Provider();
		$provider->person_id = $this->treating_person_id;
		$provider->populate();
		return $provider->person->getDisplayName();
	}

	public function getVisitId() {
		return $this->encounter_id;
	}

	public function setVisitId($id) {
		$this->encounter_id = $id;
	}

	public function getInsuranceProgram() {
		return InsuranceProgram::getInsuranceProgram($this->activePayerId);
	}

	public function ormEditMethod($ormId,$isAdd) {
		return $this->ormVisitTypeEditMethod($ormId,$isAdd);
	}

	public function ormVisitTypeEditMethod($ormId,$isAdd) {
		$controller = Zend_Controller_Front::getInstance();
		$request = $controller->getRequest();
		$enumerationId = (int)$request->getParam('enumerationId');

		$view = Zend_Layout::getMvcInstance()->getView();
		$params = array();
		if ($isAdd) {
			$params['parentId'] = $enumerationId;
			unset($_GET['enumerationId']); // remove enumerationId from params list
			$params['grid'] = 'enumItemsGrid';
		}
		else {
			$params['enumerationId'] = $enumerationId;
			$params['ormId'] = $ormId;
		}
		return $view->action('edit-type','visit-details',null,$params);
	}

	public static function ormClasses() {
		return array(
			'Visit' => 'Visit Type',
			'ProcedureCodesCPT' => 'Procedure',
			'DiagnosisCodesICD' => 'Diagnosis',
		);
	}

	public function populateByAppointmentId($appointmentId = null) {
		if ($appointmentId === null) {
			$appointmentId = $this->appointmentId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('appointmentId = ?',(int)$appointmentId);
		$ret = $this->populateWithSql($sqlSelect->__toString());
		$this->postPopulate();
		return $ret;
	}

	public function getProviderId() {
		return $this->treating_person_id;
	}

	public function setProviderId($id) {
		$this->treating_person_id = (int)$id;
	}

	public function getDisplayStatus() {
		if ($this->void) {
			return 'void';
		}
		else if ($this->closed) {
			return 'closed';
		}
		else {
			return 'open';
		}
	}

	public function populateLatestVisit($personId=null) {
		if ($personId === null) $personId = $this->patient_id;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('patient_id = ?',(int)$personId)
				->order('date_of_treatment DESC')
				->limit(1);
		return $this->populateWithSql($sqlSelect->__toString());
	}

	public function calculateFees($recompute=null) { // pass true or false to override visit.closed checking
		if ($recompute === null) $recompute = ($this->closed)?false:true;
		$visitId = (int)$this->encounter_id;
		$total = 0;
		$discounted = 0;
		$visitFlat = 0;
		$visitPercentage = 0;
		$codeFlat = 0;
		$codePercentage = 0;

		$discountApplied = array();
		if ($recompute) {
			$insuranceProgramId = (int)$this->activePayerId;
			$dateOfVisit = date('Y-m-d',strtotime($this->date_of_treatment));
			$statistics = PatientStatisticsDefinition::getPatientStatistics((int)$this->patient_id);
			$familySize = isset($statistics['family_size'])?$statistics['family_size']:0;
			$monthlyIncome = isset($statistics['monthly_income'])?$statistics['monthly_income']:0;

			$retDiscount = DiscountTable::checkDiscount($insuranceProgramId,$dateOfVisit,$familySize,$monthlyIncome);
			if ($retDiscount !== false) {
				$discount = (float)$retDiscount['discount'];
				switch ($retDiscount['discountType']) {
					case DiscountTable::DISCOUNT_TYPE_FLAT_VISIT:
						$discountApplied[] = 'Flat Visit: $'.$discount;
						$visitFlat += $discount;
						break;
					case DiscountTable::DISCOUNT_TYPE_FLAT_CODE:
						$discountApplied[] = 'Flat Code: $'.$discount;
						$codeFlat += $discount;
						break;
					case DiscountTable::DISCOUNT_TYPE_PERC_VISIT:
						$discountApplied[] = 'Percentage Visit: '.$discount.'%';
						$visitPercentage += ($discount / 100);
						break;
					case DiscountTable::DISCOUNT_TYPE_PERC_CODE:
						$discountApplied[] = 'Percentage Code: '.$discount.'%';
						$codePercentage += ($discount / 100);
						break;
				}
			}
		}
		else {
			$claimLineFees = array();
			$iterator = ClaimLine::mostRecentClaims($visitId);
			foreach ($iterator as $claimLine) {
				$code = $claimLine->procedureCode;
				if (!isset($claimLineFees[$code])) $claimLineFees[$code] = array('baseFee'=>0,'adjustedFee'=>0);
				$claimLineFees[$code]['baseFee'] += (float)$claimLine->baseFee;
				$claimLineFees[$code]['adjustedFee'] += (float)$claimLine->adjustedFee;
			}
		}

		$details = array();
		$iterator = new PatientProcedureIterator();
		$iterator->setFilters(array('visitId'=>$visitId));
		$firstProcedureId = null;

		foreach ($iterator as $patientProcedure) {
			$patientProcedureId = (int)$patientProcedure->patientProcedureId;
			$code = $patientProcedure->code;
			$quantity = (int)$patientProcedure->quantity;
			$writeoff = 0;
			if ($recompute) {
				$fee = '-.--';
				$feeDiscounted = '-.--';
				$discountedRate = '';
				$retFee = FeeSchedule::checkFee($insuranceProgramId,$dateOfVisit,$code);
				if ($retFee !== false && (float)$retFee['fee'] != 0) {
					$fee = (float)$retFee['fee'];
					$tmpFee = 0;
					for ($i = 1; $i <= 4; $i++) {
						$modifier = 'modifier'.$i;
						if (!strlen($patientProcedure->$modifier) > 0) continue;
						switch ($patientProcedure->$modifier) {
							case $retFee['modifier1']:
								$tmpFee += (float)$retFee['modifier1fee'];
								break 2;
							case $retFee['modifier2']:
								$tmpFee += (float)$retFee['modifier2fee'];
								break 2;
							case $retFee['modifier3']:
								$tmpFee += (float)$retFee['modifier3fee'];
								break 2;
							case $retFee['modifier4']:
								$tmpFee += (float)$retFee['modifier4fee'];
								break 2;
						}
					}
					if ($tmpFee > 0) $fee = $tmpFee;

					if ($quantity > 0) {
						$fee *= $quantity;
						$feeDiscounted *= $quantity;
					}

					// calculate discounts
					if ($codeFlat > 0) {
						$tmpDiscount = ($fee - $codeFlat);
						if ($tmpDiscount < 0) $tmpDiscount = 0;
						$feeDiscounted += $tmpDiscount;
						$writeoff = $tmpDiscount;
					}
					if ($firstProcedureId !== null && $visitFlat > 0) {
						$writeoff = $fee;
						trigger_error('VISIT FLAT: '.$visitFlat);
						trigger_error('WRITEOFF: '.$writeoff);
					}
					if ($codePercentage > 0) {
						$tmpDiscount = ($feeDiscounted * $codePercentage);
						if ($tmpDiscount < 0) $tmpDiscount = 0;
						$feeDiscounted += $tmpDiscount;
						$writeoff = $tmpDiscount;
					}
				}
				if ($firstProcedureId === null) $firstProcedureId = $patientProcedureId;
			}
			else {
				if (isset($claimLineFees[$code])) {
					$fee = $claimLineFees[$code]['baseFee'];
					$feeDiscounted = $claimLineFees[$code]['adjustedFee'];
				}
				else {
					$fee = $patientProcedure->baseFee;
					$feeDiscounted = $patientProcedure->adjustedFee;
				}
				if ($quantity > 0) {
					$fee *= $quantity;
					$feeDiscounted *= $quantity;
				}
			}
			/*$quantity = (int)$patientProcedure->quantity;
			if ($quantity > 0) {
				$fee *= $quantity;
				$feeDiscounted *= $quantity;
			}*/
			$total += $fee;
			$discounted += (float)$feeDiscounted;
			$details[$patientProcedureId] = array();
			$details[$patientProcedureId]['orm'] = $patientProcedure;
			$details[$patientProcedureId]['fee'] = $fee;
			$details[$patientProcedureId]['feeDiscounted'] = $feeDiscounted;
			$details[$patientProcedureId]['writeoff'] = $writeoff;
		}
		if ($visitFlat > 0) {
			$discounted += $visitFlat;
			// update the first procedure
			if ($firstProcedureId !== null) {
				$details[$firstProcedureId]['feeDiscounted'] += $visitFlat;
				$writeoff = $details[$firstProcedureId]['fee'] - $details[$firstProcedureId]['feeDiscounted'];
				if ($writeoff < 0) $writeoff = 0;
				$details[$firstProcedureId]['writeoff'] = $writeoff;
			}
		}
		if ($visitPercentage > 0) {
			$discounted += ($discounted * $visitPercentage);
			// update the first procedure
			if ($firstProcedureId !== null) $details[$firstProcedureId]['feeDiscounted'] += ($details[$firstProcedureId]['feeDiscounted'] * $visitFlat);
		}
		$row = array();
		$row['discountApplied'] = $discountApplied;
		$row['details'] = $details;
		$row['total'] = $total;
		$row['discounted'] = $discounted;
		return $row;
	}

	public function syncClaimsInsurance() {
		$visitId = (int)$this->encounter_id;
		$payerId = (int)$this->activePayerId;
		$db = Zend_Registry::get('dbAdapter');
		$claim = new ClaimLine();
		$sql = 'UPDATE `'.$claim->_table.'` SET `insuranceProgramId` = '.$payerId.' WHERE `visitId` = '.$visitId;
		return $db->query($sql);
	}

	public function getIteratorByPersonId($personId=null) {
		if ($personId === null) $personId = $this->patient_id;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('patient_id = ?',(int)$personId)
				->order('date_of_treatment DESC');
		return $this->getIterator($sqlSelect);
	}

	public function getAccountDetails() {
		$visitId = (int)$this->encounter_id;
		$ret = array(
			'claimFiles'=>array(
				'details'=>array(),
				'totals'=>array(),
			),
			'charges'=>array(
				'details'=>array(),
				'totals'=>array(),
			),
			'miscCharges'=>array(
				'details'=>array(),
				'totals'=>array(),
			),
			'payments'=>array(
				'details'=>array(),
				'totals'=>array(),
			),
			'writeOffs'=>array(
				'details'=>array(),
				'totals'=>array(),
			),
		);
		$totalBilled = 0;
		$totalPaid = 0;
		$totalWO = 0;
		$totalBalance = 0;
		foreach (ClaimFile::listClaims(array('visitId'=>$visitId)) as $data) {
			$claimFile = $data['claimFile'];
			$totalBilled += (float)$claimFile->billed;
			$totalPaid += (float)$claimFile->paid;
			$totalWO += (float)$claimFile->writeOff;
			$totalBalance += (float)$claimFile->balance;
			$ret['claimFiles']['details'][] = $data;
		}
		$ret['claimFiles']['totals'] = array(
			'billed'=>$totalBilled,
			'paid'=>$totalPaid,
			'writeOff'=>$totalWO,
			'balance'=>$totalBalance,
		);

		$totalBilled = 0;
		if ($this->closed) {
			$iterator = ClaimLine::mostRecentClaims($visitId);
			foreach ($iterator as $claimLine) {
				$totalBilled += (float)$claimLine->baseFee;
				$ret['charges']['details'][] = $claimLine;
			}
		}
		else {
			$fees = $this->calculateFees();
			$totalBilled += $fees['total'];
			$totalWO += $fees['discounted'];
			$discount = $fees['total'] - $fees['discounted'];
			if ($discount < 0) $discount = 0;

			$claimLine = new ClaimLine();
			$claimLine->baseFee = $fees['total'];
			$claimLine->adjustedFee = $fees['discounted'];
			$claimLine->visitId = $visitId;
			$claimLine->dateTime = $this->date_of_treatment;
			$claimLine->insuranceProgramId = $this->activePayerId;
			$claimLine->enteredBy = $this->getEnteredBy();
			$ret['charges']['details'][] = $claimLine;

			if ($discount > 0) {
				$writeOff = new WriteOff();
				$writeOff->amount = $discount;
				$writeOff->writeOffId = time();
				$writeOff->payerId = InsuranceProgram::lookupSystemId('Discounts'); // ID of System->Discounts
				$writeOff->timestamp = $this->date_of_treatment;
				$writeOff->userId = ($this->last_change_user_id > 0)?$this->last_change_user_id:$this->created_by_user_id;
				$ret['writeOffs']['details'][] = $writeOff;
			}
		}
		$ret['charges']['totals'] = array(
			'billed'=>$totalBilled,
			'paid'=>0,
			'writeOff'=>0,
			'balance'=>0,
		);

		// misc charges
		$miscCharge = new MiscCharge();
		$totalBilled = 0;
		foreach ($miscCharge->getIteratorByVisitId($visitId) as $row) {
			$totalBilled += (float)$row->amount;
			$ret['miscCharges']['details'][] = $row;
		}
		$ret['miscCharges']['totals'] = array(
			'billed'=>$totalBilled,
			'paid'=>0,
			'writeOff'=>0,
			'balance'=>0,
		);

		$iterators = ClaimLine::getPaymentHistory(array('visitId'=>$visitId,'unposted'=>true));
		$totalPaid = 0;
		$totalWO = 0;
		foreach ($iterators as $iterator) {
			foreach ($iterator as $item) {
				if ($item instanceof PostingJournal) {
					$totalPaid += (float)$item->amount;
					$ret['payments']['details'][] = $item;
				}
				else if ($item instanceof Payment) {
					$totalPaid += (float)$item->unallocated;
					$ret['payments']['details'][] = $item;
				}
				else {
					$totalWO += (float)$item->amount;
					$ret['writeOffs']['details'][] = $item;
				}
			}
		}
		// payments
		$ret['payments']['totals'] = array(
			'billed'=>0,
			'paid'=>$totalPaid,
			'writeOff'=>0,
			'balance'=>0,
		);
		// writeoffs
		$ret['writeOffs']['totals'] = array(
			'billed'=>0,
			'paid'=>0,
			'writeOff'=>$totalWO,
			'balance'=>0,
		);
		return $ret;
	}

	public function getFacility() {
		$ret = '';
		$roomId = (int)$this->roomId;
		if ($roomId > 0) {
			$room = new Room();
			$room->roomId = $roomId;
			$room->populate();
			$ret = $room->building->name.'->'.$room->name;
		}
		return $ret;
	}

	public function getCopay() {
		// payments with appointmentId
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('payment')
				->where('personId = ?',(int)$this->patientId)
				->where('encounter_id = ?',(int)$this->encounter_id)
				->where('(amount - allocated) > 0')
				->where('appointmentId != 0');
		$total = 0;
		$details = array();
		$stmt = $db->query($sqlSelect);
		$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
		while ($row = $stmt->fetch()) {
			$payment = new Payment();
			$payment->populateWithArray($row);
			$total += $payment->unallocated;
			$details[$row['payment_id']] = $payment;
		}
		return array(
			'total'=>$total,
			'details'=>$details,
		);
	}

	public function getUnallocatedFunds() {
		return Payment::listUnallocatedFunds($this->patientId);
	}

	public function getUniqueClaimIds() {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new ClaimLine();
		$sqlSelect = $db->select()
				->from($orm->_table,array('claimId'))
				->where('visitId = ?',$this->encounter_id)
				->order('claimId DESC')
				->group('claimId');
		$ret = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$ret[] = $row['claimId'];
			}
		}
		return $ret;
	}

	public function getUniqueClaims() {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new ClaimLine();
		$sqlSelect = $db->select()
				->from($orm->_table,array('claimId','insuranceProgramId AS payerId'))
				->where('visitId = ?',$this->encounter_id)
				->order('claimId DESC')
				->group('claimId');
		$ret = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				foreach ($row as $key=>$value) $ret[$key][] = $value;
			}
		}
		return $ret;
	}

	public function getAccountSummary() {
		$visitId = (int)$this->visitId;
		$total = 0;
		$billed = 0;
		$writeoff = 0;
		$balance = 0;
		$baseFee = 0;
		$adjustedFee = 0;
		$miscCharge = MiscCharge::total(array('visitId'=>$visitId));
		$payment = Payment::total(array('visitId'=>$visitId));
		$payment += Payment::unpostedTotal($visitId);
		$writeoff += WriteOff::total(array('visitId'=>$visitId));
		if ($this->closed) {
			$iterator = ClaimLine::mostRecentClaims($visitId);
			foreach ($iterator as $claimLine) {
				$baseFee += (float)$claimLine->baseFee;
				$adjustedFee += (float)$claimLine->adjustedFee;
			}
		}
		else {
			$fees = $this->calculateFees();
			$baseFee += $fees['total'];
			$adjustedFee = $fees['discounted'];
			if (!$writeoff > 0 && $adjustedFee > 0) $writeoff += ($baseFee - $adjustedFee);
		}

		$total = $baseFee + $miscCharge;
		$billed = $miscCharge;
		if ($adjustedFee > 0) $billed += $adjustedFee;
		if (!$billed > 0) $billed = $total;
		$balance = abs($total) - ($payment + $writeoff);
		if ($balance == $total) $balance = $billed; // use total billed if balance equals to total

		$ret = array();
		$ret['total'] = $total;
		$ret['billed'] = $billed;
		$ret['payment'] = $payment;
		$ret['balance'] = $balance;
		$ret['writeoff'] = $writeoff;
		$ret['baseFee'] = $baseFee;
		$ret['adjustedFee'] = $adjustedFee;
		$ret['miscCharge'] = $miscCharge;
		if (isset($claimLine)) $ret['claimLine'] = $claimLine;
		return $ret;
	}

	public function getEnteredBy() {
		$userId = ($this->last_change_user_id > 0)?$this->last_change_user_id:$this->created_by_user_id;
		$ret = '';
		if ($userId > 0) {
			$user = new User();
			$user->userId = $userId;
			$user->populate();
			$ret = $user->username;
		}
		return $ret;
	}

	public function hasPayments() {
		$ret = false;
		$visitId = (int)$this->encounter_id;
		$db = Zend_Registry::get('dbAdapter');
		$orm = new Payment();
		$sqlSelect = $db->select()
				->from($orm->_table)
				->where('encounter_id = ?',$visitId)
				->limit(1);
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		else {
			$orm = new PostingJournal();
			$sqlSelect = $db->select()
					->from($orm->_table)
					->where('visitId = ?',$visitId)
					->limit(1);
			if ($row = $db->fetchRow($sqlSelect)) {
				$ret = true;
			}
		}
		return $ret;
	}

}
