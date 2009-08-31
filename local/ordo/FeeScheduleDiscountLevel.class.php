<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_discount_level
 *
 * @package	com.clear-health.Celini
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
	var $_internalName='FeeScheduleDiscountLevel';


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
		$discount = ereg_replace('\$|%',"",$discount);
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

	function set_discount($discount){
			
		if(strstr($discount,"$")){
			$this->type = 'flat';
		}
		else{
			$this->type = 'percent';
		}
		
		$discount = ereg_replace('\$|%',"",$discount);
		$this->discount = $discount;
	}

	function value_discount() {
		switch ($this->get('type')) {
			case 'flat' :
				return '$' . $this->get('discount');
				break;
			
			case 'percent' :
				return $this->get('discount') . '%';
				break;
		}
	}
	
	
	function setupByPracticeIncomeSize($practiceId,$income,$size, $programId) {
		$practiceId = EnforceType::int($practiceId);
		$income = EnforceType::int($income);
		$size = EnforceType::int($size);
		$programId = EnforceType::int($programId);
		$programSql = " d.insurance_program_id = " . $programId ." ";
		if ($programId == 0) {
			$programSql = " l.type == 'default' ";
		}
		$table = $this->tableName();

		$sql = "
			select l.* 
			from 
				$table l
				inner join fee_schedule_discount_income i on l.fee_schedule_discount_level_id = i.fee_schedule_discount_level_id
				inner join fee_schedule_discount d ON d.fee_schedule_discount_id=l.fee_schedule_discount_id
			where
				i.family_size = $size and
				i.income >= $income and
				d.practice_id = $practiceId AND
				(d.insurance_program_id = {$programId} OR d.type = 'default')
			order by i.income asc
			";
		$res = $this->dbHelper->execute($sql);
		$this->helper->populateFromResults($this,$res);
	}
	
	function setupByPracticeCode($practiceId, $fee) {
		$qPracticeId = $this->dbHelper->quote($practiceId);
		$table = $this->tableName();
		$qCodePattern = $this->dbHelper->quote($fee['code']);
		$sql = "
			SELECT
				fsdl.*
			FROM 
				{$table} AS fsdl
				INNER JOIN fee_schedule_discount_by_code AS fsdc on fsdc.fee_schedule_discount_level_id = fsdl.fee_schedule_discount_level_id
				INNER JOIN fee_schedule_discount AS fsd on fsd.fee_schedule_discount_id = fsdc.fee_schedule_discount_id
			WHERE
				fsdc.code_pattern = {$qCodePattern} AND
				fsd.practice_id = {$qPracticeId}";
		$res = $this->dbHelper->execute($sql);
		$this->helper->populateFromResults($this, $res);
	}
	
	
	function setupByPracticeCodeWildcard($practiceId, $fee) {
		$qPracticeId = $this->dbHelper->quote($practiceId);
		$table = $this->tableName();
		$qCodePattern = $this->dbHelper->quote($fee['code']);
		$sql = "
			SELECT
				fsdl.*
			FROM 
				{$table} AS fsdl
				INNER JOIN fee_schedule_discount_by_code AS fsdc on fsdc.fee_schedule_discount_level_id = fsdl.fee_schedule_discount_level_id
				INNER JOIN fee_schedule_discount AS fsd on fsd.fee_schedule_discount_id = fsdl.fee_schedule_discount_id
			WHERE
				{$qCodePattern} LIKE fsdc.code_pattern AND
				fsd.practice_id = {$qPracticeId}";
		$res = $this->dbHelper->execute($sql);
		$this->helper->populateFromResults($this, $res);
	}
}
?>
