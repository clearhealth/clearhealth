<?php
class C_PatientPaymentPlan extends Controller 
{
	var $_plan = null;
	var $_payments = array();
	
	function C_PatientPaymentPlan() {
		parent::Controller();
	}
	
	function ajax_edit($plan_id) {
		$plan =& Celini::newORDO('PatientPaymentPlan',$plan_id);
		$this->view->assign_by_ref('ppp',$plan);
		return $this->view->render('paymentplan.html');
	}
	
	function processPayment($plan_id,$amount,$payment_id=0) {
		$plan =& Celini::newORDO('PatientPaymentPlan');
		$payment =& Celini::newORDO('PatientPaymentPlanPayment',$payment_id);
		if($payment->get('id') > 0) {
			// Modifying a payment
			$plan->set('remaining_balance',$plan->get('remaining_balance') + ($payment->get('amount') - $amount));
			$payment->set('amount',$amount);
			$plan->persist();
			$payment->persist();
		} else {
			$payment->set('amount',$amount);
			$payment->set('payment_date',date('Y-m-d'));
			$payment->set('patient_payment_plan_id',$plan_id);
			$payment->persist();
			$plan->set('remaining_amount',$plan->get('remaining_amount') - $amount);
			$plan->persist();
		}
	}
	
	function process($data) {
		$plan =& Celini::newORDO('PatientPaymentPlan');
		$plan->populate_array($data);
		$plan->persist();
		$encounter =& Celini::newORDO('Encounter',$data['encounter_id']);
		$plan->setParent($encounter);
		$plan->create_payments();
	}
	
}
