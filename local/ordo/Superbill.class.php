<?php
/**
 * Object Relational Persistence Mapping Class for table: superbill
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class Superbill extends ORDataObject {

	/**#@+
	 * Fields of table: superbill mapped to class members
	 */
	var $superbill_id	= '';
	var $name		= '';
	var $practice_id	= '';
	var $status		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'superbill';

	/**
	 * Primary Key
	 */
	var $_key = 'superbill_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'Superbill';

	/**
	 * Handle instantiation
	 */
	function Superbill() {
		parent::ORDataObject();
	}

	
}
?>
