<?php
/**
 * Object Relational Persistence Mapping Class for table: misc_charge
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class MiscCharge extends ORDataObject {

	/**#@+
	 * Fields of table: misc_charge mapped to class members
	 */
	var $misc_charge_id		= '';
	var $encounter_id		= '';
	var $amount		= '';
	var $charge_date		= '';
	var $title		= '';
	var $note		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'misc_charge';

	/**
	 * Primary Key
	 */
	var $_key = 'misc_charge_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'MiscCharge';

	/**
	 * Handle instantiation
	 */
	function MiscCharge() {
		parent::ORDataObject();
	}

	function totalChargesForEncounter($encounterId) {
		$eId = EnforceType::int($encounterId);

		$sql = "select sum(amount) from misc_charge where encounter_id = $eId";
		$ret = $this->dbHelper->getOne($sql);
		//var_dump($ret);
		return $ret;
	}
	
}
?>
