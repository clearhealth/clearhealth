<?php
/**
 * Object Relational Persistence Mapping Class for table: encounter_dates
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
 * Object Relational Persistence Mapping Class for table: encounter_dates
 *
 * @package	com.uversainc.freestand
 */
class Encounter_dates extends ORDataObject {

	/**#@+
	 * Fields of table: encounter_dates mapped to class members
	 */
	var $id		= '';
	var $encounter_id		= '';
	var $title		= '';
	var $date		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Encounter_dates($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'encounter_dates';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Encounter_dates with this
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
		parent::populate('encounter_dates_id');
	}

	/**#@+
	 * Getters and Setters for Table: encounter_dates
	 */

	
	/**
	 * Getter for Primary Key: encounter_dates_id
	 */
	function get_encounter_dates_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: encounter_dates_id
	 */
	function set_encounter_dates_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
