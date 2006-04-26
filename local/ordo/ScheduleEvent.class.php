<?php

$loader->requireOnce("ordo/CalendarEvent.class.php");

/**
 * ORDO extender for the calendar's Event class
 * 
 * Relationships:
 * Optional:
 * 	Parent:	Schedule
 * 	Parent: Practice
 * 	Parent: Provider
 * 	Parent: Room
 */

class ScheduleEvent extends CalendarEvent{
	var $_internalName='ScheduleEvent';
	
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function ScheduleEvent($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::CalendarEvent();
	}
	
	
}


