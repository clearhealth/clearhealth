<?php
/**
 * Object Relational Persistence Mapping Class for table: appointment_template
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

$loader->requireOnce('ordo/ORDataObject.class.php');

/**
 * Object Relational Persistence Mapping Class for table: appointment_template
 *
 * @package	com.uversainc.Celini
 */
class AppointmentTemplate extends ORDataObject {

	/**#@+
	 * Fields of table: appointment_template mapped to class members
	 */
	var $id		= '';
	var $name	= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function AppointmentTemplate($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'appointment_template';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('appointment_template_id');
	}

	/**#@+
	 * Getters and Setters for Table: appointment_template
	 */

	
	/**
	 * Getter for Primary Key: appointment_template_id
	 */
	function get_appointment_template_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: appointment_template_id
	 */
	function set_appointment_template_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
