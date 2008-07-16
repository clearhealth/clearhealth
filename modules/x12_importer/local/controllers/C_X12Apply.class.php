<?php
$loader->requireOnce('/includes/X12Transaction.class.php');
$loader->requireOnce('includes/Datasource_array.class.php');
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('includes/AJAXController.class.php');
class C_X12Apply extends controller {

	function actionView($id = 1) {
		$sourceId = $id;
		$session =& Celini::sessionInstance();
		$session->set('X12Import:sourceId',$id);

		$head =& Celini::HTMLHeadInstance();
		$head->addNewJs('payment','templates/eob/payment.js');



		$data = $this->_transSummary($id);

		$ajax =& Celini::ajaxInstance();
		$ajax->stubs[] = 'Controller';
		$ajax->jsLibraries[] = array('scriptaculous');
		$this->view->assign('transactions',$this->actionTransactions());
		$this->view->assign('PROCESS_ACTION',Celini::link('ProcessEOB','X12Apply',false));
		$this->view->assign('currentTransactions',$session->get('X12Import:currentTransaction',0));
		$helper =& Celini::ajaxInstance();
		$this->view->assign('data',$helper->jsonEncode($data));

		$transClaims = array();
		$firstClaim = false;
		$firstTrans = false;
		foreach($this->_getTransactionGroup($id) as $transaction) {
			$id = $transaction->summary->get('identifier');
			if (!$firstTrans) {
				$firstTrans = $id;
			}
			$transClaims[$id] = array();
			foreach($transaction->details as $detail) {
				if (!$firstClaim) {
					$firstClaim = $detail->get('identifier');
				}
				$history =& Celini::newOrdo('X12TransactionHistory',array($id,$detail->get('identifier')),'ByClaim');
				$transClaims[$id][$detail->get('identifier')] = $history->get('applied_date');
			}
		}
		$this->view->assign('transClaims',$helper->jsonEncode($transClaims));
		$this->view->assign('currentClaim',$session->get('X12Import:currentClaim',$firstClaim));
		$transactions = $this->_getTransactionGroup($sourceId);
		$transaction = $transactions[$session->get('X12Import:currentTransaction',0)];

		$this->view->assign('currentTransactionId',$helper->jsonEncode($transaction->summary->get('identifier')));

		return $this->view->render('view.html');
	}

	function actionTransactions() {
		$session =& Celini::sessionInstance();
		$id = $session->get('X12Import:sourceId');
		$data = $this->_transSummary($id);

		$currentTrans = $session->get('X12Import:currentTransaction',0);
		$this->view->assign('currentTransactions',$currentTrans);

		$queueCount = 0;
		$next = $currentTrans;
		$n = false;
		foreach($data as $key => $trans) {
			if ($trans['num_to_apply'] > 0) {
				$queueCount++;
			}
			if (isset($_POST['next']) && $_POST['next'] == 'true') {
				if ($n) {
					$currentTrans = $key;
					$n = false;
				}
				if ($key == $next) {
					$n = true;
				}
			}
		}
		if ($n && $next == $currentTrans) {
			$currentTrans = 0;
		}

		$session->set('X12Import:currentTransaction',$currentTrans);

		$typeLookup = array(
			'C' => 'Payment, Remittance Advice',
			'D' => 'Payment only',
			'H' => 'Notification only',
			'I' => 'Remittance info only',
			'P' => 'Prenotification of Future Transfers',
			'U' => 'Split Payment and Remittance',
			'X' => 'Handling Party\'s Option to Split Payment and Remittance'
			);

		$this->view->assign('queueCount',$queueCount);
		$this->view->assign('queueTotal',count($data));
		$this->view->assign('typeLookup',$typeLookup);
		$this->view->assign('currentTransaction',$currentTrans);	
		$this->view->assign('currentTrans',$data[$currentTrans]);	

		return $this->view->render('transactions.html');
	}

	function actionTransactionClaims($transId) {
		$session =& Celini::sessionInstance();
		$id = $session->get('X12Import:sourceId');
		$data = $this->_transLines($id,$transId);
		$labels = array(
				'line_id' => 'ID',
				'status'	=> 'Status',
				'payment'	=> 'Payment',
				'date'		=> 'Date',
				'patient_last'	=> 'Patient Last',
				'patient_first'	=> 'First',
				'patient_id'	=> 'ID',
				'patient_idtype'=> 'ID Type',
				'applied_on'	=> 'Applied'
		);

		$ds =& new Datasource_array();
		$ds->setup($labels,$data);

		$ds->registerFilter('line_id',array($this,'_linkLink'));

		$grid =& new cGrid($ds);
		$grid->orderLinks = false;
		$grid->updateAttribute('table',0,array('id'=>'claimGrid'));
		$this->view->assign_by_ref('clGrid',$grid);

		$transactions = $this->_getTransactionGroup($id);

		foreach($transactions as $transaction) {
			if ($transId === $transaction->summary->identifier) {
				break;
			}
		}
		$this->view->assign('payer',$transaction->payer);


		$fx12 = false;
		if (isset($transaction->fakeX12) && $transaction->fakeX12) {
			$fx12 = true;
		}

		return array('html'=>$this->view->render('claims.html'),'fakeX12'=>$fx12);
	}

	function actionEob() {
		$session =& Celini::sessionInstance();
		$id = $session->get('X12Import:sourceId');

		$transId = $_POST['transId'];
		$claimId = $_POST['claimId'];

		// claimId matches the fbclaim.claim_id
		$fbclaim =& Celini::newOrdo('FBClaim',$claimId);
		$chclaim =& Celini::newOrdo('ClearhealthClaim',$fbclaim->get('claim_identifier'),'byIdentifier');

		$subscriber =& $fbclaim->childEntity('FBSubscriber');
		$encounter =& Celini::newOrdo('Encounter',$chclaim->get('encounter_id'));

		//var_dump($subscriber);
		
		// get who we think the correct payer should be, lots of fallback to always at least pick a payer that the user has
		$ir =& Celini::newOrdo('InsuredRelationship',
			array($encounter->get('patient_id'),$subscriber->get('group_name'),$subscriber->get('group_number')),
			'byGroup');

		$ac = new AJAXController();
		$html = $ac->dispatchAction('Eob','payment',array('id'=>$chclaim->get('id')));

		$programId = 0;
		$programId = $encounter->get('patient_id');
		if ($ir->isPopulated()) {
			$programId = $ir->get('insurance_program_id');
		}
		else {
			$programs = InsuredRelationship::fromPersonId($encounter->get('patient_id'));
			if (isset($programs[0])) {
				$programId = $programs[0]->get('insurance_program_id');
			}
		}

		// get the per claimline payment info
		$transactions = $this->_getTransactionGroup($id);

		foreach($transactions as $transaction) {
			if ($transId === $transaction->summary->identifier) {
				foreach($transaction->details as $claim) {
					if ($claim->identifier == $claimId) {
						break;
					}
				}
				break;
			}
		}

		$lines = array();
		$adjs = array();
		foreach($claim->lines as $line) {
			if (!isset($line->procedure)) {
			}
			else {
				$lines[] = array(
					'code' => $line->get('procedureCode'),
					'codeType' => $line->get('procedureType'),
					'amount' => $line->get('paymentAmount'),
					'contractualObligations' => $line->get('COAmount'),
					'patientResponsibility' => $line->get('PRAmount'),
					'serviceData' => $line->get('serviceDate'),
					);
				foreach($line->adjustments as $adj) {
					$adjs[$line->get('procedureCode')] = array(
						'group' => $adj->group,
						'reason' => $adj->reason,
						'amount' => $adj->amount,
					);
				}
			}
		}

		$data = array(
			'payer'=>$programId,
			'status' =>$claim->get('status'),
			'lines'=>$lines
		);

		$info = $this->actionClaimInfo($transId,$claimId);

		
		return array(
			'data' => $data,
			'html' => $html,
			'info' => $info,
			'infoData' => array(
				'Total Charge'=>$claim->get('totalChargeAmount'),
				'Patient Responsibility'=>$claim->get('patientResponsibilityAmount'),
				'Plan Type'=>$claim->get('planIndicator')
			),
			'adjustments' => $adjs,
			'chClaimId' => $chclaim->get('id')
		);
	}

	function actionClaimInfo($transactionId,$claimId) {
		// get the per claimline payment info
		$session =& Celini::sessionInstance();
		$id = $session->get('X12Import:sourceId');
		$transactions = $this->_getTransactionGroup($id);

		foreach($transactions as $transaction) {
			if ($transactionId === $transaction->summary->identifier) {
				foreach($transaction->details as $claim) {
					if ($claim->identifier == $claimId) {
						break;
					}
				}
			}
		}


		$history =& Celini::newOrdo('X12TransactionHistory',array($transactionId,$claimId),'ByClaim');

		$this->view->assign_by_ref('history',$history);
		$this->view->assign('transaction_id',$transactionId);
		$this->view->assign('claim',$claim);
		return $this->view->render('claim_info.html');
	}

	function _linkLink($id,$row) {
		if ($row['payment'] == 0) {
			$row['payment'] = '0.00';
		}
		return "<a href=\"javascript:applyClaim('$id')\">$id</a>";
	}

	function _transSummary($id) {
		$ret = array();
		$history =& Celini::newOrdo('X12TransactionHistory');
		foreach($this->_getTransactionGroup($id) as $transaction) {
			$ret[] = array(
				'id'=> $transaction->summary->get('identifier'),
				'date'=> $transaction->summary->get('date'),
				'type'=> $transaction->summary->get('transactionType'),
				'amount'=> $transaction->summary->get('amount'),
				'num_to_apply'=> ( count($transaction->details)- $history->numAppliedClaims($transaction->summary->get('identifier')))
				);
		}
		return $ret;
	}

	function _transLines($id,$transId) {
		$transactions = $this->_getTransactionGroup($id);

		foreach($transactions as $transaction) {
			if ($transId === $transaction->summary->identifier) {
				break;
			}
		}

		$ret = array();
		foreach($transaction->details as $detail) {
			$history =& Celini::newOrdo('X12TransactionHistory',array($transId,$detail->get('identifier')),'ByClaim');
			$ret[$detail->get('identifier')] = array(
				'line_id'	=> $detail->get('identifier'),
				'status'	=> $detail->get('status'),
				'payment'	=> $detail->get('claimPaymentAmount'),
				'date'		=> $detail->get('claimDate'),
				'patient_last'	=> $detail->patient->get('nameLast'),
				'patient_first'	=> $detail->patient->get('nameFirst'),
				'patient_id'	=> $detail->patient->get('id'),
				'patient_idtype'=> $detail->patient->get('idType'),
				'applied_on' 	=> $history->get('applied_date')
				);
		}
		return $ret;
	}

	function _getTransactionGroup($id) {

		if (!isset($_SESSION['X12Import']['transactions'][$id])) {
			Celini::raiseError('No transactions loaded: '.$id);
		}

		return unserialize($_SESSION['X12Import']['transactions'][$id]);
	}

	function processProcessEOB() {
		$GLOBALS['loader']->requireOnce('controllers/C_Eob.class.php');
		$c = new C_Eob();
		$claimId = $this->GET->get('id');
		$c->processPayment_edit($claimId);

		$h = $this->POST->get('history');

		$session =& Celini::sessionInstance();
		$id = $session->get('X12Import:sourceId');

		$history =& Celini::newOrdo('X12TransactionHistory');
		$history->set('source_id',$id);
		$history->set('transaction_id',$h['transaction_id']);
		$history->set('claim_id',$h['claim_id']);
		$history->set('applied_date',date('Y-m-d H:i:s'));
		$history->set('applied_by',$this->_me->get_id());
		$history->set('payment_id',$c->payment_id);
		$history->persist();

		$transactions = $this->_getTransactionGroup($id);

		foreach($transactions as $transaction) {
			if ($h['transaction_id'] === $transaction->summary->identifier) {
				foreach($transaction->details as $claim) {
					if ($claim->identifier == $h['claim_id']) {
						break;
					}
				}
				break;
			}
		}

		$data =& Celini::newOrdo('X12TransactionData');
		$data->set('history_id',$history->get('id'));
		$data->set('raw',serialize($transaction));
		$data->set('transaction_status',$claim->get('status'));
		$data->set('payment_amount',$claim->get('claimPaymentAmount'));
		$data->set('total_charge',$claim->get('totalChargeAmount'));
		$data->set('patient_responsibility',$claim->get('patientResponsibilityAmount'));
		$data->persist();
	}

	function actionProcessEOB() {
		return '<p><b>Payment Recorded</b></p>';
	}
}
?>
