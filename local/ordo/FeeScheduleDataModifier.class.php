<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_data_modifiers
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class FeeScheduleDataModifier extends ORDataObject {

	/**#@+
	 * Fields of table: fee_schedule_data_modifiers mapped to class members
	 */
	var $fsd_modifier_id	= '';
	var $fee_schedule_id	= '';
	var $code_id		= '';
	var $modifier		= '';
	var $fee		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'fee_schedule_data_modifier';

	/**
	 * Primary Key
	 */
	var $_key = 'fsd_modifier_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'FeeScheduleDataModifier';

	/**
	 * Handle instantiation
	 */
	function FeeScheduleDataModifier() {
		parent::ORDataObject();
	}

	function setupByFeeScheduleCodeModifier($fee_schedule_id,$code_id,$modifier) {
		$f = EnforceType::int($fee_schedule_id);
		$c = EnforceType::int($code_id);
		$m = EnforceType::int($modifier);

		$sql = "select * from ".$this->tableName()." where fee_schedule_id = $f and code_id = $c and modifier = $m";

		$this->helper->populateFromQuery($this,$sql);
	}

	
}
?>
