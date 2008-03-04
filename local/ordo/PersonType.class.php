<?php
/**
 * Object Relational Persistence Mapping Class for table: person_type
 *
 * @package	com.clear-health.celini
 * @author	ClearHealth Inc.
 */
class PersonType extends ORDataObject {

	/**#@+
	 * Fields of table: person_type mapped to class members
	 */
	var $person_id		= '';
	var $person_type		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'person_type';

	/**
	 * Primary Key
	 */
	var $_key = 'Array';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'PersonType';

	/**
	 * Handle instantiation
	 */
	function PersonType() {
		parent::ORDataObject();
	}

	
}
?>
