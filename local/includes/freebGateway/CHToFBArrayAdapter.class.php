<?php

/**
 * Takes an array of a Clearhealth ORDO and changes it into an array that can be
 * used with Freeb2.
 *
 * This generally will not be used directly, but rather as part of 
 * {@link ClearhealthToFreebGateway}.
 *
 * @see ClearhealthToFreebGateway, adapted()
 * @package com.uversainc.clearhealth
 * @subpackage modules.freeb2
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class CHToFBArrayAdapter
{
	/**#@+
	 * @access private
	 * @var array
	 */
	
	/**
	 * The original CH array
	 */
	var $_original = array();
	
	/**
	 * The adapted FB array
	 */
	var $_adapted  = array();
	/**#@-*/
	
	
	/**
	 * Handle initialization
	 *
	 * @param array
	 */
	function CHToFBArrayAdapter($array) {
		assert('is_array($array)');
		$this->_original = $array;
		$this->_adapted  = $array;
	}
	
	
	/**
	 * Return the array this was instantiated with after it has been adapted to
	 * Freeb2 format.
	 *
	 * @return array
	 */
	function adapted() {
		// map date_of_birth to dob
		if (isset($this->_original['date_of_birth'])) {
			$this->_adapted['dob'] = $this->_original['date_of_birth'];
		}
		
		// map postal_code to zip
		if (isset($this->_original['address']['postal_code'])) {
			$this->_adapted['address']['zip'] = $this->_original['address']['postal_code'];
		}

		// map phone number
		// TODO: there should be some kind of "billing phone number" flag or something...
		if (isset($this->_original['home_phone'])) {
			$this->_adapted['phone_number'] = $this->_original['home_phone'];
			unset($this->_original['home_phone']);
		}
		if (isset($this->_original['gender'])) { 
			$this->_adapted['gender'] = substr($this->_original['gender'],0,1);
		}
		
		// remove unnecessary address info
		if (isset($this->_original['address'])) {
			unset($this->_adapted['address']['id']);
			unset($this->_adapted['address']['name']);
			unset($this->_adapted['address']['postal_code']);
			unset($this->_adapted['address']['region']);
		}
		
		// determine payer type from enum
		if (isset($this->_original['payer_type'])) {
			$payer = ORDataObject::factory("InsuranceProgram");
			$pt_enum = $payer->_load_enum("PayerType");
			$this->_adapted['payer_type'] = $pt_enum[$this->_original['payer_type']];
		}
		
		// remove all person_id and type data
		unset($this->_adapted['person_id']);
		unset($this->_adapted['type']);
		return $this->_adapted;
	}
}
