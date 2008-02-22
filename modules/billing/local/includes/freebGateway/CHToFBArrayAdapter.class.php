<?php

/**
 * Takes an array of a Clearhealth ORDO and changes it into an array that can be
 * used with Freeb2.
 *
 * This generally will not be used directly, but rather as part of 
 * {@link ClearhealthToFreebGateway}.
 *
 * @see ClearhealthToFreebGateway, adapted()
 * @package com.clear-health.clearhealth
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
	
	
	/**
	 * An array of methods to call for handling the various changes that need to
	 * be made.
	 *
	 * The methods it looks for will be _adapt<i>callback</i>(), (i.e., 
	 * General == {@link _adaptGeneral()}
	 */
	var $_callbacks = array('General', 'DateOfBirth', 'Address', 'Phone', 
		'Gender', 'Payer');
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
		foreach ($this->_callbacks as $methodSuffix) {
			$method = '_adapt' . $methodSuffix;
			$this->$method();
		}
			
		return $this->_adapted;
	}
	
	/**#@+
	 * @access private
	 */
	
	/**
	 * remove all person_id and type data
	 */
	function _adaptGeneral() {
		unset($this->_adapted['person_id']);
		unset($this->_adapted['type']);
	}
	
	/**
	 * map date_of_birth to dob
	 */
	function _adaptDateOfBirth() {
		if (!isset($this->_original['date_of_birth'])) {
			return;
		}
		$this->_adapted['dob'] = $this->_original['date_of_birth'];
	}
	
	/**
	 * map postal_code to zip & remove unnecessary address info
	 */
	function _adaptAddress() {
		if (!isset($this->_original['address'])) {
			return;
		}
		
		if (isset($this->_original['address']['postal_code'])) {
			$this->_adapted['address']['zip'] = $this->_original['address']['postal_code'];
		}
		
		unset($this->_adapted['address']['id']);
		unset($this->_adapted['address']['name']);
		unset($this->_adapted['address']['postal_code']);
		unset($this->_adapted['address']['region']);
	}
	
	/**
	 * map phone number
	 *
	 * @todo  there should be some kind of "billing phone number" flag or something...
	 */
	function _adaptPhone() {
		if (!isset($this->_original['home_phone'])) {
			return;
		}
		$this->_adapted['phone_number'] = $this->_original['home_phone'];
		unset($this->_original['home_phone']);
	}
	
	/**
	 * map gender
	 */
	function _adaptGender() {
		if (!isset($this->_original['gender'])) {
			return;
		}
		$this->_adapted['gender'] = substr($this->_original['gender'],0,1);
	}
	
	/**
	 * determine payer type from enum
	 *
	 * @todo switch to new {@link EnumManager{
	 */
	function _adaptPayer() {
		if (!isset($this->_original['payer_type'])) {
			return;
		}
		
		$payer = ORDataObject::factory("InsuranceProgram");
		$pt_enum = $payer->_load_enum("PayerType");
		$this->_adapted['payer_type'] = $pt_enum[$this->_original['payer_type']];
	}
	/**#@-*/
}
