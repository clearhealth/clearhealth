<?php
/**
 * Object Relational Persistence Mapping Class for table: prescription
 *
 * @package	com.uversainc.Celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class Prescription extends ORDataObject {

	/**#@+
	 * Fields of table: prescription mapped to class members
	 */
	var $prescription_id	= '';
	var $patient_id		= '';
	var $filled_by_id	= '';
	var $pharmacy_id	= '';
	var $date_added		= '';
	var $date_modified	= '';
	var $provider_id	= '';
	var $start_date		= '';
	var $drug		= '';
	var $form		= '';
	var $dosage		= '';
	var $quantity		= '';
	var $size		= '';
	var $unit		= '';
	var $route		= '';
	var $interval		= '';
	var $substitute		= '';
	var $refills		= '';
	var $per_refill		= '';
	var $filled_date	= '';
	var $note		= '';
	var $active		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'prescription';

	/**
	 * Primary Key
	 */
	var $_key = 'prescription_id';
	var $_internalName='Prescription';

	
	/**#@+
	 * Field: date_added, time formatting
	 */
	function get_date_added() {
		return $this->_getDate('date_added');
	}
	function set_date_added($date) {
		$this->_setDate('date_added',$date);
	}
	/**#@-*/

	/**#@+
	 * Field: date_modified, time formatting
	 */
	function get_date_modified() {
		return $this->_getDate('date_modified');
	}
	function set_date_modified($date) {
		$this->_setDate('date_modified',$date);
	}
	/**#@-*/

	/**#@+
	 * Field: start_date, time formatting
	 */
	function get_start_date() {
		return $this->_getDate('start_date');
	}
	function set_start_date($date) {
		$this->_setDate('start_date',$date);
	}
	/**#@-*/

	/**#@+
	 * Field: filled_date, time formatting
	 */
	function get_filled_date() {
		return $this->_getDate('filled_date');
	}
	function set_filled_date($date) {
		$this->_setDate('filled_date',$date);
	}
	/**#@-*/

}
?>
