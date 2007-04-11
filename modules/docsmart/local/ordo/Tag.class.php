<?php
/**
 * Object Relational Persistence Mapping Class for table: tags
 *
 * @package com.uversainc.docsmart
 *
 * @todo prefix with DocSmart
 */
class Tag extends ORDataObject {

	/**#@+
	 * Fields of table: tags mapped to class members
	 */
	var $tag_id		= '';
	var $tag		= '';
	var $storable_id		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'tags';

	/**
	 * Primary Key
	 */
	var $_key = 'tag_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'Tag';

	/**
	 * Handle instantiation
	 */
	function Tag() {
		parent::ORDataObject();
	}
}
?>
