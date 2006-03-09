<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_discount
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class FeeScheduleDiscountProgram extends ORDataObject {

	/**#@+
	 * Fields of table: fee_schedule_discount mapped to class members
	 */
	var $fee_schedule_discount_program_id		= '';
	var $fee_schedule_discount_id		= '';
	var $insurance_program_id		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'fee_schedule_discount_program';

	/**
	 * Primary Key
	 */
	var $_key = 'fee_schedule_discount_program_id';

	var $_foreignKeyList = array('practice_id' => 'Practice');
}
?>
