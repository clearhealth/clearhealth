<?php
/**
 * Object Relational Persistence Mapping Class for table: claim
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
 * Object Relational Persistence Mapping Class for table: claim
 *
 * @package	com.uversainc.clearhealth
 */
class ClearhealthClaim extends ORDataObject {

	/**#@+
	 * Fields of table: claim mapped to class members
	 */
	var $id			= '';
	var $encounter_id	= '';
	var $identifier		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function ClearhealthClaim($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'clearhealth_claim';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of ClearhealthClaim with this
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
		parent::populate('claim_id');
	}

	/**#@+
	 * Getters and Setters for Table: claim
	 */

	
	/**
	 * Getter for Primary Key: claim_id
	 */
	function get_claim_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: claim_id
	 */
	function set_claim_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
