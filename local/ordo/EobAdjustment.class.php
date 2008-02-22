<?php
/**
 * Object Relational Persistence Mapping Class for table: eob_adjustment
 *
 * @package	com.clear-health.celini
 * @author	Uversa Inc.
 */
class EobAdjustment extends ORDataObject {

	/**#@+
	 * Fields of table: eob_adjustment mapped to class members
	 */
	var $eob_adjustment_id		= '';
	var $payment_id		= '';
	var $payment_claimline_id		= '';
	var $adjustment_type		= '';
	var $value		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'eob_adjustment';

	/**
	 * Primary Key
	 */
	var $_key = 'eob_adjustment_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'EobAdjustment';

	/**
	 * Handle instantiation
	 */
	function EobAdjustment() {
		parent::ORDataObject();
	}

	
}
?>
