<?php
require_once CELLINI_ROOT ."/includes/Grid.class.php";

class C_Eob extends Controller {

	function payment_action_edit($claim_id) {

		$claim =& ORDataObject::Factory('ClearhealthClaim',$claim_id);
		$encounter =& ORDataObject::factory('Encounter',$claim->get('encounter_id'));
		$patient =& ORDataObject::factory('Patient',$encounter->get('patient_id'));


		$codingData =& ORDataOBject::Factory('CodingData');
		$codeList = $codingData->getCodeList($claim->get('encounter_id'));

		ORdataObject::Factory_include('FeeSchedule');
		$feeSchedule = FeeSchedule::defaultFeeSchedule();

		$company =& ORDataObject::Factory('Company');

		$payer_ds = $company->companyListForType('Insurance');

		ORDataObject::factory_include('Payment');
		$payments =& Payment::fromForeignId($claim_id);
		
		$payment = ORDataObject::factory("Payment");
		$payment->set("foreign_id",$claim_id);		
		// get the newest payment
		if (count($payments) >0) {
			$payment = $payments[count($payments)-1];
		}

		$paymentDs =& $payment->paymentList($claim_id,true);
		$paymentGrid =& new cGrid($paymentDs);

		$payers = array(' '=>' ',$patient->get('id')=>'Self Pay');
		foreach($payer_ds->toArray('company_id','name') as $key => $val) {
			$payers[$key] = $val;
		}

		$billList = array();

		$i = 0;
		foreach($codeList as $code) {
			$billList[$i]['code'] = $code['code'];
			$billList[$i]['code_id'] = $code['code_id'];
			$billList[$i]['description'] = $code['description'];
			$billList[$i]['amount'] = $feeSchedule->getFee($code['code']);
			$billList[$i]['paid'] = 0;
			$billList[$i]['writeoff'] = 0;
			$billList[$i]['current_paid'] = $payment->totalPaidForCodeId($code['code_id']);
			$billList[$i]['current_writeoff'] = $payment->totalWriteoffForCodeId($code['code_id']);


			$billList[$i]['carry'] = $billList[$i]['amount'] - $billList[$i]['current_paid'] - $billList[$i]['current_writeoff'];

			$i++;
		}
		
		$this->assign_by_ref('claim',$claim);
		$this->assign_by_ref('encounter',$encounter);
		$this->assign_by_ref('patient',$patient);
		$this->assign_by_ref('paymentGrid',$paymentGrid);
		$this->assign('billList',$billList);
		$this->assign('payers',$payers);
		
		$this->assign('FORM_ACTION',Cellini::link('payment',true,true,$claim_id));

		return $this->fetch(Cellini::getTemplatePath("/eob/" . $this->template_mod . "_payment.html"));
	}

	function payment_action_process($claim_id) {

		$payment =& ORDataObject::factory('Payment');
		$payment->set('foreign_id',$claim_id);

		$payment->set('user_id',$this->_me->get_id());
		$payment->set('payer_id',$_POST['payment']['payer']);
		$payment->persist();

		$payment_id = $payment->get('id');

		$total_paid = 0;
		$total_writeoff = 0;

		if (isset($_POST['bill']) && count($_POST['bill']) > 0) {
			foreach($_POST['bill'] as $line) {
				unset($pcl);
				$pcl =& ORDataObject::factory('PaymentClaimline',0,$payment_id);
				$pcl->populate_array($line);
				$pcl->persist();
				$total_paid += $pcl->get('paid');
				$total_writeoff += $pcl->get('writeoff');
			}

			$payment->set('amount',$total_paid);
			$payment->set('writeoff',$total_writeoff);
			$payment->persist();
	
			// update claim total
			$claim =& ORDataObject::factory('ClearhealthClaim',$claim_id);
			$claim->set('total_paid',$claim->get('total_paid',$total_paid));
			$claim->persist();
		}
	}
}
?>
