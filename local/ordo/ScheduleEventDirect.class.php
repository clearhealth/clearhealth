<?php
/**
 * Object Relational Persistence Mapping Class for table: schedule_event
 *
 * @package	com.clear-health.celini
 * @author	Uversa Inc.
 */
class ScheduleEventDirect extends ORDataObject {

	/**#@+
	 * Fields of table: schedule_event mapped to class members
	 */
	var $event_id		= '';
	var $event_group_id		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'schedule_event';

	/**
	 * Primary Key
	 */
	var $_key = 'event_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'ScheduleEvent';

	/**
	 * Handle instantiation
	 */
	function ScheduleEventDirect() {
		parent::ORDataObject();
	}

	
}
?>
