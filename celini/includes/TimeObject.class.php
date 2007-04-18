<?php
// $Id$

class TimeObject
{
	var $hour   = null;
	var $minute = null;
	var $second = null;
	
	
	/**
	 * The format to return the string represntation of this TimeObject in
	 *
	 * @var		string
	 * @see		setFormat(), getFormat()
	 * @access	private
	 */
	var $_format = "%H:%i:%s";
	
	/**
	 * Contains the values that we're looking to replace out with the hour,
	 * minute, and second.
	 *
	 * @var		array
	 * @see		toString()
	 * @access	private
	 */
	var $_valuePlaceholders = array(array('%H', '%h'), '%i', '%s');
	
	
	function TimeObject() {
 		$this->_format = TimeObject::_determineFormat();
	}
	
	/**
	 * Creates an returns a TimeObject with the given time
	 *
	 * Accepts both 24 and 12 hour time formats.
	 *
	 * @see create24Hour(), create12Hour()
	 * @param string
	 * @return TimeObject
	 */
	function &create($time) {
		$newTime =& TimeObject::create24Hour($time);
		if ($newTime->isValid()) {
			return $newTime;
		}
		
		$newTime =& TimeObject::create12Hour($time);
		if ($newTime->isValid()) {
			return $newTime;
		}
		
		$newTime =& new TimeObject();
		return $newTime;
	}
	
	
	/**
	 * Create and return a new {@link TimeObject} with the provided 24 hour time string
	 *
	 * @param  string
	 * @return TimeObject
	 */
	function &create24Hour($time) {
		$return =& new TimeObject();
		if (!preg_match('/^(\d{1,2})([\:](\d{2}))?([\:](\d{2}))?$/', $time, $matches)) {
			return $return;
		}
		$hour = $matches[1];
		$minute = '00';
		$second = '00';
		if (isset($matches[3])) {
			$minute = $matches[3];
		}
		if (isset($matches[5])) {
			$second = $matches[5];
		}
		
		$newTime =& new TimeObject();
		$newTime->_set('hour',   $hour);
		$newTime->_set('minute', $minute);
		$newTime->_set('second', $second);
		return $newTime;
	}
	
	
	/**
	 * Create and return a new {@link TimeObject} with the provided 12 hour time string
	 *
	 * @param  string
	 * @return TimeObject
	 */
	function &create12Hour($time) {
		$return =& new TimeObject();
		//if (!preg_match('/^(\d{1,2})([\:](\d{2}))?([\:](\d{2}))?$/', $time, $matches)) {
		if (!preg_match('/^(\d{1,2})(:?(\d{2})?(:(\d{2}))?( (AM|PM))?)?$/i', $time, $matches)) {
			return $return;
		}
		list(, $hour, , $minutes, , $seconds, , $suffix) = $matches;
		
		if (strtoupper($suffix) == 'PM') {
			$hour = $hour + 12;
		}
			
		//$return =& new TimeObject();
		$return->_set('hour', $hour);
		$return->_set('minute', $minutes);
		$return->_set('second', empty($seconds) ? '00' : $seconds);
		return $return;	
	}
	
	
	
	/**
	 * Returns whether this is a valid {@link TimeObject}
	 *
	 * @return boolean
	 */
	function isValid() {
		return !(is_null($this->hour) || is_null($this->minute) || is_null($this->second));
	}
	
	
	/**
	 * Returns a string representation of this object
	 *
	 * If $format is set, it will use that format to determine the output,
	 * otherwise, the last format set via {@link setFormat()} will be used.
	 *
	 * @param	string|null
	 * @return	string
	 */
	function toString($format = null) {
		if (!$this->isValid()) { 
			return '00:00:00';
		}
		
		if (is_null($format)) {
			$format = $this->_format;
		}
		
		$replacementValues = array($this->hour, $this->minute, $this->second);
		$timestamp = strtotime($this->hour . ':' . $this->minute . ':' . $this->second);
		return date(str_replace('%', '', $format), $timestamp);
	}
	
	
	/**
	 * Returns a 12 hour formatted string reprenstation of this object
	 *
	 * @return string
	 */
	function to12Hour($showMeridiem = true) {
		return $this->toString('%g:%i:%s' . ($showMeridiem ? ' %A' : ''));
	}
	
	
	/**
	 * Sets the format that this {@link TimeObject::toString()} should return 
	 * in by default.
	 *
	 * @param	string
	 */
	function setFormat($format) {
		$this->_format = $format;
	}
	
	
	/**
	 * Returns the format that {@link TimeObject::toString()} will return in
	 * by default.
	 *
	 * If this is called statically, it will be return what the system wide
	 * default format is.
	 *
	 * @return	string
	 */
	function getFormat() {
		if (is_a($this, 'TimeObject')) {
			return $this->_format;
		}
		else {
			return TimeObject::_determineFormat();
		}
	}
	
	
	/**
	 * Returns AM or PM depending on what hour this represents
	 *
	 * @return string
	 */
	function getMeridiem() {
		return $this->hour > 12 ? 'PM' : 'AM';
	}

	/**
	 * Check if a date hasn't been set on the object
	 *
	 * @return 	boolean
	 */
	function isEmpty() {
		return ($this->hour === '00' && $this->minute === '00' && $this->second === '00');
	}
	
	
	/**
	 * Used internally to determine what the default format should be
	 *
	 * @return	string
	 * @access	private
	 * @static
	 */
	function _determineFormat() {
		if (isset($GLOBALS['config']['locale']['time_format'])) {
			return $GLOBALS['config']['locale']['time_format'];
		}
		else {
			return '%H:%i:%s';
		}
	}
	
	
	/**
	 * Used internally to set the public properties.
	 *
	 * This insures that all values are zero-padded
	 *
	 * @param	string	The key to set
	 * @param	string	The value to set to
	 * @access	private
	 */
	function _set($key, $value) {
		if (is_null($value) || empty($value)) {
			$value = '00';
		}
		elseif (strlen($value) == 1) {
			$value = '0' . $value;
		}
		
		$this->$key = $value;
	}
}

?>
