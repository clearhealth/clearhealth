<?php
/**
 * Object Relational Persistence Mapping Class for table: x12transaction_data
 *
 * @package	com.clear-health.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class X12TransactionData extends ORDataObject {

	/**#@+
	 * Fields of table: x12transaction_data mapped to class members
	 */
	var $transaction_data_id	= '';
	var $history_id			= '';
	var $raw			= '';
	var $transaction_status		= '';
	var $payment_amount		= '';
	var $total_charge		= '';
	var $patient_responsibility	= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'x12transaction_data';

	/**
	 * Primary Key
	 */
	var $_key = 'transaction_data_id';

	/**
	 * Handle instantiation
	 */
	function X12transactionData() {
		parent::ORDataObject();
	}

	
}
?>
