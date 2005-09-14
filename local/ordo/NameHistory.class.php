<?php
/**
 * Object Relational Persistence Mapping Class for table: name_history
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
require_once CELLINI_ROOT.'/includes/Datasource_sql.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: name_history
 *
 * @package	com.uversainc.freestand
 */
class NameHistory extends ORDataObject {

	/**#@+
	 * Fields of table: name_history mapped to class members
	 */
	var $id			= '';
	var $person_id		= '';
	var $first_name		= '';
	var $last_name		= '';
	var $middle_name	= '';
	var $update_date	= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function NameHistory($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'name_history';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Name_history with this
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
		parent::populate('name_history_id');
	}

	/**
	 * Get a ds with all the name history for a given person_id
	 */
	function &nameHistoryList($person_id) {
		settype($person_id,'int');
		
		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "first_name, last_name, middle_name, update_date",
				'from' 	=> "$this->_table nh",
				'where'	=> "person_id = $person_id",
				'orderby' => 'update_date DESC'

			),
			array('first_name' => 'First Name', 'last_name' => 'Last Name', 'middle_name' => 'Middle Initial', 'update_date' => 'Date Changed'));
		return $ds;
	}

	/**#@+
	 * Getters and Setters for Table: name_history
	 */

	
	/**
	 * Getter for Primary Key: name_history_id
	 */
	function get_name_history_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: name_history_id
	 */
	function set_name_history_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
