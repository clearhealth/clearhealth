<?php

class FeeScheduleDiscountByCode extends ORDataObject
{
	/**#@+
	 * @access protected
	 */
	var $fee_schedule_discount_by_code_id  = '';
	var $fee_schedule_discount_id          = '';
	var $fee_schedule_discount_level_id	   = '';
	var $code_pattern = '';
	
	var $_table = 'fee_schedule_discount_by_code';
	var $_key = 'fee_schedule_discount_by_code_id';
	/**#@-*/
	
	function setupByCodeLevel($fsdId, $codePattern, $level) {
		$table = $this->tableName();
		$qFsdId = $this->dbHelper->quote($fsdId);
		$qCodePattern = $this->dbHelper->quote($codePattern);
		$qLevel = $this->dbHelper->quote($level);
		
		$sql = "
			SELECT 
				* 
			FROM
				{$table}
			WHERE
				fee_schedule_discount_id = {$qFsdId} AND
				fee_schedule_discount_level_id = {$qLevel} AND
				code_pattern = {$qCodePattern}";
		$this->helper->populateFromResults($this, $this->dbHelper->execute($sql));
		
		if (!$this->isPopulated()) {
			$this->set('fee_schedule_discount_id',$fsdId);
			$this->set('fee_schedule_discount_level_id',$level);
			$this->set('code_pattern', $codePattern);
		}
	}
	
	
	function set_code_pattern($value) {
		$this->code_pattern = str_replace('*', '%', $value);
	}
}

