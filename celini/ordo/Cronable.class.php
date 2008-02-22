<?php
/**
 * Object Relational Persistence Mapping Class for table: cronable
 *
 * @package	com.clear-health.Celini
 */

$loader->requireOnce('ordo/ORDataObject.class.php');

/**
 * Object Relational Persistence Mapping Class for table: cronable
 *
 * @package	com.clear-health.Celini
 */
class Cronable extends ORDataObject {
	
	/**#@+
	 * Fields of table: Cronable mapped to class members
	 */
	var $cronable_id	= '';
	var $label = '';
	var $minute = '';
	var $hour = '';
	var $day_of_month = '';
	var $month = '';
	var $day_of_week = '';
	var $year = '';
	var $at_time	= '';
	var $wrapper = '';
	var $controller = '';
	var $action = '';
	var $arguments = '';
	var $last_run = '';
	var $_table = 'cronable';
	var $_key = 'cronable_id';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */

	/*function Cronable($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'cronable';
		$this->_sequence_name = 'sequences';	
	}*/
	
	function set_arguments($arguments) {
		$this->arguments = serialize($arguments);
		
	}
	function get_arguments() {
		if (strlen($this->arguments) > 0) {
			return unserialize($this->arguments); 	
		}
		
	}
	
	
}
