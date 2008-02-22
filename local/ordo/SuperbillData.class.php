<?php
/**
 * Object Relational Persistence Mapping Class for table: superbill_data
 *
 * @package	com.clear-health.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: superbill_data
 *
 * @package	com.clear-health.clearhealth
 */
class SuperbillData extends ORDataObject {

	/**#@+
	 * Fields of table: superbill_data mapped to class members
	 */
	var $superbill_data_id	= '';
	var $superbill_id	= '';
	var $code_id		= '';
	var $status		= '';
	/**#@-*/
	var $_table = 'superbill_data';
	var $_internalName='SuperbillData';
	var $_key = 'superbill_data_id';


	/**
	 */
	function SuperbillData() {
		parent::ORDataObject();
	}

	function setupBySuperbillCode($superbillId,$codeId) {
		$s = Enforcetype::int($superbillId);
		$c = EnforceType::int($codeId);

		$sql = "select * from ".$this->tableName()." where superbill_id = $s and code_id = $c";
		$this->helper->populateFromQuery($this,$sql);
		$this->set('superbill_id',$s);
		$this->set('code_id',$c);
	}

	/**#@+
	 * Getters and Setters for Table: superbill_data
	 */

	
	/**
	 * Getter for Primary Key: superbill_data_id
	 */
	/**#@-*/
}
?>
