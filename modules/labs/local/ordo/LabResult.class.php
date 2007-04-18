<?php
/**
 * Object Relational Persistence Mapping Class for table: lab_result
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class LabResult extends ORDataObject {

	/**#@+
	 * Fields of table: lab_result mapped to class members
	 */
	var $lab_result_id		= '';
	var $lab_test_id		= '';
	var $identifier		= '';
	var $value		= '';
	var $units		= '';
	var $reference_range		= '';
	var $abnormal_flag		= '';
	var $result_status		= '';
	var $observation_time		= '';
	var $producer_id		= '';
	var $description		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'lab_result';

	/**
	 * Primary Key
	 */
	var $_key = 'lab_result_id';

	/**
	 * Handle instantiation
	 */
	function LabResult() {
		parent::ORDataObject();
		$this->auditChanges = false;	
	}

	
}
?>
