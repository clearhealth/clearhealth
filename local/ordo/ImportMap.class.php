<?php
/**
 * Object Relational Persistence Mapping Class for table: import_map
 *
 * @package	com.uversainc.clearhealth
 * @author	Fred Trotter <ftrotter@uversainc.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: import_map
 *
 * @package	com.uversainc.clearhealth
 */
class ImportMap extends ORDataObject {

	/**#@+
	 * Fields of table: import_map mapped to class members
	 */
	var $id				= '';
	var $new_id			= '';
	var $old_table_name		= '';
	var $new_object_name		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Import_map($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'import_map';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Import_map with this
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
		parent::populate('old_id');
	}

	/**#@+
	 * Getters and Setters for Table: import_map
	 */

	
	/**
	 * Getter for Primary Key: old_id
	 */
	function get_old_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: old_id
	 */
	function set_old_id($id)  {
		$this->id = $id;
	}

	/*
		This object should...
  
                isInClearHealth(old_id,old_tablename) return true if an item is already imported
		getClearHealthId(old_id,old_tablename) return the ClearHealth Id of old item.

		getClearHealthObjectName(old_id,old_tablename) return the ClearHealth Object for an old item. 



	*/

	/**#@-*/
}
?>
