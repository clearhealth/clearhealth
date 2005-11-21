<?php
/**
 * Object Relational Persistence Mapping Class for table: enumeration_value_practice
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: enumeration_value_practice
 *
 * @package	com.uversainc.Celini
 */
class EnumerationValuePractice extends ORDataObject {

	/**#@+
	 * Fields of table: enumeration_value_practice mapped to class members
	 */
	var $id		= '';
	var $practice_id		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function EnumerationValuePractice($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'enumeration_value_practice';
		$this->_sequence_name = 'sequences';	
	}

	/**#@+
	 * Getters and Setters for Table: enumeration_value_practice
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
