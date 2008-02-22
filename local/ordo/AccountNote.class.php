<?php
/**
 * Object Relational Persistence Mapping Class for table: account_note
 *
 * @package	com.clear-health.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class AccountNote extends ORDataObject {

	/**#@+
	 * Fields of table: account_note mapped to class members
	 */
	var $account_note_id	= '';
	var $patient_id		= '';
	var $claim_id		= '';
	var $user_id		= '';
	var $date_posted	= '';
	var $note		= '';
	var $note_type		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'account_note';

	/**
	 * Primary Key
	 */
	var $_key = 'account_note_id';
	var $_internalName='AccountNote';

	
	function set_date_posted($datetime) {
		$this->_setDate('date_posted',$datetime);
	}
	function get_date_posted() {
		return $this->_getTimestamp('date_posted');
	}
}
?>
