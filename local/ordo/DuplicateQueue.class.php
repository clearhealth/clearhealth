<?php
/**
 * Object Relational Persistence Mapping Class for table: duplicate_queue
 *
 * @package	com.uversainc.clearhealth
 * @author	Uversa Inc.
 */
class DuplicateQueue extends ORDataObject {

	/**#@+
	 * Fields of table: duplicate_queue mapped to class members
	 */
	var $duplicate_queue_id	= '';
	var $parent_id		= '';
	var $child_id		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'duplicate_queue';

	/**
	 * Primary Key
	 */
	var $_key = 'duplicate_queue_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'DuplicateQueue';

	/**
	 * Handle instantiation
	 */
	function DuplicateQueue() {
		parent::ORDataObject();
	}

	function setupByPatientId($patientId) {
		$p = EnforceType::int($patientId);
		$sql = "select * from ".$this->tableName()." where child_id = $p";
		$this->helper->populateFromQuery($this,$sql);
	}

	function value_childSummary() {
		return $this->patientSummary($this->get('child_id'));
	}

	function value_parentSummary() {
		return $this->patientSummary($this->get('parent_id'));
	}

	function patientSummary($id) {
		$patient =& Celini::newOrdo('Patient',$id);

		$ret = '<b>'.$patient->value('patient').'</b><br>';
		$ret .= 'SSN: '.$patient->value('identifier').'<br>';
		$ret .= 'Phone: '.$patient->value('phone').'<br>';
		return $ret;
	}
}
?>
