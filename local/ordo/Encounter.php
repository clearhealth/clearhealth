<?php
/**
 * Object Relational Persistence Mapping Class for table: encounter
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
 * Object Relational Persistence Mapping Class for table: encounter
 *
 * @package	com.uversainc.freestand
 */
class Encounter extends ORDataObject {

	/**#@+
	 * Fields of table: encounter mapped to class members
	 */
	var $id		= '';
	var $encounter_reason		= '';
	var $building_id		= '';
	var $date_of_treatment		= '';
	var $treating_person_id		= '';
	var $timestamp		= '';
	var $last_change_user_id		= '';
	var $status		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Encounter($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'encounter';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Encounter with this
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
		parent::populate('encounter_id');
	}

	/**#@+
	 * Getters and Setters for Table: encounter
	 */

	
	/**
	 * Getter for Primary Key: encounter_id
	 */
	function get_encounter_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: encounter_id
	 */
	function set_encounter_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
