<?php
/**
 * Object Relational Persistence Mapping Class for table: payment_claimline
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: payment_claimline
 *
 * @package	com.uversainc.clearhealth
 */
class PaymentClaimline extends ORDataObject {

	/**#@+
	 * Fields of table: payment_claimline mapped to class members
	 */
	var $id			= '';
	var $payment_id		= '';
	var $code_id		= '';
	var $paid		= '';
	var $writeoff		= '';
	var $carry		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function PaymentClaimline($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'payment_claimline';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Payment_claimline with this
	 */
	function setup($id = 0,$payment_id = 0) {
		if ($payment_id > 0) {
			$this->set('payment_id',$payment_id);
		}
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('payment_claimline_id');
	}

	/**#@+
	 * Getters and Setters for Table: payment_claimline
	 */

	
	/**
	 * Getter for Primary Key: payment_claimline_id
	 */
	function get_payment_claimline_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: payment_claimline_id
	 */
	function set_payment_claimline_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
