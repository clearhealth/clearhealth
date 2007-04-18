<?php
// $Id: DateObject.class.php 1545 2006-08-28 21:55:14Z jeichorn $

/**
 * A represtnations of a date as an object.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class DateObject
{
	var $year  = null;
	var $month = null;
	var $day   = null;
	
	/**
	 * The date format this object's toString() should return the values in
	 *
	 * @var		string
	 * @access	private
	 * @see		setFormat(), toString();
	 */
	var $_format = "%Y-%m-%d";
	
	/**
	 * The placeholders that {@link $_format} will use for determining what
	 * values are replaced with what strings.
	 *
	 * @var		array
	 * @access	private
	 * @see		toString()
	 */
	var $_valuePlaceholders = array('%Y', '%m', '%d');
	
	
	function DateObject() {
		$this->_format = DateObject::_determineFormat();
	}
	
	
	/**
	 * Creates and returns a DateObject, regardless of type.
	 *
	 * @param string
	 * @return DateObject
	 */
	function &create($date) {
		$dateObj =& DateObject::createFromISO($date);
		if ($dateObj->isValid()) {
			return $dateObj;
		}
		
		$dateObj =& DateObject::createFromUSA($date);
		if ($dateObj->isValid()) {
			return $dateObj;
		}
		
		$return =& new DateObject();
		return $return;
	}
	
	/**
	 * Creates an returns a DateObject from an ISO-formatted date string
	 *
	 * @param string
	 * @return DateObject
	 */
	function &createFromISO($date) {
		$newDate =& new DateObject();
		if (!preg_match('/^(\d{4})[\/-]*(\d{1,2})[\/-]*(\d{1,2})$/', $date, $matches)) {
			return $newDate;
		}
		
		$newDate->year  = $matches[1];
		$newDate->month = $matches[2];
		$newDate->day   = $matches[3];
		return $newDate;
	}
	
	/**
	 * Creates an returns a DateObject from an USA-formatted date string
	 *
	 * @param string
	 * @param DateObject
	 */
	function &createFromUSA($date) {
		$newDate =& new DateObject();
		if (!preg_match('/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{2,4})$/', $date, $matches)) {
			return $newDate;
		}
		
		
		$newDate->year  = $matches[3];
		if (strlen($newDate->year) == 2) {
			$newDate->year = '19'.$newDate->year;
		}
		if (strlen($newDate->year) == 3) {
			$newDate->year = '1'.$newDate->year;
		}
		$newDate->month = $matches[1];
		$newDate->day   = $matches[2];
		
		return $newDate;
	}
	
	function isValid() {
		return (!is_null($this->year) && !is_null($this->month) && !is_null($this->day));
	}
	
	/**
	 * Returns this DateObject as an ISO-formatted date
	 *
	 * @return string
	 */
	function toISO() {
		return $this->toString('%Y-%m-%d');
	}
	
	/**
	 * Returns this DateObject as a USA-formatted date
	 *
	 * @return string
	 */
	function toUSA() {
		return $this->toString('%m/%d/%Y');
	}
	
	
	/**
	 * Takes an ISO-formatted date string and returns it as an English 
	 * formatted string.
	 *
	 * If the format is not recognized, this returns the $dateString value 
	 * unchanged.
	 * 
	 * @param	string
	 * @return	string
	 * @static
	 */
	function ISOtoUSA($dateString) {
		$date =& DateObject::createFromISO($dateString);
		if (!$date->isValid()) {
			return $dateString;
		}
		
		return $date->toUSA();
	}
	
	
	/**
	 * Returns a string representation of this DateObject
	 *
	 * If $format is specified, it should be equivalent to a format string from
	 * MySQL's date_foramt() method.  If it is not specified, the last format
	 * set via {@link setFormat()} will be used. 
	 *
	 * @param	string|null
	 * @return	string
	 * @see		setFormat()
	 */
	function toString($format = null) {
		if (is_null($format)) {
			$format = $this->_format;
		}
		
		$replacementValues = array($this->year, $this->month, $this->day);
		return str_replace($this->_valuePlaceholders, $replacementValues, $format);
	}
	
	
	/**
	 * Used to set the format that this instance of DateObject should return
	 * by default via toString().
	 *
	 * @param	string
	 */
	function setFormat($format) {
		$this->_format = $format;
	}
	
	
	/**
	 * Returns the format that this DateObject::toString() will return in.
	 *
	 * If called statically, this will determine the default type that would
	 * be returned if this were instantiated via create().
	 *
	 * @return	string
	 * @see		setFormat(), _determineFormat()
	 */
	function getFormat() {
		if (is_a($this, 'DateObject')) {
			return $this->_format;
		}
		else {
			return DateObject::_determineFormat();
		}
	}

	/**
	 * Check if a date hasn't been set on the object
	 *
	 * @return 	boolean
	 */
	function isEmpty() {
		return ($this->year === '0000' && $this->month === '00' && $this->day === '00');
	}
	
	
	/**
	 * Returns the format that should be used by default.
	 *
	 * If $config['locale']['date_format'] is set, return that format,
	 * otherwise assume it's ISO.
	 *
	 * @return string
	 * @access private
	 * @static
	 */
	function _determineFormat() {
		if (isset($GLOBALS['config']['locale']['date_format'])) {
			return $GLOBALS['config']['locale']['date_format'];
		}
		else {
			return '%Y-%m-%d';
		}
	}
}

?>
