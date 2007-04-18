<?php

class refAppointment extends ORDataObject
{
	var $refappointment_id = '';
	var $refrequest_id = '';
	var $date = '';
	var $time = '';
	
	var $refpractice_id = '';
	var $reflocation_id = '';
	var $refprovider_id = '';
	
	var $_table = 'refappointment';
	var $_virtualValues = array();
	var $_internalName = 'refAppointment';
	
	function setupByRequest($request_id) {
		$this->set('refrequest_id', (int)$request_id);
		parent::populate('refrequest_id');
	}
	
	function populate() {
		parent::populate('refappointment_id');
	}
	
	function get_id() {
		return $this->get('refappointment_id');
	}
	
	function set_id($value) {
		$this->set('refappointment_id', $value);
	}
	
	function get_date() {
		return $this->_getTimestamp('date');
	}
	
	function set_date($value) {
		$this->_setDate('date', $value);
	}
	
	/**#@+
	 * Virtual accessor/mutators
	 *
	 * @access protected
	 */
	function get_date_month() {
		$dateObj =& $this->date->getDate();
		return $dateObj->month;
	}
	
	function get_date_year() {
		$dateObj =& $this->date->getDate();
		return $dateObj->year;
	}
	
	function get_date_date() {
		$dateObj =& $this->date->getDate();
		return $dateObj->day;
	}
	
	function get_time_digits() {
		$timeObj =& $this->date->getTime();
		return $timeObj->toString('%g:%i');
	}
	
	function get_time_suffix() {
		$timeObj =& $this->date->getTime();
		return $timeObj->getMeridiem();
	}
	
	/**
	 * Loads the refPractice ORDO associated with this appointment and returns
	 * its name
	 *
	 * @return string
	 */
	function get_practice_name() {
		$practice =& $this->getChild('refPractice');
		return $practice->get('name');
	}
	
	
	/**
	 * Deprecated, use {@link value_provider_name()} instead
	 *
	 * @see value_provider_name()
	 */
	function get_provider_name() {
		return $this->value('provider_name');
	}
	
	/**
	 * Loads the refProvider ORDO associated with this appointment and returns
	 * its full name
	 *
	 * @return string
	 */
	function value_provider_name() {
		$provider =& Celini::newORDO('refProvider', $this->get('refprovider_id'));
		return $provider->get('prefix') . ' ' . $provider->get('first_name') . ' ' . $provider->get('last_name');
	}
	
	/**
	 * Loads the refPracticeLocation ORDO assocaited with this appointment and
	 * returns its full data, formatted as a text multi-line string
	 *
	 * @return string
	 */
	function get_location_data() {
		$location =& Celini::newORDO('refPracticeLocation', $this->get('reflocation_id'));
		if (!$location->isPopulated()) {
			return '';
		}
		return sprintf("%s\n%s%s, %s %s",
			$location->get('address1'),
			($location->get('address2') != '') ? $location->get('address2') . "\n" : '',
			$location->get('city'), $location->get('state'), $location->get('zipcode'));
	}
	/**#@-*/
	
	
	/**
	 * Returns the {@link refPractice} associated with this
	 *
	 * @return refPractice
	 */
	function &getChild_refPractice() {
		$practice =& Celini::newORDO('refPractice', $this->get('refpractice_id'));
		return $practice;
	}
	
	
	/**
	 * Returns a {@link refPracticeLocation} associated with this
	 *
	 * @return refPracticeLocation
	 */
	function &getChild_refPracticeLocation() {
		$practiceLocation =& Celini::newORDO('refPracticeLocation', $this->get('reflocation_id'));
		return $practiceLocation;
	}
}

