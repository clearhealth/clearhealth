<?php

class CalendarDescriptionWeek {

	var $interval = 0;
	var $start = 0;
	var $end = 0;

	var $date;
	var $dateTs;
	var $_current = false;
	var $_object = false;

	var $parent;
	var $cd;

	function CalendarDescriptionWeek($d) {
		$this->date = $d;

		$this->dateTs = strtotime("$d[0]-$d[1]-$d[2]");
		$this->interval = 60 * 60 * 24; // One day
		
		// Figure out first day of week
		$today = date('w', $this->dateTs);
		if($today > 0){
			$this->start = strtotime($today." days ago", $this->dateTs);
		}else{
			$this->start = $this->dateTs;
		}
		$this->end = $this->start + ($this->interval * 7);
	}

	function setParent(&$p) {
		$this->parent =& $p;
	}

	function setCalendarDescription(&$cd) {
		$this->cd =& $cd;
	}
	
	function &getCalendarDescription(){
		return $this->cd;
	}

	/**
	 * Reset the iterator
	 */
	function rewind() {
		$this->_current = $this->start;
		$this->_object = new CalendarDescriptionInterval($this->_current,$this->interval);
		$this->_object->setParent($this);
	}

	/**
	 * Move to the next Interval
	 */
	function next() {
		$this->_current = strtotime("+1 day", $this->_current);
		if ($this->_current <= $this->end) {
			$this->_object = new CalendarDescriptionInterval($this->_current,$this->interval);
			$this->_object->setParent($this);
			return $this->_object;
		}
	}

	/**
	 * Is the current interval were on valid
	 */
	function valid() {
		if ($this->_current < $this->end && $this->_current >= $this->start) {
			return true;
		}
		return false;
	}

	/**
	 * Return an object that describes the current interval
	 */
	function current() {
		return $this->_object;
	}

	/**
	 * Is this the first interval in the week
	 */
	function isFirst() {
		if ($this->_current == $this->start) {
			return true;
		}
		return false;
	}

	/**
	 * Is this the last interval in the week
	 */
	function isLast() {
		if ($this->_current == $this->end) {
			return true;
		}
		return false;
	}
	
	function getPrevWeek(){
		return $this->start - ($this->interval * 7);
	}

	function getNextWeek(){
		return $this->end + $this->interval;
	}
}

?>
