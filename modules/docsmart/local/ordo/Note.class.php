<?php
/**
 * Object Relational Persistence Mapping Class for table: notes
 *
 * @package com.uversainc.docsmart
 *
 * @todo prefix with DocSmart
 */
class Note extends ORDataObject {

	/**#@+
	 * Fields of table: notes mapped to class members
	 */
	var $note_id		= '';
	var $revision_id		= '';
	var $user_id		= '';
	var $note		= '';
	var $create_date		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'notes';

	/**
	 * Primary Key
	 */
	var $_key = 'note_id';

	/**
	 * Handle instantiation
	 */
	function Note() {
		parent::ORDataObject();
	}

	/**
	 * Remove note record from the database
	 *
	 * @return boolean
	 */
	function drop() {
		if(!($id = $this->get_id())) {
			return false;
		}
		$sql = "DELETE FROM ".$this->_table." WHERE ".$this->_key." = '".$id."'";
		return $this->_db->Execute($sql);
	}
	
	
}
?>
