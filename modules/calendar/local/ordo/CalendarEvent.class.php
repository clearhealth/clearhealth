<?php

class CalendarEvent extends ORDataObject
{
	/**#@+
	 * Property of Event
	 *
	 * @access protected
	 */
	var $event_id = '';
	var $title = '';
	var $start = '';
	var $end = '';
	/**#@-*/
	
	/**#@+
	 * {@inheritdoc}
	 */
	var $_key = 'event_id';
	var $_table = 'event';
	var $_internalName='CalendarEvent';

	function CalendarEvent() {
		parent::ORDataObject();
	}
	/**#@-*/

	function persist() {
		//$date = $this->start->getDate();
		//$view = new clniView();
		parent::persist();
		//$view->clear_cache(null,$date->toISO());
	}
	
	function drop() {
		//$date = $this->start->getDate();
		//$view = new clniView();
		parent::drop();
		// Takes care of removing individual schedule events
		//$view->clear_cache(null,$date->toISO());
	}

	/**#@+
	 * Timestamp accessor/mutator
	 *
	 * @access protected
	 */
	function get_start() {
		return $this->_getTimestamp('start');
	}
	
	function get_end() {
		return $this->_getTimestamp('end');
	}
	
	function set_start($value) {
		$this->_setDate('start', $value);
	}
	function set_start_time($value) {
		$this->set('start', date('Y-m-d ',strtotime($this->get('date'))) .$value);
	}
	
	function set_end($value) {
		$this->_setDate('end', $value);
	}
	function set_end_time($value) {
		$this->set('end', date('Y-m-d ',strtotime($this->get('date'))).$value);
	}

	function get_date() {
		return $this->_getDate('start');
	}

	function get_start_time() {
		return $this->_getTime('start');
	}

	function get_end_time() {
		return $this->_getTime('end');
	}
	
	function get_name(){
		return $this->get('title');
	}
	
	function set_name($name) {
		$this->set('title',$name);
	}

	function get_start_ts() {
		return strtotime($this->get('start'));
	}

	function get_end_ts() {
		return strtotime($this->get('start'));
	}

	function get_schedule_id() {
		$s =& $this->getParent('CalendarSchedule');;
		var_dump($s->toString());
		return $s->get('id');
	}
	/**#@-*/

	function get_events_between($start, $end){
		$sql = "SELECT *, UNIX_TIMESTAMP(start) as start_ts, UNIX_TIMESTAMP(end) as end_ts  FROM {$this->_table} AS e WHERE e.start BETWEEN '$start' AND '$end'";
		$result = $this->_Execute($sql);
		return $result->GetArray();
	}

	/**
	 * Returns the duration of this object from start to finish
	 *
	 * @return	int (total minutes of event)
	 * @access	protected
	 */
	function get_duration() {
		return (strtotime($this->end) - strtotime($this->start)) / 60;
	}

	function get_delete_message() {
		$message = "Deleting event #".$this->_db->qstr($this->id);
		return $message;
	}

	/**
	 * Returns a RelationshipCollection
	 *
	 * @param int $schedule_id
	 * @return RelationshipCollection
	 */
	function &getEventsForSchedule($schedule_id){
		$event=&Celini::newORDO('Schedule',$schedule_id);
		$ocs=&$event->getChildren('Event');
		return $ocs;
	}
}
