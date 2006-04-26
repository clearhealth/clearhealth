<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_discount_income
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class FeeScheduleDiscountIncome extends ORDataObject {

	/**#@+
	 * Fields of table: fee_schedule_discount_income mapped to class members
	 */
	var $fee_schedule_discount_income_id	= '';
	var $fee_schedule_discount_id		= '';
	var $fee_schedule_discount_level_id	= '';
	var $family_size			= '';
	var $income				= '';
	/**#@-*/
	var $_internalName='FeeScheduleDiscountIncome';


	/**
	 * DB Table
	 */
	var $_table = 'fee_schedule_discount_income';

	/**
	 * Primary Key
	 */
	var $_key = 'fee_schedule_discount_income_id';

	function setupByFamilySizeLevel($fsdId,$familySize,$level) {
		$table = $this->tableName();
		$fsdId = EnforceType::int($fsdId);
		$familySize = EnforceType::int($familySize);
		$level = EnforceType::int($level);


		$sql = "select * from $table where 
			fee_schedule_discount_id = $fsdId and family_size = $familySize and fee_schedule_discount_level_id = $level";

		$res = $this->dbHelper->execute($sql);

		$this->helper->populateFromResults($this,$res);

		if (!$this->isPopulated()) {
			$this->set('fee_schedule_discount_id',$fsdId);
			$this->set('fee_schedule_discount_level_id',$level);
			$this->set('family_size',$familySize);
		}
	}
}
?>
