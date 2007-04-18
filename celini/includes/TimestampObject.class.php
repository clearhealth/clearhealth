<?php
// $Id: TimestampObject.class.php 1614 2006-10-13 16:05:40Z jeichorn $

$loader->requireOnce('includes/DateObject.class.php');
$loader->requireOnce('includes/TimeObject.class.php');

/**
 * @todo Document this and refractor it so the seperator between the date/time
 *	isn't arbitrary.
 */
class TimestampObject
{
	var $_dateObject = false;
	var $_timeObject = false;
	
	function &create($timestamp) {
		$newTimestamp =& new TimestampObject();
		
		$exploded = explode(" ", $timestamp);
		$newTimestamp->_dateObject =& DateObject::create($exploded[0]);
		if (!$newTimestamp->_dateObject->isValid()) {
			$newTimestamp->_dateObject =& DateObject::create('0000-00-00');
		}
		
		if (isset($exploded[1])) {
			$newTimestamp->_timeObject =& TimeObject::create($exploded[1]);
		}
		if ($newTimestamp->_timeObject === false) {
			$newTimestamp->_timeObject =& TimeObject::create('00:00:00');
		}
		
		return $newTimestamp;
	}
	
	function &getDate() {
		return $this->_dateObject;
	}
	
	function &getTime() {
		return $this->_timeObject;
	}
	
	
	/**
	 * Returns the aggregate format from this TimestampObject's DateObject and
	 * TimeObject.
	 *
	 * @return	string
	 */
	function getFormat() {
		if (is_a($this, 'TimestampObject')) {
			return $this->_dateObject->getFormat() . ' ' . $this->_timeObject->getFormat();
		}
		else {
			return DateObject::getFormat() . ' ' . TimeObject::getFormat();
		}
	}

	/**
	 * Check if a date hasn't been set on the object
	 *
	 * @return 	boolean
	 */
	function isEmpty() {
		return ($this->_dateObject->isEmpty() && $this->_timeObject->isEmpty());
	}
	
	
	function toString($format = null) {
		if (!is_null($format)) {
			list($date_format, $time_format) = explode(' ', $format);
		}
		else {
			$date_format = null;
			$time_format = null;
		}
		return $this->_dateObject->toString($date_format) . ' ' . $this->_timeObject->toString($time_format);
	}
	
	
	function toISO() {
		return $this->_dateObject->toISO() . ' ' . $this->_timeObject->toString();
	}
}

