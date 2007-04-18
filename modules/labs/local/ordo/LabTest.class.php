<?php
/**
 * Object Relational Persistence Mapping Class for table: lab_test
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class LabTest extends ORDataObject {

	/**#@+
	 * Fields of table: lab_test mapped to class members
	 */
	var $lab_test_id		= '';
	var $lab_order_id		= '';
	var $order_num		= '';
	var $filer_order_num		= '';
	var $observation_time		= '';
	var $specimen_received_time		= '';
	var $report_time		= '';
	var $ordering_provider		= '';
	var $service		= '';
	var $component_code		= '';
	var $status		= '';
	var $clia_disclosure		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'lab_test';

	/**
	 * Primary Key
	 */
	var $_key = 'lab_test_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'LabTest';

	/**
	 * Handle instantiation
	 */
	function LabTest() {
		parent::ORDataObject();
	}

	
	/**#@+
	 * Field: observation_time, time formatting
	 */
	function get_observation_time() {
		return $this->_getDate('observation_time');
	}
	function set_observation_time($date) {
		$this->_setDate('observation_time',$date);
	}
	/**#@-*/

	/**#@+
	 * Field: specimen_received_time, time formatting
	 */
	function get_specimen_received_time() {
		return $this->_getDate('specimen_received_time');
	}
	function set_specimen_received_time($date) {
		$this->_setDate('specimen_received_time',$date);
	}
	/**#@-*/

	/**#@+
	 * Field: report_time, time formatting
	 */
	function get_report_time() {
		return $this->_getDate('report_time');
	}
	function set_report_time($date) {
		$this->_setDate('report_time',$date);
	}
	/**#@-*/

}
?>
