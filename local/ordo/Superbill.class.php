<?php
/**
 * Object Relational Persistence Mapping Class for table: superbill
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class Superbill extends ORDataObject {

	/**#@+
	 * Fields of table: superbill mapped to class members
	 */
	var $superbill_id	= '';
	var $name		= '';
	var $practice_id	= '';
	var $status		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'superbill';

	/**
	 * Primary Key
	 */
	var $_key = 'superbill_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'Superbill';

	/**
	 * Handle instantiation
	 */
	function Superbill() {
		parent::ORDataObject();
	}

	function SuperbillsForPractice($practiceId) {
		$p = EnforceType::int($practiceId);

		// get the superbills for this practice
		$sql = "select superbill_id from ".$this->tableName()." where practice_id = $p";
		$superbills = $this->dbHelper->getCol($sql);

		// get the default superbills
		if (count($superbills) == 0) {
			$sql = "select superbill_id from ".$this->tableName()." where practice_id = 0";
			$superbills = $this->dbHelper->getCol($sql);
		}

		// just try to get 1 superbill
		if (count($superbills) == 0) {
			$sql = "select superbill_id from ".$this->tableName()." limit 1";
			$superbills = $this->dbHelper->getCol($sql);
		}
		return $superbills;
	}
	
}
?>
