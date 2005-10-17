<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_data
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_data
 *
 * @package	com.uversainc.clearhealth
 */
class FeeScheduleData extends ORDataObject {

	/**#@+
	 * Fields of table: fee_schedule_data mapped to class members
	 */
	var $id			= '';
	var $revision_id	= '';
	var $fee_schedule_id	= '';
	var $data		= '';
	var $formula		= '';
	var $mapped_code		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FeeScheduleData($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'fee_schedule_data';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Fee_schedule_data with this
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
		parent::populate('code_id');
	}

	/**#@+
	 * Getters and Setters for Table: fee_schedule_data
	 */

	
	/**
	 * Getter for Primary Key: field_id
	 */
	function get_code_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: field_id
	 */
	function set_code_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
