<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_revision
 *
 * @package	com.clear-health.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_revision
 *
 * @package	com.clear-health.clearhealth
 */
class FeeScheduleRevision extends ORDataObject {

	/**#@+
	 * Fields of table: fee_schedule_revision mapped to class members
	 */
	var $id			= '';
	var $user_id		= '';
	var $update_time	= '';
	var $name		= '';
	/**#@-*/
	var $_table = 'fee_schedule_revision';
	var $_internalName='FeeScheduleRevision';

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FeeScheduleRevision($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Fee_schedule_revision with this
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
		parent::populate('revision_id');
	}

	/**#@+
	 * Getters and Setters for Table: fee_schedule_revision
	 */

	
	/**
	 * Getter for Primary Key: revision_id
	 */
	function get_revision_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: revision_id
	 */
	function set_revision_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
