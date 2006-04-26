<?php

$GLOBALS['loader']->requireOnce("ordo/CalendarRecurrence.class.php");

/**
 * ORDO extender for the calendar's Recurrence class
 * 
 */

class Recurrence extends CalendarRecurrence {
	
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function Recurrence($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
	}
	
	function set_schedule_id($id){
		$schedule =& Celini::newORDO('Schedule',$id);
		$this->setParent($schedule);
	}
	
	function set_event_group($id) {
		$eg =& Celini::newORDO('EventGroup',$id);
		if(!$this->isPopulated())
			$this->persist();
		$this->setParent($eg);
	}
	
	function get_event_group() {
		$eg =& $this->getParent('EventGroup');
		return $eg->get('id');
	}
	
	function createEvents($rpattern,$ordoName = 'ScheduleEvent') {
		return parent::createEvents($rpattern,$ordoName);
	}
	
}

