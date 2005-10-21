<?php
/**
 * Object Relational Persistence Mapping Class for table: practice_setting
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

$loader->requireOnce('/ordo/ORDataObject.class.php');

/**
 * Object Relational Persistence Mapping Class for table: practice_setting
 *
 * @package	com.uversainc.Celini
 */
class PracticeSetting extends ORDataObject {

	/**#@+
	 * Fields of table: practice_setting mapped to class members
	 */
	var $id			= '';
	var $practice_id	= '';
	var $name		= '';
	var $value		= '';
	var $serialized		= '';
	/**#@-*/

	function setupName($name,$practiceId) {
		$name = $this->dbHelper->escape($name);
		$practiceId = $this->dbHelper->escape($practiceId);
		$id = $this->dbHelper->getOne('select practice_setting_id from '.$this->tableName()." where name = $name and practice_id = $practiceId");
		$this->setup($id);

		if (!$this->isPopulated()) {
			$this->set('practice_id',$practiceId);
			$this->set('name',$name);
		}
	}


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function PracticeSetting($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'practice_setting';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('practice_setting_id');
	}

	/**#@+
	 * Getters and Setters for Table: practice_setting
	 */

	
	/**
	 * Getter for Primary Key: practice_setting_id
	 */
	function get_practice_setting_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: practice_setting_id
	 */
	function set_practice_setting_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
