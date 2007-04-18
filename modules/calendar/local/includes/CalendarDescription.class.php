<?php
$loader->requireOnce('includes/CalendarData.class.php');
$loader->requireOnce('includes/CalendarDescriptionDay.class.php');
$loader->requireOnce('includes/CalendarDescriptionWeek.class.php');
$loader->requireOnce('includes/CalendarDescriptionMonth.class.php');
$loader->requireOnce('includes/CalendarDescriptionInterval.class.php');

/**
 * Describes a calendar view
 *
 * Includes
 * Start Day, End Day
 * Start Time, End Time of each day
 * Intervals used for scheduling within that day
 *
 * Works as a composite iterator
 */
class CalendarDescription {

	var $startDate;
	var $endDate = false;
	var $mode	= 'day';

	var $events = false;
	var $schedules = false;

	var $eventRenderer;
	
	var $calendarData = null;

	function CalendarDescription($mode) {
		$this->mode = $mode;
	}

	function setStartDate($year,$month,$day) {
		$this->startDate = array($year,$month,$day);
		if ($this->endDate === false) {
			$this->endDate = $this->startDate;
		}
	}

	function setEndDate($year,$month,$day) {
		$this->endDate = array($year,$month,$day);
	}

	function hasWeeks() {
		if($this->mode == 'month'){
			return true;
		}else{
			return false;
		}
	}

	function hasDays() {
		if($this->mode == 'week'){
			return true;
		}else{
			return false;
		}
	}

	function setupFilters(&$data,$startDateIn,$endDateIn) {
		$startDate = strtotime($startDateIn);
		$endDate = strtotime($endDateIn);
		$filters =& $data->getFilters();
		$filters['start']->setValue(date('Y-m-d',$startDate));
		$filters['end']->setValue(date('Y-m-d',$endDate));
		if(is_null($filters['starttime']->getValue())) {

			if (strstr($startDateIn,':')) {
				$h = date('h',$startDate);
				$m = date('i',$startDate);
				$s = date('s',$startDate);
				$ap = date('A',$startDate);
			}
			else {
				$h = '01';$m = '01'; $s = '01'; $startap = 'AM';
			}
			$filters['starttime']->setValue(array('hour'=>$h,'minute'=>$m,'second'=>$s,'ap'=>$ap));
			$start = date('H:i:s',strtotime("$h:$m:$s $ap",$startDate));

			if (strstr($endDateIn,':')) {
				$h = date('h',$endDate);
				$m = date('i',$endDate);
				$s = date('s',$endDate);
				$ap = date('A',$endDate);
			}
			else {
				$h = 12;$m = '59'; $s = '59'; $endap = 'PM';
			}
			$filters['endtime']->setValue(array('hour'=>$h,'minute'=>$m,'second'=>$s,'ap'=>$ap));
			$end = date('H:i:s',strtotime("$h:$m:$s $ap",$endDate));
		} else {
			extract($filters['starttime']->getValue());
			$start = date('h:i:s A',strtotime("$hour:$minute:$second $ap"));
			extract($filters['endtime']->getValue());
			$end = date('h:i:s A',strtotime("$hour:$minute:$second $ap"));
		}	
		return array($start,$end);
	}

	// returns the top most iterator
	function getIterator() {
		$this->_eventSetup();
		if ($this->hasWeeks()) {
			$it = new CalendarDescriptionMonth($this->startDate);
			$it->setParent($this);
			$it->setCalendarDescription($this);
		}
		else if ($this->hasDays()) {
			$it = new CalendarDescriptionWeek($this->startDate);
			$it->setParent($this);
			$it->setCalendarDescription($this);
		}
		else {
			$it = new CalendarDescriptionDay($this->startDate);
			$it->setParent($this);
		}
		return $it;
	}

	// Event setup
	function _eventSetup() {
		$data =& CalendarData::getInstance();

		// use the eventScheduleMap to clear out this list
		$this->schedules = $data->getSchedules($data->getFilters());
		$this->scheduleList = $data->getScheduleList($data->getFilters(),'default');
		$this->eventScheduleMap = $data->getEventScheduleMap();

		if (!$this->hasSchedules() || $this->mode != 'day') {
			if($data->data_handler->showEventsOn[$this->mode] == true) {
				$this->events =& $data->getEvents();
				$this->_prepareEventRender();
			}
		}

		$this->calendarData =& $data;
	}

	function _prepareEventRender(){
		$event_ids = array();
		$events =& $this->events;
		if($events != null) {
		for($events->rewind(); $events->valid(); $events->next()) {
			$event =& $events->current();
			$event_ids[] = $event->get('event_id');
		}
		}
		$this->eventRenderer->prepare($event_ids);
	}
	
	function render(&$event) {
		return $this->eventRenderer->render($event,$this->mode);
	}

	function getLabel() {
		return date('D, m/d/Y',strtotime($this->startDate[0].'/'.$this->startDate[1].'/'.$this->startDate[2]));
	}

	function currentDay() {
		return $this->startDate[0].'-'.$this->startDate[1].'-'.$this->startDate[2];
	}

	function currentWeek() {
		return $this->startDate[0].'-'.$this->startDate[1].'-'.$this->startDate[2];
	}

	function currentMonth() {
		return $this->startDate[0].'-'.$this->startDate[1].'-01';
	}

	function hasSchedules() {
		if (count($this->schedules) > 0) {
			return true;
		}
		return false;
	}

	function getSchedules() {
		return $this->schedules;
	}
	function getScheduleList() {
		return $this->scheduleList;
	}

	function getHeaderColspan() {
		//return $this->calendarData->getHeaderColspan();
		return count($this->scheduleList)*3+2;
	}

}

?>
