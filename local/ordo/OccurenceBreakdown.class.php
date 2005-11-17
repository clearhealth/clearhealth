<?php
/**
 * Object Relational Persistence Mapping Class for table: occurence_breakdown
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

$loader->requireOnce('ordo/ORDataObject.class.php');

/**
 * Object Relational Persistence Mapping Class for table: occurence_breakdown
 *
 * @package	com.uversainc.Celini
 */
class OccurenceBreakdown extends ORDataObject {

	/**#@+
	 * Fields of table: occurence_breakdown mapped to class members
	 */
	var $id		= '';
	var $occurence_id		= '';
	var $offset		= '';
	var $length		= '';
	var $user_id		= '';
	var $title		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function OccurenceBreakdown($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'occurence_breakdown';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('occurence_breakdown_id');
	}

	/**#@+
	 * Getters and Setters for Table: occurence_breakdown
	 */

	
	/**
	 * Getter for Primary Key: occurence_breakdown_id
	 */
	function get_occurence_breakdown_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: occurence_breakdown_id
	 */
	function set_occurence_breakdown_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	function breakdownArray($occurenceId) {
		$sql = "select * from ".$this->tableName()." where occurence_id = ".$this->dbHelper->quote($occurenceId);
		$res = $this->dbHelper->execute($sql);

		$ret = array();
		while($res && !$res->EOF) {
			$ret[] = $res->fields;
			$res->MoveNext();
		}
		return $ret;
	}
}
?>
