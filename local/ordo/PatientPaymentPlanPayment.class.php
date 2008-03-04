<?php
/**
 * Object Relational Persistence Mapping Class for table: patient_payment_plan_payment
 *
 * @package	com.clear-health.celini
 * @author	ClearHealth Inc.
 */
class PatientPaymentPlanPayment extends ORDataObject {

	/**#@+
	 * Fields of table: patient_payment_plan_payment mapped to class members
	 */
	var $patient_payment_plan_payment_id		= '';
	var $patient_payment_plan_id		= '';
	var $payment_date		= '';
	var $amount		= '';
	var $paid_amount = '0.00';
	var $paid = 'No';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'patient_payment_plan_payment';

	/**
	 * Primary Key
	 */
	var $_key = 'patient_payment_plan_payment_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'PatientPaymentPlanPayment';

	/**
	 * Handle instantiation
	 */
	function PatientPaymentPlanPayment() {
		parent::ORDataObject();
	}

	
	/**#@+
	 * Field: payment_date, time formatting
	 */
	function get_payment_date() {
		return $this->_getDate('payment_date');
	}
	function set_payment_date($date) {
		$this->_setDate('payment_date',$date);
	}
	/**#@-*/
	
	function get_pending_amount() {
		return sprintf("%.2f",$this->get('amount') - $this->get('paid_amount'));
	}

}
?>