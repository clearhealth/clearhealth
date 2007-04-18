<?php
/**
 * Object Relational Persistence Mapping Class for table: lab_note
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class LabNote extends ORDataObject {

	/**#@+
	 * Fields of table: lab_note mapped to class members
	 */
	var $lab_note_id		= '';
	var $lab_test_id		= '';
	var $note		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'lab_note';

	/**
	 * Primary Key
	 */
	var $_key = 'lab_note_id';

	/**
	 * Handle instantiation
	 */
	function LabNote() {
		parent::ORDataObject();
		$this->auditChanges = false;	
	}

	
}
?>
