<?php
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce('includes/transaction/TransactionManager.class.php');

class C_Eob extends Controller {
	var $ppp = null;
	var $patientPaymentPlans = null;

	function C_Eob() {
		parent::Controller();
		$ajax =& Celini::ajaxServerInstance();
		$GLOBALS['loader']->requireOnce('controllers/C_PatientPaymentPlan.class.php');
		$ppp = new C_PatientPaymentPlan();
		$ajax->registerClass($ppp,'PatientPaymentPlan');
		$this->ppp =& $ppp;
	}

	function actionPayment_edit($claim_id) {
		$head =& Celini::HTMLHeadInstance();
		$head->addNewJs('payment','templates/eob/payment.js');

		$this->view->assign('REBILL_ACTION',Celini::link('rebillSelfPay','Eob',false));

		$claim =& Celini::newOrdo('ClearhealthClaim',$claim_id);
		$this->view->assign('BILLNEXT_ACTION',Celini::link('RebillNextPayer','Eob',false));
		$encounter =& Celini::newOrdo('Encounter',$claim->get('encounter_id'));
		$patient =& Celini::newOrdo('Patient',$encounter->get('patient_id'));
		$paymentplan =& Celini::newORDO('PatientPaymentPlan');
		if(is_null($this->patientPaymentPlans)) {
			$payment_finder =& $encounter->getChildrenFinder($paymentplan);
			$payment_finder->addCriteria('patient_payment_plan.balance > 0');
			$plans = $payment_finder->find();
			$this->patientPaymentPlans = $plans->toArray();
		}
		$payment_plans =& $this->patientPaymentPlans;
		$this->view->assign_by_ref('patientpaymentplans',$payment_plans);

		$codingData =& Celini::newOrdo('CodingData');
		$codeList = $codingData->getCodeList($claim->get('encounter_id'));
		$company =& Celini::newOrdo('Company');

		$payer_ds = $company->companyListForType('Insurance');

		// next 2 lines still need to be updated
		ORDataObject::factory_include('Payment');
		$payments =& Payment::fromForeignId($claim_id);
		
		$payment =& Celini::newOrdo("Payment");
		$payment->set("foreign_id",$claim_id);		
		// get the newest payment
		if (count($payments) >0) {
			$payment = $payments[count($payments)-1];
		}

		$paymentDs =& $payment->paymentList($claim_id,true);
		$paymentGrid =& new cGrid($paymentDs);

		$payers = array(' '=>' ');

		$insuranceProgram =& Celini::newOrdo('InsuranceProgram');
		foreach($insuranceProgram->programList() as $key => $val) {
			$payers[$key] = $val;
		}

		foreach($payment_plans as $plan) {
			$payers['plan'.$plan->get('id')] = 'Payment Plan '.$plan->get('id').' ('.count($plan->get_unpaid_payments()).
				' Remaining Payments, Balance: $'.sprintf('%.2f',$plan->get('balance')).')';
		}
		
		if(count($payment_plans) == 0) {
			$this->view->assign('nopatientpaymentplans',true);
		}

		$billList = array();

		$this->view->assign_by_ref('ppplan',$paymentplan);

		$i = 0;
		foreach($codeList as $code) {
			$billList[$i]['code'] = $code['code'];
			$billList[$i]['code_id'] = $code['code_id'];
			$billList[$i]['coding_data_id'] = $code['coding_data_id'];
			$billList[$i]['description'] = $code['description'];
			$billList[$i]['amount'] = $code['fee'];
			$billList[$i]['paid'] = 0;
			$billList[$i]['writeoff'] = 0;
			$billList[$i]['current_paid'] = $payment->totalPaidForCodingDataId($code['coding_data_id']);
			$billList[$i]['current_writeoff'] = $payment->totalWriteoffForCodingDataId($code['coding_data_id']);
			$billList[$i]['payment_date'] = $payment->get('payment_date');
			$billList[$i]['carry'] = $billList[$i]['amount'] - $billList[$i]['current_paid'] - $billList[$i]['current_writeoff'];

			$i++;
		}

		$em =& Celini::enumManagerInstance();
		$enum =& $em->enumList('eob_adjustment_type');

		$adjustments = array();
		for($enum->rewind();$enum->valid();$enum->next()) {
			$adjustments[] = $enum->current();
		}

		$head =& Celini::HTMLHeadInstance();
		$head->addJs('scriptaculous');
		
		$this->assign('adjustments',$adjustments);
		$this->assign_by_ref('claim',$claim);
		$this->assign_by_ref('encounter',$encounter);
		$this->assign_by_ref('patient',$patient);
		$this->assign_by_ref('paymentGrid',$paymentGrid);
		$this->assign_by_ref('payment',Celini::newOrdo('payment'));
		$this->assign('billList',$billList);
		$this->assign('payers',$payers);

		$cp = $encounter->get('current_payer');
		if (isset($payers[$cp])) {
			$this->assign('current_payer',$payers[$cp]);
		}
		$this->assign('payment_date',$payment->get('payment_date'));
		
		$this->assign('FORM_ACTION',Celini::link('payment',true,'main',$claim_id));

		return $this->view->render("payment.html");
	}

	function processPayment_edit($claim_id) {
		$payment =& Celini::newOrdo('Payment');
		$payment->set('foreign_id',$claim_id);

		$payment->set('user_id',$this->_me->get_id());
		$payarray = $this->POST->getRaw('payment');

		$payment->populateArray($payarray);

		if(strpos($payarray['payer'],'plan') === false) {
			$payment->set('payer_id',$_POST['payment']['payer']);
		}
		$payment->set('payment_date', $_POST['payment']['payment_date']);
			
		$payment->persist();

		$payment_id = $payment->get('id');

		$claim =& Celini::newOrdo('ClearhealthClaim',$claim_id);
		// store a note if its not empty
		if(!empty($_POST['note']['note'])) {
			$note =& Celini::newOrdo('AccountNote');
			$note->populateArray($_POST['note']);

			$encounter =& Celini::newOrdo('Encounter',$claim->get('encounter_id'));
			$note->set('patient_id',$encounter->get('patient_id'));
			$note->set('user_id',$this->_me->get_id());
			$note->set('date_posted',date('Y-m-d H:i:s'));
			$note->persist();
		}

		$total_paid = 0;
		$total_writeoff = 0;

		$lineLookup = array(0=>0);

		if (isset($_POST['bill']) && count($_POST['bill']) > 0) {
			foreach($_POST['bill'] as $line) {
				unset($pcl);
				$pcl =& Celini::newOrdo('PaymentClaimline',array(0,$payment_id));
				$pcl->populate_array($line);
				$pcl->persist();
				$lineLookup[$line['coding_data_id']] = $pcl->get('id');
				$total_paid += $pcl->get('paid');
				$total_writeoff += $pcl->get('writeoff');
				
				if (isset($line['payment_date'])) {
					$payment->set('payment_date', $line['payment_date']);
				}
			}

			$payment->set('amount',$total_paid);
			$payment->set('writeoff',$total_writeoff);
			$payment->persist();
	
			// update claim total
			$claim->set('total_paid',$claim->get('total_paid')+$total_paid);
			$claim->persist();

		}

		if(strpos($payarray['payer'],'plan') !== false) {
			$plan_id = str_replace('plan','',$payarray['payer']);
			$ppplan =& Celini::newORDO('PatientPaymentPlan',$plan_id);
			$ppplan->addPayment($total_paid);
			$insuranceProgram =& Celini::newOrdo('InsuranceProgram');
			foreach($insuranceProgram->programList() as $key => $val) {
				if($val == 'System->Self Pay') {
					$payment->set('payer_id',$key);
				}
			}
			$payment->set('title','Payment Plan '.$ppplan->get('id').' Payment');
			$payment->persist();
		}

		if ($this->POST->exists('adjustment')) {
			foreach($this->POST->getRaw('adjustment') as $adjustment) {
				$adj =& Celini::newOrdo('EobAdjustment');
				$adj->set('payment_id',$payment_id);
				$adj->set('payment_claimline_id',$lineLookup[$adjustment['code']]);
				$adj->set('adjustment_type',$adjustment['type']);
				$adj->set('value',$adjustment['value']);
				$adj->persist();
			}
		}
		$this->payment_id = $payment_id;
	}

	// meant to be called with an AJAX post
	function actionRebillSelfPay_edit() {
		$this->view->assign('ajax',true);
		return $this->actionPayment_edit($this->claimId);
	}

	function processRebillSelfPay_edit() {
		$claimId = $this->POST->getTyped('claim_id','int');
		$claim =& Celini::newOrdo('ClearhealthClaim',$claimId);
		$encounter =& Celini::newOrdo('Encounter',$claim->get('encounter_id'));

		$ir =& Celini::newOrdo('InsuredRelationship');
		$list = $ir->getProgramList($encounter->get('patient_id'));
		$id = array_search('System->Self Pay',$list);
		if ($id == false) {
			$ir->set('person_id',$encounter->get('patient_id'));
			$ir->set('program_order',count($list));
			$ir->set('subscriber_id',$encounter->get('patient_id'));
			$ir->set('subscriber_to_patient_relationship',1);

			$sql = "select insurance_program_id from insurance_program ip inner join company c using(company_id) 
					where c.name = 'System' and ip.name = 'Self Pay'";
			$db = new clniDb();
			$id = $db->getOne($sql);
			$ir->set('insurance_program_id',$id);
			$ir->persist();
		}
		$encounter->set('current_payer',$id);
		$encounter->persist();

		$GLOBALS['loader']->requireOnce('includes/freebGateway/ClearhealthToFreebGateway.class.php');
		$gateway =& new ClearhealthToFreebGateway($this,$encounter);
		$gateway->send('rebill');

		$this->messages->addMessage('Claim Rebilled to Self Pay');
		$this->claimId = $claimId;
	}

	// meant to be called with an AJAX post
	function actionRebillNextPayer_edit() {
		$this->view->assign('ajax',true);
		return $this->actionPayment_edit($this->claimId);
	}

	function processRebillNextPayer_edit() {
		$claimId = $this->POST->getTyped('claim_id','int');
		$claim =& Celini::newOrdo('ClearhealthClaim',$claimId);
		$encounter =& Celini::newOrdo('Encounter',$claim->get('encounter_id'));
		$id = $encounter->get('next_payer_id');
		$encounter->set('current_payer',$id);
		$encounter->persist();

		$GLOBALS['loader']->requireOnce('includes/freebGateway/ClearhealthToFreebGateway.class.php');
		$gateway =& new ClearhealthToFreebGateway($this,$encounter);
		$gateway->send('rebill');
		$payer =& Celini::newORDO('InsuranceProgram',$id);
		$this->messages->addMessage('Claim Rebilled to '.$payer->value('fullname'));
		$this->claimId = $claimId;
	}
}
?>