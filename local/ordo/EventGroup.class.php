<?php

/**
 * 
 * Relationships:
 * 	Parent:		Provider
 * 	Children: 	Event
 */

class EventGroup extends ORDataObject {
	var $event_group_id = '';
	var $title = '';
	var $room_id = '';
	var $schedule_id = '';
	
	var $_key = 'event_group_id';
	var $_table = 'event_group';
	var $_internalName='EventGroup';
	var $_schedule = null;
	
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function EventGroup($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
	}
	
	function populateSchedule() {
		if(is_null($this->_schedule)) {
			$this->_schedule =& Celini::newORDO('Schedule',$this->get('schedule_id'));
		}
	}

	/**
	 * Returns array or collection of events
	 *
	 * @return ORDOCollection|array
	 */
	function getEvents($returnArray = false,$ordoName = 'CalendarEvent',$criteria = '1'){
		$this->populateSchedule();
		$criteria .= ' AND eg.event_group_id='.$this->dbHelper->quote($this->get('id'));
		return $this->_schedule->getEvents($returnArray,$ordoName,$criteria);
	}
	
	function getFutureEvents($returnArray=false,$ordoName = 'CalendarEvent') {
		$out =& $this->getEvents($returnArray,$ordoName,"DATE_FORMAT(event.start,'%Y-%m-%d') >= DATE_FORMAT(NOW(),'%Y-%m-%d')");
		return $out;
	}

	/**
	 * Returns array or collection of recurrence ORDOs
	 *
	 * @return ORDOCollection|array
	 */
	function &getRecurrences($returnArray = false){
		$event =& Celini::newORDO('Recurrence');
		$finder =& $this->getChildrenFinder($event);
		$finder->setOrderBy('recurrence.start_date ASC, recurrence.start_time ASC');
		$events =& $finder->find();
		if($returnArray == true) {
			$events = $events->toArray();
		}
		return $events;
	}
	
	function &getByName($name) {
		$db =& Celini::dbInstance();
		$sql = "SELECT $this->_key FROM $this->_table WHERE title = ".$db->quote($name);
		$res = $db->execute($sql);
		if($res && !$res->EOF) {
			$eg =& Celini::newORDO('EventGroup',$res->fields[$this->_key]);
			return $eg;
		}
		$eg =& Celini::newORDO('EventGroup');
		return $eg;
	}

	
}


