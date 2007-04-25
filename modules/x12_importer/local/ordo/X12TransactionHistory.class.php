<?php
/**
 * Object Relational Persistence Mapping Class for table: x12transaction_history
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class X12TransactionHistory extends ORDataObject {

	/**#@+
	 * Fields of table: x12transaction_history mapped to class members
	 */
	var $history_id		= '';
	var $source_id		= '';
	var $transaction_id		= '';
	var $claim_id		= '';
	var $applied_date		= '';
	var $applied_by		= '';
	var $payment_id		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'x12transaction_history';

	/**
	 * Primary Key
	 */
	var $_key = 'history_id';

	/**
	 * Handle instantiation
	 */
	function X12transactionHistory() {
		parent::ORDataObject();
	}

	function setupByClaim($transId,$claimId) {
		$t = $this->dbHelper->quote($transId);
		$c = $this->dbHelper->quote($claimId);

		$sql = "select * from ".$this->tableName()." where transaction_id = $t and claim_id = $c";
		$res = $this->dbHelper->execute($sql);
		$this->helper->populateFromResults($this,$res);
	}

	function set_applied_date($date) {
		$this->_setDate('applied_date',$date);
	}

	function get_applied_date() {
		return $this->_getTimestamp('applied_date');
	}

	function numAppliedClaims($transactionId) {
		$t = $this->dbHelper->quote($transactionId);
		$sql = "select count( distinct claim_id) from ".$this->tableName()." where transaction_id = $t";

		return $this->dbHelper->getOne($sql);
	}
}
?>
