<?php
/**
 * Object Relational Persistence Mapping Class for table: statement_history
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

$loader->requireOnce('ordo/ORDataObject.class.php');

/**
 * Object Relational Persistence Mapping Class for table: statement_history
 *
 * @package	com.uversainc.Celini
 */
class StatementHistory extends ORDataObject {

	/**#@+
	 * Fields of table: statement_history mapped to class members
	 */
	var $id			= '';
	var $patient_id		= '';
	var $report_snapshot_id = '';
	var $statement_number	= false;
	var $date_generated	= false;
	var $amount		= '';
	var $type		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function StatementHistory($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'statement_history';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('statement_history_id');
	}

	/**#@+
	 * Getters and Setters for Table: statement_history
	 */

	
	/**
	 * Getter for Primary Key: statement_history_id
	 */
	function get_statement_history_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: statement_history_id
	 */
	function set_statement_history_id($id)  {
		$this->id = $id;
	}

	function get_statement_number() {
		if (!$this->statement_number) {
			$this->statement_number = $this->dbHelper->nextId('statement_sequence');
		}
		return $this->statement_number;
	}

	function set_date_generated($date) {
		$this->_setDate('date_generated', $date);
	}
	function get_date_generated() {
		if ($this->date_generated === false) {
			$this->_setDate('date_generated', date('Y-m-d H:i:s'));
		}
		return $this->_getTimestamp('date_generated');
	}

	function get_pay_by() {
		$this->_setDate('pay_by',date('Y-m-d',strtotime('now + 30 days')));
		return $this->_getDate('pay_by');
	}

	/**#@-*/
}
?>
