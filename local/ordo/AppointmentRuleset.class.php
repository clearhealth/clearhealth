<?php
/**
 * Object Relational Persistence Mapping Class for table: appointment_ruleset
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class AppointmentRuleset extends ORDataObject {

	/**#@+
	 * Fields of table: appointment_ruleset mapped to class members
	 */
	var $appointment_ruleset_id	= '';
	var $name			= '';
	var $error_message		= '';
	var $enabled			= 1;
	var $provider_id		= '';
	var $procedure_id		= '';
	var $room_id			= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'appointment_ruleset';

	/**
	 * Primary Key
	 */
	var $_key = 'appointment_ruleset_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'AppointmentRuleset';

	/**
	 * Handle instantiation
	 */
	function AppointmentRuleset() {
		parent::ORDataObject();
	}

	function rulesList($type) {
		$sql = "select appointment_rule_id, label from appointment_rule where appointment_ruleset_id = ".$this->get('id').' and type = '.
				$this->dbHelper->quote($type);

		return $this->dbHelper->getAssoc($sql);
	}

	
}
?>
