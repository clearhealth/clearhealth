<?php

require_once CELLINI_ROOT."/controllers/Controller.class.php";
require_once CELLINI_ROOT."/includes/Grid.class.php";
require_once CELLINI_ROOT."/includes/Datasource_sql.class.php";

class C_Account extends Controller {

	function C_Account ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;
	}

	function default_action() {
	}
	
	function history_action_view($patient_id) {
		
		$a_grid =& new cGrid($this->_paymentList($patient_id));
		
		$chc =& ORDataObject::factory("ClearhealthClaim");
		$chc_grid =& new cGrid($chc->claimList($patient_id));
		
		$pcl =& ORDataObject::factory("PaymentClaimline");
		$pcl_grid =& new cGrid($pcl->PaymentClaimlineList($patient_id));
		
		$this->assign_by_ref("account_grid",$a_grid);
		$this->assign_by_ref("chc_grid",$chc_grid);
		$this->assign_by_ref("pcl_grid",$pcl_grid);
		
		return $this->fetch(Cellini::getTemplatePath("/account/" . $this->template_mod . "_history.html"));
	}
	
	/**
	 * Get datasource for payments from the db using the patient id
	 */
	function _paymentList($patient_id) {
		settype($patient_id,'int');
		
		$payment =& ORDataObject::factory("Payment");
		
		$ds =& new Datasource_sql();

		$labels = array('payment_type' => 'Type','payment_date' => 'Payment Date', 'amount' => 'Amount');
		$labels['writeoff'] = "Write Off";
		$labels['payer_id'] = "Payer";
		$ds->registerFilter('payer_id',array(&$payment,'lookupPayer'));

		$ds->setup($payment->_db,array(
				'cols' 	=> "payment_id, foreign_id, payment_type, amount, writeoff, payer_id, payment_date, pa.timestamp",
				'from' 	=> " payment pa left join encounter e using(encounter_id) left join person p on e.patient_id = p.person_id "
						." left join clearhealth_claim chc on chc.claim_id = pa.foreign_id left join encounter e2 on chc.encounter_id = e2.encounter_id ",
				'where' => " e.patient_id = $patient_id or e2.patient_id = $patient_id"
			),
			$labels
		);
		//echo $ds->preview();
		$ds->registerFilter('payment_type',array(&$payment,'lookupPaymentType'));
		return $ds;
	}

}

?>