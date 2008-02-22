<?php
/**
 * Object Relational Persistence Mapping Class for table: appointment_breakdown
 *
 * @package	com.clear-health.celini
 * @author	Uversa Inc.
 */
class AppointmentBreakdown extends ORDataObject {

	/**#@+
	 * Fields of table: appointment_breakdown mapped to class members
	 */
	var $appointment_breakdown_id		= '';
	var $appointment_id		= '';
	var $occurence_breakdown_id		= '';
	var $person_id		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'appointment_breakdown';

	/**
	 * Primary Key
	 */
	var $_key = 'appointment_breakdown_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'AppointmentBreakdown';

	/**
	 * Handle instantiation
	 */
	function AppointmentBreakdown() {
		parent::ORDataObject();
	}
	
	function persist() {
		if($this->get('id') > 0) {
			parent::persist();
		} else {
			$db =& Celini::dbInstance();
			$sql = "SELECT appointment_breakdown_id from {$this->_table} WHERE appointment_id=".$db->quote($this->get('appointment_id'))." AND occurence_breakdown_id=".$db->quote($this->get('occurence_breakdown_id'));
			$res = $db->execute($sql);
			if($res && !$res->EOF) {
				$this->set('id',$res->fields['appointment_breakdown_id']);
			}
			parent::persist();
		}
	}

	
}
