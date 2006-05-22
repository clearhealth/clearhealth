<?php
/**
 * Object Relational Persistence Mapping Class for table: eligibility_log
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class EligibilityLog extends ORDataObject {

	/**#@+
	 * Fields of table: eligibility_log mapped to class members
	 */
	var $eligibility_log_id	= '';
	var $patient_id		= '';
	var $log_time		= '';
	var $message		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'eligibility_log';

	/**
	 * Primary Key
	 */
	var $_key = 'eligibility_log_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'EligibilityLog';

	/**
	 * Handle instantiation
	 */
	function EligibilityLog() {
		parent::ORDataObject();
	}

	
	function setupByPatientLatest($patientId) {
		$pid = EnforceType::int($patientId);
		$sql = "select * from ".$this->tableName()." where patient_id = $pid order by log_time DESC limit 1";
		$this->helper->populateFromQuery($this,$sql);
	}
}
?>
