<?php
/**
 * Object Relational Persistence Mapping Class for table: coding_data
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: coding_data
 *
 * @package	com.uversainc.clearhealth
 */
class CodingData extends ORDataObject {

	/**#@+
	 * Fields of table: coding_data mapped to class members
	 */
	var $id			= '';
	var $encounter_id	= '';
	var $parent_id		= '';
	var $code_id		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function CodingData($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'coding_data';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Coding_data with this
	 */
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('coding_data_id');
	}

	/**#@+
	 * Getters and Setters for Table: coding_data
	 */

	
	/**
	 * Getter for Primary Key: coding_data_id
	 */
	function get_coding_data_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: coding_data_id
	 */
	function set_coding_data_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
