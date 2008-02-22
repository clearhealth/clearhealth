<?php
/**
 * Object Relational Persistence Mapping Class for table: generic_notes
 *
 * @package	com.clear-health.celini
 * @author	Uversa Inc.
 */
class GenericNote extends ORDataObject {

	/**#@+
	 * Fields of table: generic_notes mapped to class members
	 */
	var $generic_note_id		= '';
	var $parent_obj_id		= '';
	var $created		= '';
	var $person_id		= '';
	var $note		= '';
	var $type		= '';
	var $deprecated		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'generic_notes';

	/**
	 * Primary Key
	 */
	var $_key = 'generic_note_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'GenericNote';

	/**
	 * Handle instantiation
	 */
	function GenericNote() {
		parent::ORDataObject();
	}

	
	/**#@+
	 * Field: created, time formatting
	 */
	function get_created() {
		return $this->_getDate('created');
	}
	function set_created($date) {
		$this->_setDate('created',$date);
	}
	/**#@-*/

}
?>
