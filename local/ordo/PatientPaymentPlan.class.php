<?php
/**
 * Object Relational Persistence Mapping Class for table: patient_payment_plan
 *
 * @package	com.clear-health.celini
 * @author	ClearHealth Inc.
 */
class PatientPaymentPlan extends ORDataObject {

	/**#@+
	 * Fields of table: patient_payment_plan mapped to class members
	 */
	var $patient_payment_plan_id		= '';
	var $patient_id		= '';
	var $start_date		= '';
	var $intervalnum	= '';
	var $intervaltype	= ''; // DAY,WEEK,MONTH,YEAR
	var $num_intervals	= '';
	var $balance		= '';
	/**#@-*/
	

	/**
	 * DB Table
	 */
	var $_table = 'patient_payment_plan';

	/**
	 * Primary Key
	 */
	var $_key = 'patient_payment_plan_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'PatientPaymentPlan';

	/**
	 * Handle instantiation
	 */
	function PatientPaymentPlan() {
		parent::ORDataObject();
	}

	
	/**#@+
	 * Field: start_date, time formatting
	 */
	function get_start_date() {
		return $this->_getDate('start_date');
	}
	function set_start_date($date) {
		$this->_setDate('start_date',$date);
	}
	
	/**
	 * Returns an array of timestamps for each payment date
	 * @return array
	 */
	function get_dates() {
		$start = $this->_getDate('start_date');
		$start = strtotime($start);
		$dates = array($start);
		for($x = 1;$x < $this->get('num_intervals');$x++) {
			$start = strtotime('+'.$this->get('intervalnum').' '.$this->get('intervaltype'),$start);
			$dates[] = $start;
		}
		return $dates;
	}
	
	function set_encounter_id($id) {
		if($this->get('id') < 1) {
			$this->persist();
		}
		$encounter =& Celini::newORDO('Encounter',$id);
		$this->setParent($encounter);
	}
	
	/**
	 * Initial creation of patient payments
	 *
	 */
	function create_payments() {
		$dates = $this->get_dates();
		$last = count($dates) - 1;
		$amount = sprintf("%.2f",$this->get('balance') / $this->get('num_intervals'));
		$remaining = $this->get('balance');
		foreach($dates as $key=>$date) {
			$payment =& Celini::newORDO('PatientPaymentPlanPayment');
			$payment->set('patient_payment_plan_id',$this->get('id'));
			$payment->set('payment_date',date('Y-m-d',$date));
			$payment->set('paid','No');
			if($key == $last) {
				$payment->set('amount',$remaining);
			} else {
				$payment->set('amount',$amount);
			}
			$payment->persist();
			$remaining -= $amount;
		}
	}
	
	/**
	 * Fixes (evens out) remaining payments when patient overpays or something
	 * modifies the amount_remaining
	 *
	 */
	function fix_payments() {
		$remaining = $this->get('balance');
		$payments = $this->get_unpaid_payments();
		$last = count($payments) - 1;
		$amount = sprintf("%.2f",$this->get('balance') / count($payments));
		foreach($payments as $key=>$payment) {
			if($key == $last) {
				$payment->set('amount',$remaining);
			} else {
				$payment->set('amount',$amount);
				$remaining -= $amount;
			}
			$payment->persist();
		}
	}
	
	/**
	 * Returns array of payment ORDOs
	 *
	 * @return array
	 */
	function get_unpaid_payments($date=false) {
		$db =& Celini::dbInstance();
		$sql = "SELECT patient_payment_plan_payment_id AS id FROM patient_payment_plan_payment WHERE patient_payment_plan_id = ".$this->get('id').
		" AND paid = 'No'";
		if($date !== false) {
			$sql .= " AND payment_date < ".$db->quote($date);
		}
		$sql .= "  ORDER BY payment_date ASC";
		$res = $db->execute($sql);
		$payments = array();
		while($res && !$res->EOF) {
			$payments[] =& Celini::newORDO('PatientPaymentPlanPayment',$res->fields['id']);
			$res->MoveNext();
		}
		return $payments;
	}

	/**
	 * Return next payment of plan or FALSE
	 *
	 * @return ORDataObject | FALSE
	 */
	function get_next_payment() {
		$dates = $this->get_unpaid_payments();
		if(count($dates > 0)) {
			return $dates[0];
		}
		return FALSE;
	}
	
	/**
	 * Returns the total amount of payments made to this plan
	 * 
	 * @return float
	 */
	function get_total_payments() {
		$db =& Celini::dbInstance();
		$sql = "SELECT SUM(paid_amount) AS total FROM patient_payment_plan_payment WHERE patient_payment_plan_id = ".$this->get('id');
		$res = $db->execute($sql);
		if($res && !$res->EOF) {
			return $res->fields['total'];
		}
		return 0;
	}
	
	function get_balance_before($date) {
		$date = date('Y-m-d',strtotime($date));
		$db =& Celini::dbInstance();
		$sql = "SELECT patient_payment_plan_payment_id AS id FROM patient_payment_plan_payment pppp
		INNER JOIN patient_payment_plan ppp ON patient_id = ".$this->get('patient_id')." AND ppp.patient_payment_plan_id = pppp.patient_payment_plan_id
		WHERE payment_date < ".$db->quote($date);
		$res = $db->execute($sql);
		$payments = array();
		while($res && !$res->EOF) {
			$payments[] =& Celini::newORDO('PatientPaymentPlanPayment',$res->fields['id']);
			$res->MoveNext();
		}
//		$payments = $this->get_unpaid_payments($date);
		$total = 0;
		$amount = 0;
		$paid = 0;
		foreach($payments as $payment) {
			$amount += $payment->get('amount');
			$paid += $payment->get('paid_amount');
		}
		return array('amount'=>$amount,'paid'=>$paid,'balance'=>$amount-$paid);
	}
	
	/**#@-*/
	
	/**
	 * Adds a payment to the plan and fixes remaining amounts if overpaid.
	 *
	 * @param float $amount
	 */
	function addPayment($amount) {
		$payment = $this->get_next_payment();
		$to_pay = $payment->get('amount') - $payment->get('paid_amount');
		$pay = min($amount,$to_pay);
		$this->set('balance',$this->get('balance')-$pay);
		$payment->set('paid_amount',$payment->get('paid_amount') + $pay);
		if($payment->get('paid_amount') == $payment->get('amount')) {
			$payment->set('paid','Yes');
		}
		$payment->persist();
		if($amount > $pay) {
			$diff = $amount - $pay;
			$this->set('balance',$this->get('balance')-$diff);
			$this->fix_payments();
		}
		$this->persist();
	}
	
	/**
	 * Returns array of payment plans which have not been paid off
	 *
	 * @param int $patient_id
	 * @return array
	 */
	function getByPatient($patient_id) {
		$db =& Celini::dbInstance();
		$sql = "SELECT patient_payment_plan_id AS id FROM patient_payment_plan WHERE balance > 0 AND patient_id = ".$db->quote($patient_id);
		$res = $db->execute($sql);
		$plans = array();
		while($res && !$res->EOF) {
			$plans[] =& Celini::newORDO('PatientPaymentPlan',$res->fields['id']);
			$res->MoveNext();
		}
		return $plans;
	}

}
?>