<?php
/**
 * Object Relational Persistence Mapping Class for table: superbill_data
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
 * Object Relational Persistence Mapping Class for table: superbill_data
 *
 * @package	com.uversainc.clearhealth
 */
class SuperbillData extends ORDataObject {

	/**#@+
	 * Fields of table: superbill_data mapped to class members
	 */
	var $id			= '';
	var $superbill_id	= '';
	var $code_id		= '';
	var $status		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function SuperbillData($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'superbill_data';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Superbill_data with this
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
		parent::populate('superbill_data_id');
	}

	/**#@+
	 * Getters and Setters for Table: superbill_data
	 */

	
	/**
	 * Getter for Primary Key: superbill_data_id
	 */
	function get_superbill_data_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: superbill_data_id
	 */
	function set_superbill_data_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
