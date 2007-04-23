<?php
/**
 * Object Relational Persistence Mapping Class for table: enumeration_value
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: enumeration_value
 *
 * @package	com.uversainc.celini
 */
class EnumerationTreeValue extends ORDataObject {

	/**#@+
	 * Fields of table: enumeration_value mapped to class members
	 */
	var $id			= '';
	var $enumeration_id	= '';
	var $key		= '';
	var $value		= '';
	var $sort		= '';
	var $extra1		= '';
	var $extra2		= '';
	var $status		= 1;
	var $depth		= 0;
	var $parent_id		= 0;
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function EnumerationTreeValue($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'enumeration_value';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Enumeration_value with this
	 */
	function setup($enumerationValueId = 0) {
		if ($enumerationValueId > 0) {
			$this->set('id',$enumerationValueId);
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('enumeration_value_id');
	}

	/**#@+
	 * Getters and Setters for Table: enumeration_value
	 */

	
	/**
	 * Getter for Primary Key: enumeration_value_id
	 */
	function get_enumeration_value_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: enumeration_value_id
	 */
	function set_enumeration_value_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
