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

	/**
	 * When we persist, we need to add an ACL resource so certain groups/users can override.
	 *
	 */
	function persist() {
		$sec =& $GLOBALS['security'];
		if($this->get('id') < 1) {
			parent::persist();
			$id = $sec->add_object('resources','Appointment Rule - '.$this->get('name'),$this->get('id'),15,0,'axo');
			$sec->add_group_object(16,'resources',$this->get('id'),'axo');
		} else {
			$id = $sec->get_object_id($this->get('id'),'appointment_rules','axo');
			$sec->edit_object($id,'resources','Appointment Rule - '.$this->get('name'),$this->get('id'),15,0,'axo');
			parent::persist();
		}
	}
	
	/**
	 * When dropping, remove ACLs first
	 *
	 */
	function drop() {
		$sec =& $GLOBALS['security'];
		$id = $sec->get_object_id('resources',strtolower($this->get('id')),'axo');
		$sec->del_object($id,'axo',true);
		parent::drop();
	}
	
}
?>