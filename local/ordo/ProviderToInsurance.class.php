<?php
/**
 * Object Relational Persistence Mapping Class for table: provider_to_insurance
 *
 * @package	com.uversainc.freestand
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: provider_to_insurance
 *
 * @package	com.uversainc.freestand
 */
class Provider_to_insurance extends ORDataObject {

	/**#@+
	 * Fields of table: provider_to_insurance mapped to class members
	 */
	var $id		= '';
	var $person_id		= '';
	var $insurance_program_id		= '';
	var $provider_number		= '';
	var $provider_number_type		= '';
	var $group_number		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Provider_to_insurance($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'provider_to_insurance';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Provider_to_insurance with this
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
		parent::populate('provider_to_insurance_id');
	}

	/**#@+
	 * Getters and Setters for Table: provider_to_insurance
	 */

	
	/**
	 * Getter for Primary Key: provider_to_insurance_id
	 */
	function get_provider_to_insurance_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: provider_to_insurance_id
	 */
	function set_provider_to_insurance_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
