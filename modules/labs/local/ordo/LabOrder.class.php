<?php
/**
 * Object Relational Persistence Mapping Class for table: lab_order
 *
 * @package	com.uversainc.celini
 * @author	Uversa Inc.
 */
class LabOrder extends ORDataObject {

	/**#@+
	 * Fields of table: lab_order mapped to class members
	 */
	var $lab_order_id		= '';
	var $patient_id		= '';
	var $type		= '';
	var $status		= '';
	var $ordering_provider		= '';
	var $manual_service		= '';
	var $manual_order_date		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'lab_order';

	/**
	 * Primary Key
	 */
	var $_key = 'lab_order_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'LabOrder';

	/**
	 * Handle instantiation
	 */
	function LabOrder() {
		parent::ORDataObject();
	}

	
	/**#@+
	 * Field: manual_order_date, time formatting
	 */
	function get_manual_order_date() {
		return $this->_getDate('manual_order_date');
	}
	function set_manual_order_date($date) {
		$this->_setDate('manual_order_date',$date);
	}
	/**#@-*/
	function get_manual_service_label() {
                $em =& Celini::enumManagerInstance();
                return $em->lookup('lab_manual_service_list',$this->get('manual_service'));
	}

}
?>
