<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_discount_level
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class FeeScheduleDiscountLevel extends ORDataObject {

	/**#@+
	 * Fields of table: fee_schedule_discount_level mapped to class members
	 */
	var $fee_schedule_discount_level_id	= '';
	var $fee_schedule_discount_id		= '';
	var $discount				= '';
	var $disp_order				= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'fee_schedule_discount_level';

	/**
	 * Primary Key
	 */
	var $_key = 'fee_schedule_discount_level_id';


	function setupByDiscount($fsdId,$discount) {
		$fsdId = EnforceType::int($fsdId);
		$discountQ = $this->dbHelper->quote($discount);
		$table = $this->tableName();

		$sql = "select * from $table where fee_schedule_discount_id = $fsdId and discount = $discountQ";
		$res = $this->dbHelper->execute($sql);

		$this->helper->populateFromResults($this,$res);

		if (!$this->isPopulated()) {
			$this->set('fee_schedule_discount_id',$fsdId);
			$this->set('discount',$discount);
		}
	}
}
?>
