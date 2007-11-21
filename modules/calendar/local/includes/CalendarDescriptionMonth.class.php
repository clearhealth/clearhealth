<?php

class CalendarDescriptionMonth {

	var $interval = 0;
	var $start = 0;
	var $end = 0;

	var $date;
	var $dateTs;
	var $_current = false;
	var $_object = false;

	var $parent;
	var $cd;

	function CalendarDescriptionMonth($d) {
		$this->date = $d;

		$this->interval = 60 * 60 * 24 * 7; // One Week
		$this->dateTs = strtotime("$d[0]-$d[1]-$d[2] 00:00:00");
		$this->month_start = strtotime("$d[0]-$d[1]-1  00:00:00");
		$this->month_end = strtotime("$d[0]-$d[1]-".date('t', $this->dateTs)." 00:00:00");
		
		// Figure out first day of display
		$first_day = date('w', $this->dateTs);
		if($first_day > 0){
			$this->start = strtotime($first_day." days ago", $this->dateTs);
		}else{
			$this->start = $this->dateTs;
		}
		
		//Figure out last day of display
		$last_day = date('w', $this->month_end);
		if($last_day < 6){
			$this->end = strtotime("+".(6 - $last_day)." days", $this->month_end);
		}else{
			$this->end = $this->month_end;
		}
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
		$this->_object = new CalendarDescriptionWeek(array(date('Y', $this->_current), date('m', $this->_current), date('d', $this->_current)));
		$this->_object->setParent($this);
		$this->_object->setCalendarDescription($this->cd);
	}

	/**
	 * Move to the next Interval
	 */
	function next() {
		$this->_current = strtotime("+1 week", $this->_current);
		if ($this->_current <= $this->end) {
			$this->_object = new CalendarDescriptionWeek(array(date('Y', $this->_current), date('m', $this->_current), date('d', $this->_current)));
			$this->_object->setParent($this);
			$this->_object->setCalendarDescription($this->cd);
			return $this->_object;
		}
	}

	/**
	 * Is the current interval were on valid
	 */
	function valid() {
		if ($this->_current <= $this->end && $this->_current >= $this->start) {
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
		if ($this->_current >= $this->end) {
			return true;
		}
		return false;
	}
	
	function getPrevMonth(){
		if($this->date[1] == 1){
			return strtotime($this->date[0]."-12-1");
		}else{
			return strtotime($this->date[0]."-".($this->date[1] - 1)."-1");
		}
	}

	function getNextMonth(){
		return strtotime("next day", $this->month_end);
	}

	function debugOutput(){
		print("Start: $this->start:".date('Y-m-d', $this->start).", End: $this->end:".date('Y-m-d', $this->end).", Current: $this->_current:".date('Y-m-d', $this->_current)."<BR>\n");
	}
}

?>
