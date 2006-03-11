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
	var $type					= '';
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
		//TODO fix this.
		if(strstr($discountQ,".")){
			$sql = "select * from $table where fee_schedule_discount_id = $fsdId and discount like $discountQ";
		}else{
		
		$sql = "select * from $table where fee_schedule_discount_id = $fsdId and discount = $discountQ";
		}
		
		$res = $this->dbHelper->execute($sql);

		$this->helper->populateFromResults($this,$res);

		if (!$this->isPopulated()) {
			$this->set('fee_schedule_discount_id',$fsdId);
			$this->set('discount',$discount);
		}
	}

	function setupByPracticeIncomeSize($practiceId,$income,$size) {
		$practiceId = EnforceType::int($practiceId);
		$income = EnforceType::int($income);
		$size = EnforceType::int($size);
		$table = $this->tableName();


		$sql = "
			select * 
			from 
				$table l
			inner join fee_schedule_discount_income i on l.fee_schedule_discount_level_id = i.fee_schedule_discount_level_id
			inner join fee_schedule_discount d using(fee_schedule_discount_id)
			where
				i.family_size = $size and i.income >= $income and d.practice_id = $practiceId
			order by i.income asc
			";
		$res = $this->dbHelper->execute($sql);
		$this->helper->populateFromResults($this,$res);
	}
}
?>
