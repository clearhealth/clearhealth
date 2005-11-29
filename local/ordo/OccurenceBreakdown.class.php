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
	var $id			= '';
	var $occurence_id	= '';
	var $index		= false;
	var $offset		= '';
	var $length		= '';
	var $user_id		= '';
	var $title		= '';
	/**#@-*/

	function setupByIndex($occurenceId,$index) {
		$sql = "select * from ".$this->tableName()." where occurence_id = ".$this->dbHelper->quote($occurenceId) .' and `index` = '.$this->dbHelper->quote($index);
		$res = $this->dbHelper->execute($sql);

		$this->helper->populateFromResults($this,$res);
		$this->set('occurence_id',$occurenceId);
		$this->set('index',$index);
	}

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

	function get_index() {
		if ($this->index === false || is_null($this->index)) {
			var_dump($this->index);
			$this->index =  $this->nextIndex();
		}
		return $this->index;
	}

	/**#@-*/

	function breakdownArray($occurenceId,$indexAsKey = false) {
		$sql = "select * from ".$this->tableName()." where occurence_id = ".$this->dbHelper->quote($occurenceId);
		$res = $this->dbHelper->execute($sql);

		$ret = array();
		while($res && !$res->EOF) {
			if ($indexAsKey) {
				$ret[$res->fields['index']] = $res->fields;
			} else {
				$ret[] = $res->fields;
			}
			$res->MoveNext();
		}
		return $ret;
	}

	function breakdownSum($occurenceId) {
		$sql = "select user_id, sum(length) length from ".$this->tableName()." where occurence_id = ".$this->dbHelper->quote($occurenceId).' group by user_id';
		$res = $this->dbHelper->execute($sql);

		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['user_id']] = $res->fields['length'];
			$res->MoveNext();
		}
		return $ret;
	}


	function nextIndex() {
		$sql = "select max(`index`) `index` from ".$this->tableName()." where occurence_id = ".$this->get('occurence_id');
		$res = $this->dbHelper->execute($sql);

		if ($res->EOF) {
			return 0;
		}
		else {
			return $res->fields['index']+1;
		}
	}
}
?>
