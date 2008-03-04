<?php
/**
 * Object Relational Persistence Mapping Class for table: self_mgmt_goals
 *
 * @package	com.clear-health.celini
 * @author	ClearHealth Inc.
 */
class SelfMgmtGoals extends ORDataObject {

	/**#@+
	 * Fields of table: self_mgmt_goals mapped to class members
	 */
	var $self_mgmt_id		= '';
	var $person_id		= '';
	var $initiated		= '';
	var $completed		= '';
	var $type		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'self_mgmt_goals';

	/**
	 * Primary Key
	 */
	var $_key = 'self_mgmt_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'SelfMgmtGoals';

	/**
	 * Handle instantiation
	 */
	function SelfMgmtGoals() {
		parent::ORDataObject();
	}

	
	/**#@+
	 * Field: initiated, time formatting
	 */
	function get_initiated() {
		return $this->_getDate('initiated');
	}
	function set_initiated($date) {
		$this->_setDate('initiated',$date);
	}
	/**#@-*/

	/**#@+
	 * Field: completed, time formatting
	 */
	function get_completed() {
		return $this->_getDate('completed');
	}
	function set_completed($date) {
		$this->_setDate('completed',$date);
	}
	/**#@-*/

}
?>
