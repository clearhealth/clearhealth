<?php
/**
 * Object Relational Persistence Mapping Class for table: route_slip
 *
 * @package	com.clear-health.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: route_slip
 *
 * @package	com.clear-health.Celini
 */
class RouteSlip extends ORDataObject {

	/**#@+
	 * Fields of table: route_slip mapped to class members
	 */
	var $id			= '';
	var $encounter_id	= '';
	var $report_date	= false;
	/**#@-*/
	var $_table = 'route_slip';
	var $_internalName='RouteSlip';


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function RouteSlip($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('route_slip_id');
	}

	/**#@+
	 * Getters and Setters for Table: route_slip
	 */

	
	/**
	 * Getter for Primary Key: route_slip_id
	 */
	function get_route_slip_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: route_slip_id
	 */
	function set_route_slip_id($id)  {
		$this->id = $id;
	}

	function get_report_date() {
		if ($this->report_date === false) {
			$this->_setDate('report_date', date('Y-m-d H:i:s'));
		}
		return $this->_getTimestamp('report_date');
	}
	function set_report_date($date) {
		$this->_setDate('report_date', $date);
	}
	/**#@-*/
}
?>
