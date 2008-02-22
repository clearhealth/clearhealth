<?php
/**
 * Object Relational Persistence Mapping Class for table: password_reset
 *
 * @package	com.clear-health.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: password_reset
 *
 * @package	com.clear-health.celini
 */
class Password_reset extends ORDataObject {

	/**#@+
	 * Fields of table: password_reset mapped to class members
	 */
	var $id			= '';
	var $user_id		= '';
	var $submit_date	= '';
	var $submitter_ip	= '';
	var $hash		= '';
	/**#@-*/

	/**
	 * Amount of seconds to keep a keep a password reset record around before garbage collecting it
	 */
	var $garbageTime = 172800;	// 2 days


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Password_reset($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'password_reset';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Password_reset with this
	 */
	function setup($id = 0) {
		$this->set('id',$id);
		if ($this->id == 0) {
			$this->generateHash();
			$this->garbageCollect();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('password_reset_id');
	}

	/**
	* Static method to get a User instance from a username
	*/
	function &fromHash($hash) {

		$pr =& ORDataObject::factory('Password_reset');

		$res = $pr->_execute("select password_reset_id from $pr->_prefix$pr->_table where hash = ".$pr->_quote($hash));
		if ($res->fields) {
			$id = $res->fields['password_reset_id'];
		}
		else {
			return $pr;
		}
		$pr->set('password_reset_id',$id);
		$pr->populate();
		return $pr;
	}

	/**
	 * Persist the record, setting submit_date to now
	 */
	function persist() {

		$this->set('submit_date',date('Y-m-d H:i:s'));
		parent::persist();
	}

	/**
	 * Delete unused reset records
	 */
	function garbageCollect() {
		$this->_execute("delete from $this->_prefix$this->_table where submit_date < (NOW() - $this->garbageTime)");
	}

	/**
	 * Generate a new hash
	 */
	function generateHash() {
		$this->hash = md5(uniqid(rand(),true).'orangeSocks');
	}

	/**#@+
	 * Getters and Setters for Table: password_reset
	 */

	
	/**
	 * Getter for Primary Key: password_reset_id
	 */
	function get_password_reset_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: password_reset_id
	 */
	function set_password_reset_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
