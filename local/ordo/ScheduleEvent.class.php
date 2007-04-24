<?php

$loader->requireOnce("ordo/CalendarEvent.class.php");

/**
 * ORDO extender for the calendar's Event class
 * 
 * Relationships:
 * Optional:
 * 	Parent:	Schedule (through eventgroup)
 * 	Parent: Practice (through eventgroup->room)
 * 	Parent: Provider (through eventgroup->schedule)
 */

class ScheduleEvent extends CalendarEvent{
	var $_internalName='ScheduleEvent';
	
	var $event_group_id = '';
	var $room_id = '';
	var $_key = 'event_id';

	
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function ScheduleEvent($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::CalendarEvent();
	}
	
	function get_event_group_id() {
		$db =& $this->dbHelper;
		$res = $db->execute("SELECT event_group_id FROM schedule_event WHERE event_id=".$db->quote($this->get('id')));
		if(!$res->EOF) {
			return $res->fields['event_group_id'];
		}
		return false;
	}
	
	function set_event_group_id($egid) {
		$db =& $this->dbHelper;
		$res = $db->execute("REPLACE INTO schedule_event (`event_id`,`event_group_id`) VALUES (".$db->quote($this->get('id')).",".$db->quote($egid).")");
		$this->event_group_id = $egid;
	}
	
	function get_room_id() {
		$db =& $this->dbHelper;
		$res = $db->execute("SELECT eg.room_id FROM event_group eg INNER JOIN schedule_event se ON se.event_id=eg.event_id WHERE se.event_id=".$db->quote($this->get('id')));
		if($res && !$res->EOF) {
			return $res->fields['room_id'];
		}
		return 0;
	}
	
	// Since room is attached to EventGroup, there is no set_room_id()
	
	function &getParent_Schedule() {
		$db =& $this->dbHelper;
		$eg =& Celini::newORDO('EventGroup',$this->get('event_group_id'));
		$schedule =& Celini::newORDO('Schedule',$eg->get('schedule_id'));
		return $schedule;
	}

	function &getParent_Room() {
		$room =& Celini::newORDO('Room',$this->get('room_id'));
		return $room;
	}
	
	function &getParent_Provider() {
		$db =& $this->dbHelper;
		$res = $db->execute("
			SELECT
				provider_id
			FROM
				schedule
				INNER JOIN event_group eg ON eg.schedule_id=schedule_id
			WHERE
				eg.event_id=".$db->quote($this->get('id'))
		);
		if($res && !$res->EOF) {
			$provider =& Celini::newORDO('Provider',$res->fields['provider_id']);
		} else {
			$provider =& Celini::newORDO('Provider');
		}
		return $provider;
	}

	function &getParent_Practice() {
		$db =& $this->dbHelper;
		$res = $db->execute("
			SELECT
				practice_id
			FROM
				schedule_event se
				INNER JOIN event_group eg ON eg.event_group_id=se.event_group_id
				INNER JOIN rooms ON rooms.id=eg.room_id
				INNER JOIN buildings ON buildings.id=rooms.building_id
			WHERE
				se.event_id=".$db->quote($this->get('id')));
		if($res && !$res->EOF) {
			$practice =& Celini::newORDO('Practice',$res->fields['practice_id']);
		} else {
			$practice =& Celini::newORDO('Practice');
		}
		return $practice;
	}
}


