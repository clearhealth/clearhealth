<?php
/*****************************************************************************
*       AccountsController.php
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


class AccountsController extends WebVista_Controller_Action {

	protected $_session;

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
	}

	public function indexAction() {
		if (!isset($this->_session->filters)) {
			$filters = array();
			$filters['DateStart'] = date('Y-m-d',strtotime('-1 month'));
			$filters['DateEnd'] = date('Y-m-d');
			$filters['facilities'] = array();
			$filters['payers'] = array();
			$filters['providers'] = array();
			$tmp = array('active'=>0,'operator'=>'=','operand1'=>'','operand2'=>'');
			$filters['billed'] = $tmp;
			$filters['paid'] = $tmp;
			$filters['writeoff'] = $tmp;
			$filters['balance'] = $tmp;
			$this->_session->filters = $filters;
		}
		$this->view->filters = $this->_session->filters;

		$this->render();
	}

	public function listAction() {
		$posStart = $this->_getParam('posStart');
		if ($posStart < 0) {
			$msg = 'Invalid request';
			trigger_error($msg);
			throw new Exception($msg);
		}
		$sessions = $this->_session->filters;
		$filters = array();
		$filters['dateRange'] = array('start'=>$sessions['DateStart'],'end'=>$sessions['DateEnd']);
		if (isset($sessions['facilities']) && count($sessions['facilities']) > 0) { // practiceId_buildingId_roomId
			foreach ($sessions['facilities'] as $key=>$value) {
				if (!$value) continue;
				if (!isset($filters['facilities'])) $filters['facilities'] = array();
				$x = explode('_',$key);
				$practiceId = $x[0];
				$buildingId = $x[1];
				$roomId = $x[2];
				$filters['facilities'][] = array('practice'=>$practiceId,'building'=>$buildingId,'room'=>$roomId);
			}
		}
		if (isset($sessions['payers'])&& count($sessions['payers']) > 0) {
			foreach ($sessions['payers'] as $key=>$value) {
				if (!$value) continue;
				if (!isset($filters['payers'])) $filters['payers'] = array();
				$filters['payers'][] = $key;
			}
		}
		if (isset($sessions['providers'])&& count($sessions['providers']) > 0) {
			foreach ($sessions['providers'] as $key=>$value) {
				if (!$value) continue;
				if (!isset($filters['providers'])) $filters['providers'] = array();
				$filters['providers'][] = $key;
			}
		}
		$filters['billed'] = isset($sessions['billed'])?$sessions['billed']:0;
		$filters['paid'] = isset($sessions['paid'])?$sessions['paid']:0;
		$filters['writeoff'] = isset($sessions['writeoff'])?$sessions['writeoff']:0;
		$filters['balance'] = isset($sessions['balance'])?$sessions['balance']:0;

		$rows = array();
		$appointmentId = 0;
		$totalBilled = 0.00;
		$totalPaid = 0.00;
		$totalWriteoff = 0.00;
		$totalBalance = 0.00;

		$oneDay = 60 * 60 * 24;
		$today = time();

		$aging_0_30 = 0;
		$aging_31_60 = 0;
		$aging_61_90 = 0;
		$aging_91_120 = 0;
		$aging_120_plus = 0;

		$filters['closed'] = 1;
		foreach (ClaimLine::listCharges($filters) as $account) {
			$billed = (float)$account['billed'];
			$paid = (float)$account['paid'];
			$writeoff = (float)$account['writeOff'];
			$balance = (float)$account['balance'];

			$names = array('billed','paid','writeoff','balance');
			foreach ($names as $name) {
				if (!isset($filters[$name]) || !$filters[$name]['active']) continue;
				$operator = $filters[$name]['operator'];
				$operand1 = $filters[$name]['operand1'];
				$operand2 = $filters[$name]['operand2'];
				if ($operator == '=' && !($$name == $operand1)) continue 2;
				else if ($operator == '>' && !($$name > $operand1)) continue 2;
				else if ($operator == '>=' && !($$name >= $operand1)) continue 2;
				else if ($operator == '<' && !($$name < $operand1)) continue 2;
				else if ($operator == '<=' && !($$name <= $operand1)) continue 2;
				else if ($operator == 'between' && $operand2 > 0 && !($$name >= $operand1 && $$name <= $operand2)) {
					continue 2;
				}
			}
			$balance = abs($balance);
			$totalBilled += $billed;
			$totalPaid += $paid;
			$totalWriteoff += $writeoff;
			$totalBalance += $balance;
			$dateBilled = substr($account['dateBilled'],0,10);

			$db = strtotime($dateBilled);
			// calculate aging
			$aging = ($today - $db) / $oneDay;
			if ($aging <= 30) $aging_0_30 += $balance;
			else if ($aging <= 60) $aging_31_60 += $balance;
			else if ($aging <= 90) $aging_61_90 += $balance;
			else if ($aging <= 120) $aging_91_120 += $balance;
			else $aging_120_plus += $balance;


			$rows[] = array(
				'id'=>$account['id'],
				'data'=>array(
					substr($account['dateOfTreatment'],0,10), // Date
					$dateBilled, // Date Billed
					$account['patientName'], // Patient
					$account['payer'], // Payer
					'$'.$billed, // Billed
					'$'.$paid, // Paid
					'$'.$writeoff, // Write Off
					'$'.$balance, // Balance
					$account['facility'], // Facility
					$account['providerName'], // Provider
				),
			);
		}
		if (isset($rows[0])) {
			$rows[0]['userdata']['totalBilled'] = $totalBilled;
			trigger_error('total billed: '.$totalBilled);
			$rows[0]['userdata']['totalPaid'] = $totalPaid;
			trigger_error('total paid: '.$totalPaid);
			$rows[0]['userdata']['totalWriteoff'] = $totalWriteoff;
			trigger_error('total writeof: '.$totalWriteoff);
			$rows[0]['userdata']['totalBalance'] = $totalBalance;
			trigger_error('total balance: '.$totalBalance);

			$rows[0]['userdata']['aging_0_30'] = $aging_0_30;
			trigger_error('total aging 0-30: '.$aging_0_30);
			$rows[0]['userdata']['aging_31_60'] = $aging_31_60;
			trigger_error('total aging 31-60: '.$aging_31_60);
			$rows[0]['userdata']['aging_61_90'] = $aging_61_90;
			trigger_error('total aging 61-90: '.$aging_61_90);
			$rows[0]['userdata']['aging_91_120'] = $aging_91_120;
			trigger_error('total aging 91-120: '.$aging_91_120);
			$rows[0]['userdata']['aging_120_plus'] = $aging_120_plus;
			trigger_error('total aging 120+: '.$aging_120_plus);
		}
		$data = array('rows'=>$rows);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function advancedFiltersAction() {
		$this->view->balanceOperators = Claim::balanceOperators();
		$filters = $this->_session->filters;
		$this->view->filters = $filters;
		$facilityIterator = new FacilityIterator();
		$facilityIterator->setFilter(array('Practice','Building','Room'));
		$facilities = array();
		foreach($facilityIterator as $facility) {
			$key = $facility['Practice']->practiceId.'_'.$facility['Building']->buildingId.'_'.$facility['Room']->roomId;
			$name = $facility['Practice']->name.'->'.$facility['Building']->name.'->'.$facility['Room']->name;
			$facilities[$key] = $name;
		}
		$this->view->facilities = $facilities;
		$this->render();
	}

	public function setFiltersAction() {
		$params = $this->_getParam('filters');
		if (is_array($params)) {
			$filters = $this->_session->filters;
			foreach ($params as $key=>$value) {
				$filters[$key] = $value;
			}
			$this->_session->filters = $filters;
		}
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function patientAction() {
		if (!isset($this->_session->patientFilters)) $this->_session->patientFilters = array();
		$filters = $this->_session->patientFilters;
		if (!isset($filters['dateStart'])) {
			$filters['dateStart'] = date('Y-m-d',strtotime('-1 week'));
			$this->_session->patientFilters = $filters;
		}
		if (!isset($filters['dateEnd'])) {
			$filters['dateEnd'] = date('Y-m-d');
			$this->_session->patientFilters = $filters;
		}
		$this->view->filters = $filters;
		$this->view->personId = (int)$this->_getParam('personId');

		$facilities = array(''=>'');
		$facilityIterator = new FacilityIterator();
		$facilityIterator->setFilter(array('Practice','Building','Room'));
		foreach($facilityIterator as $facility) {
			$key = $facility['Practice']->practiceId.'_'.$facility['Building']->buildingId.'_'.$facility['Room']->roomId;
			$name = $facility['Practice']->name.'->'.$facility['Building']->name.'->'.$facility['Room']->name;
			$facilities[$key] = $name;
		}
		$this->view->facilities = $facilities;
		$payers = array(''=>'');
		foreach (InsuranceProgram::getInsurancePrograms() as $key=>$value) {
			$payers[$key] = $value;
		}
		$this->view->payers = $payers;
		$providers = array(''=>'');
		$provider = new Provider();
		foreach ($provider->getIter() as $row) {
			$providers[$row->personId] = $row->displayName;
		}
		$this->view->providers = $providers;
		$users = array(''=>'');
		$db = Zend_Registry::get('dbAdapter');
		$user = new User();
		$sqlSelect = $db->select()
				->from($user->_table)
				->order('username');
		foreach ($user->getIterator($sqlSelect) as $row) {
			$users[$row->userId] = $row->username;
		}
		$this->view->users = $users;
		$this->render();
	}

	public function listPatientAccountsAction() {
		$personId = (int)$this->_getParam('personId');
		$rows = array();
		$filters = $this->_session->patientFilters;
		$iterator = new VisitIterator();
		$iterator->setFilters(array(
			'patientId'=>$personId,
			'dateRange'=>$filters['dateStart'].':'.$filters['dateEnd'],
			'facilityId'=>$filters['facilityId'],
			'payerId'=>$filters['payerId'],
			'providerId'=>$filters['providerId'],
			'userId'=>$filters['userId'],
			'openClosed'=>1,
			'void'=>0,
		));
		foreach ($iterator as $item) {
			$visitId = (int)$item->visitId;
			//$acct = $item->accountSummary;
			//$total = $acct['total'];
			//$payment = $acct['payment'];
			//$writeOff = $acct['writeoff'];
			//$balance = $acct['balance'];

			$billed = 0;
			$total = 0;
			$pendingInsurance = 0; // TODO: connect to claimFiles?
			$paidInsurance = 0;
			$paidPatient = 0; // $payment + $writeOff;

			$summary = $item->accountDetails;
			// charges
			foreach ($summary['charges']['details'] as $row) {
				$billed += (float)$row->adjustedFee;
				$total += (float)$row->baseFee;
			}
			// misc charges
			foreach ($summary['miscCharges']['details'] as $row) {
				$billed += (float)$row->amount;
				$total += (float)$row->amount;
			}
			// payments
			foreach ($summary['payments']['details'] as $row) {
				if ($row instanceof Payment) $amount = (float)$row->unallocated;
				else $amount = (float)$row->amount;
				$payer = InsuranceProgram::getInsuranceProgram($row->payerId);
				if ((!strlen($payer) > 0) || strtolower(substr($payer,0,8)) == 'system->') $paidPatient += $amount;
				else $paidInsurance += $amount;
			}
			// writeoffs
			foreach ($summary['writeOffs']['details'] as $row) {
				$payer = InsuranceProgram::getInsuranceProgram($row->payerId);
				if (!strlen($payer) > 0) continue; // exclude/void writeoff if no payer specified
				$amount = (float)$row->amount;
				if (strtolower(substr($payer,0,8)) == 'system->') $paidPatient += $amount;
				else $paidInsurance += $amount;
			}
			$balance = $total - ($paidPatient + $paidInsurance + $pendingInsurance);
			if (!$billed > 0 && $total > 0) $billed = $total;

			$row = array();
			$row['id'] = $visitId;
			$row['data'] = array();
			$row['data'][] = $this->view->baseUrl.'/accounts.raw/list-patient-account-details?visitId='.$visitId;
			$row['data'][] = substr($item->dateOfTreatment,0,10);
			$row['data'][] = $item->insuranceProgram;
			$row['data'][] = '$'.number_format(abs($billed),2);
			$row['data'][] = '$'.number_format(abs($pendingInsurance),2);
			$row['data'][] = '$'.number_format(abs($paidInsurance),2);
			$row['data'][] = '$'.number_format(abs($paidPatient),2);
			$row['data'][] = '$'.number_format(abs($balance),2);
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listPatientAccountDetailsAction() {
		$visitId = (int)$this->_getParam('visitId');
		$visit = new Visit();
		$visit->visitId = $visitId;
		$visit->populate();
		$facility = $visit->facility; // Facility
		$providerDisplayName = $visit->providerDisplayName; // Provider

		$summary = $visit->accountDetails;
		$rows = array();
		foreach ($summary['claimFiles']['details'] as $data) {
			$claimFile = $data['claimFile'];
			$visit = $data['visit'];
			$claimFileId = (int)$claimFile->claimFileId;
			$row = array();
			$row['id'] = $visitId;
			$row['data'] = array();
			$row['data'][] = $claimFileId; // Id
			$row['data'][] = InsuranceProgram::getInsuranceProgram($claimFile->payerId); // Payer Name
			$row['data'][] = substr($claimFile->dateBilled,0,10); // Date Billed
			$row['data'][] = substr($claimFile->dateTime,0,10); // Date
			$row['data'][] = '$'.$claimFile->billed; // Billed
			$row['data'][] = '$'.$claimFile->paid; // Paid
			$row['data'][] = '$'.$claimFile->writeOff; // Write Off
			$row['data'][] = '$'.$claimFile->balance; // Balance
			$row['data'][] = ''; // Chk #
			$row['data'][] = $facility; // Facility
			$row['data'][] = $providerDisplayName; // Provider
			$row['data'][] = $claimFile->enteredBy; // Entered By

			if (!isset($rows[$id])) $rows[$id] = array();
			$rows[$id][] = $row;
		}

		$ctr = 0;
		// charges
		foreach ($summary['charges']['details'] as $row) {
			$amount = (float)$row->baseFee;
			$id = $row->visitId;
			if (!isset($rows[$id])) $rows[$id] = array();
			$rows[$id]['info'][] = array('amount'=>$amount,'type'=>'debit');
			$rows[$id][] = array(
				'id'=>$id.'-'.$ctr++,
				'data'=>array(
					'Charge', // Id
					InsuranceProgram::getInsuranceProgram($row->insuranceProgramId), // Payer Name
					substr($row->dateTime,0,10), // Date Billed
					'', // Date
					'$'.$amount, // Billed
					'', // Paid
					'', // Write Off
					'', // Balance
					'', // Chk #
					$facility, // Facility
					$providerDisplayName, // Provider
					$row->enteredBy, // Entered By
				),
			);
		}
		// misc charges
		foreach ($summary['miscCharges']['details'] as $row) {
			$amount = (float)$row->amount;
			$id = $row->miscChargeId;
			if (!isset($rows[$id])) $rows[$id] = array();
			$rows[$id]['info'][] = array('amount'=>$amount,'type'=>'debit');
			$rows[$id][] = array(
				'id'=>$id,
				'data'=>array(
					'Misc Charge', // Id
					'', // Payer Name
					substr($row->chargeDate,0,10), // Date Billed
					'', // Date
					'$'.$amount, // Billed
					'', // Paid
					'', // Write Off
					'', // Balance
					'', // Chk #
					$facility, // Facility
					$providerDisplayName, // Provider
					$row->enteredBy, // Entered By
				),
			);
		}
		// payments
		foreach ($summary['payments']['details'] as $row) {
			if ($row instanceOf PostingJournal) {
				$amount = (float)$row->amount;
				$id = $row->postingJournalId;
				$datePosted = $row->datePosted;
				$refNum = $row->payment->refNum;
			}
			else {
				$amount = (float)$row->unallocated;
				$id = $row->paymentId;
				$datePosted = $row->paymentDate;
				$refNum = $row->refNum;
			}
			if (!isset($rows[$id])) $rows[$id] = array();
			$rows[$id]['info'][] = array('amount'=>$amount,'type'=>'credit');
			$rows[$id][] = array(
				'id'=>$id,
				'data'=>array(
					'Payment', // Id
					InsuranceProgram::getInsuranceProgram($row->payerId), // Payer
					'', // Date Billed
					substr($datePosted,0,10), // Date
					'', // Billed
					'$'.$amount, // Paid
					'', // Write Off
					'', // Balance
					$refNum, // Chk #
					$facility, // Facility
					$providerDisplayName, // Provider
					$row->enteredBy, // Entered By
				),
			);
		}
		// writeoffs
		foreach ($summary['writeOffs']['details'] as $row) {
			$amount = (float)$row->amount;
			$id = $row->writeOffId;
			if (!isset($rows[$id])) $rows[$id] = array();
			$rows[$id]['info'][] = array('amount'=>$amount,'type'=>'credit');
			$rows[$id][] = array(
				'id'=>$id,
				'data'=>array(
					'Write Off', // Id
					InsuranceProgram::getInsuranceProgram($row->payerId), // Payer
					'', // Date Billed
					substr($row->timestamp,0,10), // Date
					'', // Billed
					'', // Paid
					'$'.$amount, // Write Off
					'', // Balance
					'', // Chk #
					$facility, // Facility
					$providerDisplayName, // Provider
					$row->enteredBy, // Entered By
				),
			);
		}
		ksort($rows);
		$data = array('rows'=>array());
		$balance = 0;
		foreach ($rows as $values) {
			$info = $values['info'];
			unset($values['info']);
			foreach ($values as $key=>$value) {
				$amount = $info[$key]['amount'];
				if ($info[$key]['type'] == 'debit') $balance += $amount;
				else $balance -= $amount;
				$value['data'][7] = '$'.abs($balance);
				$data['rows'][] = $value;
			}
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function setPatientFiltersAction() {
		$params = $this->_getParam('filters');
		if (is_array($params)) {
			$filters = $this->_session->patientFilters;
			foreach ($params as $key=>$value) {
				$filters[$key] = $value;
			}
			$this->_session->patientFilters = $filters;
		}
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function paymentAction() {
		$personId = (int)$this->_getParam('personId');
		$payment = new Payment();
		$payment->personId = $personId;
		$form = new WebVista_Form(array('name'=>'paymentId'));
		$form->setAction(Zend_Registry::get('baseUrl').'accounts.raw/process-payment');
		$form->loadORM($payment,'Payment');
		$form->setWindow('windowUnallocPayment');
		$this->view->form = $form;

		$guid = 'd1d9039a-a21b-4dfb-b6fa-ec5f41331682';
		$enumeration = new Enumeration();
		$enumeration->populateByGuid($guid);
		$closure = new EnumerationClosure();
		$this->view->paymentTypes = $closure->getAllDescendants($enumeration->enumerationId,1,true)->toArray('key','name');
		$this->render();
	}

	public function processPaymentAction() {
		$params = $this->_getParam('payment');
		$data = false;
		if (is_array($params)) {
			$payment = new Payment();
			$payment->populateWithArray($params);
			if (!strlen($payment->userId) > 0) $payment->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
			if (!strlen($payment->timestamp) > 0) $payment->timestamp = date('Y-m-d H:i:s');
			$payment->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function manualJournalAction() {
		$postingJournal = new PostingJournal();
		$form = new WebVista_Form(array('name'=>'journalId'));
		$form->setAction(Zend_Registry::get('baseUrl').'accounts.raw/process-manual-journal');
		$form->loadORM($postingJournal,'Journal');
		$form->setWindow('windowManualJournal');
		$this->view->form = $form;
		$this->render();
	}

	public function processManualJournalAction() {
		$params = $this->_getParam('journal');
		$data = array('error'=>'Invalid parameters');
		if (is_array($params)) {
			$postingJournal = new PostingJournal();
			$postingJournal->populateWithArray($params);
			$postingJournal->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
			$postingJournal->dateTime = date('Y-m-d H:i:s');
			$postingJournal->persist();
			$msg = 'Posting journal was successfully saved';
			$data = array('msg'=>$msg);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listClaimLinesAction() {
		$visitId = (int)$this->_getParam('visitId');
		$payerId = (int)$this->_getParam('payerId');
		$data = array();
		if ($visitId > 0 || $payerId > 0) {
			$iterator = new ClaimLineIterator();
			$filters = array();
			if ($visitId > 0) $filters['visitId'] = $visitId;
			if ($payerId > 0) $filters['insuranceProgramId'] = $payerId;
			$iterator->setFilters($filters);
			foreach ($iterator as $claimLine) {
				$data[$claimLine->claimLineId] = $claimLine->procedureCode.' : '.$claimLine->procedure; // Code
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
