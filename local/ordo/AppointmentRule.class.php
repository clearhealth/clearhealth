<?php
/**
 * Object Relational Persistence Mapping Class for table: appointment_rule
 *
 * @package	com.clear-health.celini
 * @author	ClearHealth Inc.
 */
class AppointmentRule extends ORDataObject {

	/**#@+
	 * Fields of table: appointment_rule mapped to class members
	 */
	var $appointment_rule_id	= '';
	var $appointment_ruleset_id	= '';
	var $type			= '';
	var $label			= '';
	var $data			= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'appointment_rule';

	/**
	 * Primary Key
	 */
	var $_key = 'appointment_rule_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'AppointmentRule';

	/**
	 * Handle instantiation
	 */
	function AppointmentRule() {
		parent::ORDataObject();
	}

	
}
?>
