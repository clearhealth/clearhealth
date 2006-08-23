<?php
/**
 * Object Relational Persistence Mapping Class for table: misc_charge
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class MiscCharge extends ORDataObject {

	/**#@+
	 * Fields of table: misc_charge mapped to class members
	 */
	var $misc_charge_id		= '';
	var $encounter_id		= '';
	var $amount		= '';
	var $charge_date		= '';
	var $title		= '';
	var $note		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'misc_charge';

	/**
	 * Primary Key
	 */
	var $_key = 'misc_charge_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'MiscCharge';

	/**
	 * Handle instantiation
	 */
	function MiscCharge() {
		parent::ORDataObject();
	}

	
}
?>
