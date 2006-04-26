<?php

/**
 * 
 * Relationships:
 * 	Parent:		Provider
 * 	Parent:		Schedule
 * 	Children: 	Event
 */

class EventGroup extends ORDataObject {
	var $event_group_id = '';
	var $title = '';
	
	var $_key = 'event_group_id';
	var $_table = 'event_group';
	var $_internalName='EventGroup';
	
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function EventGroup($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
	}
	
	/**
	 * Returns array or collection of events
	 *
	 * @return ORDOCollection|array
	 */
	function &getEvents($returnArray = false,$ordoName = 'CalendarEvent'){
		$event =& Celini::newORDO($ordoName);
		$finder =& $this->getChildrenFinder($event);
		$finder->setOrderBy('event.start ASC');
		$events =& $finder->find();
		if($returnArray == true) {
			$events = $events->toArray();
		}
		return $events;
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


