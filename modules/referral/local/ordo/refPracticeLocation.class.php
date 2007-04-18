<?php

/**
 * This is accessed by {@link C_Refpracitce}
 */
class refPracticeLocation extends ORDataObject {
	/**#@+
	 * @access protected
	 * @see get()
	 */
	 
	/**
	 * Primary key for this ordo
	 *
	 * @var int
	 */
	var $refPracticeLocation_id = '';
	
	/**#@+
	 * Fields stored within the ORDO table
	 *
	 * @var string
	 */
	var $address1 = '';
	var $address2 = '';
	var $city = '';
	var $state = '';
	var $zipcode = '';
	var $appointment_number = '';
	var $phone_number = '';
	var $fax_number = '';
	/**#@-*/
	
	/**
	 * External id references
	 *
	 * @var int
	 */
	var $refPractice_id = '';
	
	/**#@-*/
	
	var $_table = 'refPracticeLocation';
	
	var $defaultValueValue = '';
	
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',(int) $id);
			$this->populate();
		}
	}
	
	function populate() {
		parent::populate('refPracticeLocation_id');
	}
	
	function persist() {
		parent::persist();
	}
	
	function get_id() {
		return $this->get('refPracticeLocation_id');
	}
	
	function set_id($value) {
		$this->set('refPracticeLocation_id', $value);
	}
	
	function value($key) {
		$value = parent::value($key);
		if (!$this->isPopulated() && empty($value)) {
			return $this->defaultValueValue;
		}
	}
	
	function value_print_address() {
		$address = $this->get('address1');
		if ($this->get('address2') != '') {
			$address .= ', ' . $this->get('address2');
		}
		return $address;
	}
}
