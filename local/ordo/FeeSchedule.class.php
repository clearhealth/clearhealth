<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule
 *
 * @package	com.uversainc.clearheath
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: fee_schedule
 *
 * @package	com.uversainc.clearhealth
 */
class FeeSchedule extends ORDataObject {

	/**#@+
	 * Fields of table: fee_schedule mapped to class members
	 */
	var $id			= '';
	var $name		= '';
	var $label		= '';
	var $description	= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FeeSchedule($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'fee_schedule';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Fee_schedule with this
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
		parent::populate('fee_schedule_id');
	}

	/**#@+
	 * Getters and Setters for Table: fee_schedule
	 */

	
	/**
	 * Getter for Primary Key: fee_schedule_id
	 */
	function get_fee_schedule_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: fee_schedule_id
	 */
	function set_fee_schedule_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
