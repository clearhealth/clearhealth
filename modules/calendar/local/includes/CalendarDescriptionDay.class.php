<?php

class CalendarDescriptionDay {

	var $interval = 0;
	var $start = 0;
	var $end = 0;

	var $date;
	var $dateTs;
	var $_current = false;
	var $_object = false;
	var $columns = array();

	var $parent;

	function CalendarDescriptionDay($d) {
		$this->date = $d;
		$data =& CalendarData::getInstance();
		$config =& Celini::configInstance();
		if(!$calConfig = $data->getConfig()) {
			$calConfig = $config->get('calendar');
		}
		$this->dateTs = strtotime($d[0].'-'.$d[1].'-'.$d[2]);
		$this->interval = $calConfig['increment'];
		$this->start = $this->dateTs + $calConfig['hour_start'] * (60 * 60);
		$this->end = $this->start + ($calConfig['hour_length'] * (60 * 60));
                if ($config->get('CalendarDynamicTimes') == true) {
			$this->setupDynamicTimes();
		}
	}
	function setStartTS($start) {
		$this->start =  $this->dateTs + ($start * (60 * 60));
	}
	function setEndTS($end) {
		$this->end =  $this->dateTs + ($end * (60 * 60))+3600;
	}

	function setParent(&$p) {
		$this->parent =& $p;
	}

	function getCalendarDescription(){
		return $this->parent;
	}

	function numIntervals() {
		$num = ($this->end-$this->start)/$this->interval;
		return $num;
	}

	function canvasHeight() {
		return ($this->numIntervals()*25)+47;
	}

	function timestampToPosition($timestamp) {
		return ((($timestamp-$this->start)/$this->interval)*25)+25;
	}

	function timeDifferenceToHeight($start,$end) {
		return ((($end-$start)/$this->interval)*25)-2;
	}
	function setupDynamicTimes() {
		$profile =& Celini::getCurrentUserProfile();
		$data =& CalendarData::getInstance();
                $filters = $data->getFilters();
                if(is_null($filters['building']->getValue()) || count($filters['building']->getValue()) == 0) {
			if ($profile->getDefaultLocationId()) {
                	$room =& ORDataObject::factory('Room',$profile->getDefaultLocationId());			
			}
			else {
                	$room = Room::getFirstRoom();			
			}
                        $filters['building']->setValue(array($room->get('building_id')));
                }
                $building_list = implode(",",$filters['building']->getValue());
		
                $sql = "select DATE_FORMAT(MIN(start),'%H') as start, DATE_FORMAT(MAX(end),'%H') as end from event ev left join appointment ap on ap.event_id = ev.event_id left join schedule_event se on se.event_id = ev.event_id left join rooms r on r.id = ap.room_id left join buildings b on b.id = r.building_id left join event_group eg on eg.event_group_id = se.event_group_id  left join rooms r2 on r2.id = eg.room_id left join buildings b2 on b2.id = r2.building_id
where (r.building_id 
                        IN(" .$building_list . ") OR r2.building_id 
                        IN(" .$building_list . ")) and 
                        ev.start >= '" . date('Y-m-d',$this->dateTs) . " 00:00' and 
                        ev.end <= '" . date('Y-m-d',$this->dateTs) ." 23:59:59'";
                $db = new clniDB();
                $res = $db->execute($sql);
               	if (!$res->EOF) {
                  $start = $res->fields['start'];
                  $end = $res->fields['end'];
               	  $this->setStartTS($start);
               	  $this->setEndTS($end);
               	}

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
		$this->_current += $this->interval;
		if ($this->_current <= $this->end) {
			$this->_object = new CalendarDescriptionInterval($this->_current,$this->interval);
			$this->_object->setParent($this);
			return $this->_object;
		}
	}

	/**
	 * Move to the previous Interval
	 */
	function previous() {
		$this->_current -= $this->interval;
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
	 * Is this the first interval in the day
	 */
	function isFirst() {
		if ($this->_current == $this->start) {
			return true;
		}
		return false;
	}

	/**
	 * Is this the last interval in the day
	 */
	function isLast() {
		if ($this->_current == $this->end) {
			return true;
		}
		return false;
	}

	function getTime() {
		return date('h:i A',$this->_current);
	}
	
	function getTimestamp() {
		return $this->_current;
	}

	function getTimeSpan() {
		$intMin = ($this->interval/60);
		switch($intMin){
			case 1:
			case 2:
				return 5;
			case 15:
				return 4;
			case 5:
			case 10:
			case 20:
				return 3;
			case 30:
				return 2;
			case 60:
				return 1;
			default:
				return 1;
		}
	}

	function needTimeLabel() {
		$min = (int)date('i',$this->_current);
		$interval = $this->interval/60;
		switch($this->getTimeSpan()) {
			case 1:
				return true;
			case 2:
				if($min == 0)
					return true;
				return false;
			case 3:
				if($min == 0)
					return true;
				if($interval == 10 && $min % 30 == 0)
					return true;
				if($interval == 5 && $min % 15 == 0)
					return true;
				return false;
			case 4:
				if($min == 0)
					return true;
			case 5:
				if($min == 0)
					return true;
				if($interval == 2 && $min % 10 == 0)
					return true;
				if($interval == 1 && $min % 5 == 0)
					return true;
				return false;
			default:
				return true;
		}
	}
	
	function getDayOfWeek(){
		return date('l', $this->_current);
	}
	
}

?>
