<?php
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce('includes/transaction/TransactionManager.class.php');

class C_Eob extends Controller {

	function actionPayment_edit($claim_id) {

		$claim =& Celini::newOrdo('ClearhealthClaim',$claim_id);
		$encounter =& Celini::newOrdo('Encounter',$claim->get('encounter_id'));
		$patient =& Celini::newOrdo('Patient',$encounter->get('patient_id'));


		$codingData =& Celini::newOrdo('CodingData');
		$codeList = $codingData->getCodeList($claim->get('encounter_id'));

		$company =& Celini::newOrdo('Company');

		$payer_ds = $company->companyListForType('Insurance');

		// next 2 lines still need to be updated
		ORDataObject::factory_include('Payment');
		$payments =& Payment::fromForeignId($claim_id);
		
		$payment = Celini::newOrdo("Payment");
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

		$billList = array();

		$tmanager = new TransactionManager();
		$trans = $tmanager->createTransaction('EstimateDiscountedClaim');
		$trans->setAllFromEncounterId($claim->get('encounter_id'));
		$trans->resultsInMap = true;
		$fees = $tmanager->processTransaction($trans);

		$i = 0;
		foreach($codeList as $code) {
			$billList[$i]['code'] = $code['code'];
			$billList[$i]['code_id'] = $code['code_id'];
			$billList[$i]['description'] = $code['description'];
			$billList[$i]['amount'] = $fees[$code['code']];
			$billList[$i]['paid'] = 0;
			$billList[$i]['writeoff'] = 0;
			$billList[$i]['current_paid'] = $payment->totalPaidForCodeId($code['code_id']);
			$billList[$i]['current_writeoff'] = $payment->totalWriteoffForCodeId($code['code_id']);
			$billList[$i]['payment_date'] = $payment->get('payment_date');
			$billList[$i]['carry'] = $billList[$i]['amount'] - $billList[$i]['current_paid'] - $billList[$i]['current_writeoff'];

			$i++;
		}
		
		$this->assign_by_ref('claim',$claim);
		$this->assign_by_ref('encounter',$encounter);
		$this->assign_by_ref('patient',$patient);
		$this->assign_by_ref('paymentGrid',$paymentGrid);
		$this->assign('billList',$billList);
		$this->assign('payers',$payers);
		$this->assign('payment_date',$payment->get('payment_date'));
		
		$this->assign('FORM_ACTION',Celini::link('payment',true,true,$claim_id));

		return $this->view->render("payment.html");
	}

	function processPayment_edit($claim_id) {

		$payment =& Celini::newOrdo('Payment');
		$payment->set('foreign_id',$claim_id);

		$payment->set('user_id',$this->_me->get_id());
		$payment->set('payer_id',$_POST['payment']['payer']);
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

		if (isset($_POST['bill']) && count($_POST['bill']) > 0) {
			foreach($_POST['bill'] as $line) {
				unset($pcl);
				$pcl =& Celini::newOrdo('PaymentClaimline',array(0,$payment_id));
				$pcl->populate_array($line);
				$pcl->persist();
				$total_paid += $pcl->get('paid');
				$total_writeoff += $pcl->get('writeoff');
				
				if (isset($line['payment_date'])) {
					$payment->set('payment_date', $line['payment_date']);
				}
			}

			$payment->set('amount',$total_paid);
			$payment->set('writeoff',$total_writeoff);
			$payment->set('payment_type',"remittance");
			$payment->persist();
	
			// update claim total
			$claim->set('total_paid',$claim->get('total_paid')+$total_paid);
			$claim->persist();

		}
	}
}
?>
