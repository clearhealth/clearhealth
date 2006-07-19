<?php
/**
 * Object Relational Persistence Mapping Class for table: code_category
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class CodeCategory extends ORDataObject {

	/**#@+
	 * Fields of table: code_category mapped to class members
	 */
	var $code_category_id		= '';
	var $category_name		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'code_category';

	/**
	 * Primary Key
	 */
	var $_key = 'code_category_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'CodeCategory';

	/**
	 * Handle instantiation
	 */
	function CodeCategory() {
		parent::ORDataObject();
	}

	
}
?>
