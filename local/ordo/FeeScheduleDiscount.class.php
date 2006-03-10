<?php
/**
 * Object Relational Persistence Mapping Class for table: fee_schedule_discount
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class FeeScheduleDiscount extends ORDataObject {

	/**#@+
	 * Fields of table: fee_schedule_discount mapped to class members
	 */
	var $fee_schedule_discount_id		= '';
	var $practice_id		= '';
	var $insurance_program_id = '';
	var $type	=	'';
	var $name		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'fee_schedule_discount';

	/**
	 * Primary Key
	 */
	var $_key = 'fee_schedule_discount_id';

	var $_foreignKeyList = array('practice_id' => 'Practice','insurance_program_id' => 'InsuranceProgram');
	
}
?>
