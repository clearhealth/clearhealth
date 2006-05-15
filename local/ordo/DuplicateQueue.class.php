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
}
?>
