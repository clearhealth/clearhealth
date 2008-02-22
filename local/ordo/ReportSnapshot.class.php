<?php
/**
 * Object Relational Persistence Mapping Class for table: report_snapshot
 *
 * @package	com.clear-health.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: report_snapshot
 *
 * @package	com.clear-health.Celini
 */
class ReportSnapshot extends ORDataObject {

	/**#@+
	 * Fields of table: report_snapshot mapped to class members
	 */
	var $id			= '';
	var $report_id		= '';
	var $template_id	= '';	
	var $snapshot_date	= false;
	var $data		= '';
	/**#@-*/
	var $_table = 'report_snapshot';
	var $_internalName='ReportSnapshot';


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function ReportSnapshot($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('report_snapshot_id');
	}

	/**#@+
	 * Getters and Setters for Table: report_snapshot
	 */

	
	/**
	 * Getter for Primary Key: report_snapshot_id
	 */
	function get_report_snapshot_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: report_snapshot_id
	 */
	function set_report_snapshot_id($id)  {
		$this->id = $id;
	}

	function set_snapshot_date($date) {
		$this->_setDate('snapshot_date', $date);
	}
	function get_snapshot_date() {
		if ($this->snapshot_date === false) {
			$this->_setDate('snapshot_date', date('Y-m-d H:i:s'));
		}
		return $this->_getTimestamp('snapshot_date');
	}

	/**#@-*/
}
?>
