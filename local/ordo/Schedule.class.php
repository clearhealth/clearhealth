<?php

$GLOBALS['loader']->requireOnce("ordo/CalendarSchedule.class.php");

/**
 * ORDO extender for the calendar's Schedule class
 * 
 * Relationships:
 * Optional:
 * 	Children:	Event
 */
 
class Schedule extends CalendarSchedule{
	
	/**
	 *	
	 *	@var schedule_code
	 */
	var $schedule_code = '';
	var $provider_id = '';
	
	var $_internalName='Schedule';
	var $_foreignKeyList = array('practice_id' => 'Practice',
							'provider_id' => 'Provider',
							'room_id' => 'Room');
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function Schedule($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
	}
	
	function setupByProvider($providerId) {
		$tableName = $this->tableName();
		$qProviderId = $this->dbHelper->quote($providerId);
		$sql = "
			SELECT 
				*
			FROM
				{$tableName} AS s
			WHERE
				s.provider_id = {$qProviderId}
			";
		$this->helper->populateFromQuery($this, $sql);
	}
	
	
	/**
	 * Setup a {@link Schedule} from a {@link Room} id.
	 *
	 * @param  int
	 * @access protected
	 */
	function setupByRoomId($roomId) {
		$tableNae = $this->tableName();
		$qRoomId = $this->dbHelper->quote($roomId);
		$sql = "SELECT * FROM {$tableName} AS s WHERE s.room_id = {$qRoomId}";
		$this->helper->populateFromQuery($this, $sql);
	}

	function get_events() {
		$events=$this->getChildren('ScheduleEvent');
		return $events;	
	}

	function get_delete_message() {
		$string = "Schedule Name: " . $this->get('schedule_code') . "-" . $this->get('name') . "\n";
		$evs = $this->get_events();
		while($ev=&$evs->current() && $evs->valid()){
			$string .= $ev->get_delete_message();
			$evs->next();
	}
		return $string;
	}

	function drop(){
		$events = $this->getChildren('ScheduleEvent');
		while($event=&$events->current() && $events->valid()){
			$event->drop();
			$events->next();
		}
		parent::drop();
	}

	function get_name(){
		return $this->get('title');
	}
	
	function get_practice_id(){
		$practice =& $this->getParent('Practice');
		return $practice->get('id');
	}
	
	function set_practice_id($id){
		if($this->get('id') < 1)
			$this->persist();
		$practice =& Celini::newORDO('Practice',$id);
		$this->setParent($practice);
	}
	
	function get_room_id(){
		$room =& $this->getParent('Room');
		return $room->get('id');
	}
		
	function set_room_id($id){
		if($this->get('id') < 1)
			$this->persist();
		$room =& Celini::newORDO('Room',$id);
		$this->setParent($room);
	}

	function genericList(){
			
			$db = new clniDb();
			$sql = "select $this->_key, title from $this->_table where 1";
			$result = $db->execute($sql);
			$list = array();
		while ($result && !$result->EOF) {
				$list[$result->fields[$this->_key]] = $result->fields['title'] ;
			$result->MoveNext();
		}
			return $list;
	}

	function fromUserId($user_id) {
		$user =& Celini::newORDO('User',$user_id);
		$person_id = $user->get('person_id');
		$finder =& new ORDOFinder('Schedule','provider_id='.$user->dbHelper->quote($person_id));
		$children = $finder->find();
		return $children;
	}
	
	/**
	 * Returns array of recurrence ordos
	 *
	 * @return array
	 */
	function getRecurrences(){
		$recurs = $this->getChildren('Recurrence');
		$recurs = $recurs->toArray();
		return $recurs;
	}

	/**
	 * Creates the Recurrence, RecurrencePattern, and Events
	 *
	 * @param array $recdata
	 * @param array $rpdata
	 * @return ORDataObject|false
	*/
	function &createRecurrence($recdata,$rpdata){
		$recdata['start_date']=date('Y-m-d',strtotime($recdata['start_date']));
		$recdata['end_date']=date('Y-m-d',strtotime($recdata['end_date']));
		$rec=&Celini::newORDO('Recurrence');
		$rec->populate_array($recdata);
		$rec->persist();
		$this->setChild($rec);
		$rp=&$rec->createPattern($rpdata,'ScheduleEvent');
		if(!$rp) {
			$rec->drop();
			$return = false;
			return $return;
		}
		$eg =& $rec->getParent('EventGroup');
		$ocs=$rec->createEvents($rp,'ScheduleEvent');
		foreach($ocs as $ocid){
			if($eg->get('id') > 0) {
				$qEventGroupId = $this->dbHelper->quote($eg->get('id'));
				$qEventGroupTitle = $this->dbHelper->quote($eg->get('title'));
				$qOccurenceId = $this->dbHelper->quote($ocid);
				
				$sql = "
					INSERT INTO relationship 
						(`parent_type`,`parent_id`,`child_type`,`child_id`)
					VALUES 
						('EventGroup', {$qEventGroupId}, 'ScheduleEvent', {$qOccurenceId})";
				$this->dbHelper->execute($sql);
				
				$sql = "
					UPDATE 
						event
					SET
						`title`= {$qEventGroupTitle}
					WHERE 
						event_id = {$qOccurenceId} LIMIT 1";
				$this->dbHelper->execute($sql);
			}
		}
		return $rec;
	}

	/**
	 * Returns date and begin/end timestamps of the first
	 * empty timeslot in a provider's schedule
	 *
	 * @param int $provider_id
	 * @param int|null $room_id
	 * @param ISO DateTime $start
	 * @param ISO DateTime $end
	 * @param int $amount amount of time in seconds required
	 * @param string|null $schedule_code
	 * @return int|false timestamp of start of block
	 */
	function findFirst($provider_id,$room_id = 0,$start,$end,$amount,$schedule_code = null) {
		$db =& Celini::dbInstance();
		$provider =& Celini::newORDO('Provider',$provider_id);
		$finder =& new ORDOFinder('ScheduleEvent','');
		$finder->_joins = ' INNER JOIN schedule_event ON event.event_id=schedule_event.event_id INNER JOIN event_group ON event_group.event_group_id=schedule_event.event_group_id INNER JOIN schedule ON schedule.schedule_id=event_group.schedule_id';
		if(empty($finder->_criteria)) {
			$finder->_criteria = '1';
		}
		if($provider_id > 0) {
			$finder->_criteria .= " AND schedule.provider_id=".$db->quote($provider_id);
		}
		if($room_id > 0) {
			$finder->_criteria .= " AND event_group.room_id=".$db->quote($room_id);
		}
		if(!is_null($schedule_code) && !empty($schedule_code)) {
			$finder->_criteria .= " AND schedule.schedule_code=".$db->quote($schedule_code);
		}
		$finder->_orderBy = 'ORDER BY event.start ASC, event.end ASC';
		$finder->_criteria .= ' AND UNIX_TIMESTAMP(event.start) >= '.strtotime($start).' AND UNIX_TIMESTAMP(event.start) <= '.$db->quote(strtotime($end));
		$schedules =& $finder->find();

		$event =& Celini::newORDO('CalendarEvent');
		for($schedules->rewind();$schedules->valid();$schedules->next()) {
			$sched =& $schedules->current();
			$start = strtotime($sched->get('start'));
			$end = strtotime($sched->get('end'));
			$finder =& $event->relationshipFinder();
			$finder->_orderBy = 'event.start';
			$finder->_joins .= ' LEFT JOIN appointment ON appointment.event_id = event.event_id,appointment_breakdown ab';
			$finder->addCriteria('UNIX_TIMESTAMP(event.start) >= '.$start.' AND UNIX_TIMESTAMP(event.start) < '.$end.' AND (appointment.provider_id ='.$db->quote($provider_id)." OR (ab.appointment_id=appointment.appointment_id AND ab.person_id=".$db->quote($provider_id)."))");
			$events =& $finder->find();
			if($events->count() == 0) {
				if($end - $start >= $amount) {
					return $start;
				} else {
					continue;
				}
			}
			for($events->rewind();$events->valid();$events->next()) {
				$event =& $events->current();
				if(!isset($evend)) $evend = $start;
				$evstart = strtotime($event->get('start'));
				if($evstart - $evend >= $amount) {
					return $evend;
				}
				if($events->key()+1 == $events->count()) {
					if($end - strtotime($event->get('end')) >= $amount) {
						return strtotime($event->get('end'));
					}
				}
				$evend = $evend > strtotime($event->get('end')) ? $evend : strtotime($event->get('end'));
			}
		}
		return false;
	}

	function get_future_events($eventType) {
		$this->getEvents(false,$eventType,"DATE_FORMAT(event.start,'%Y-%m-%d' >= '".date('Y-m-d')."'");
		return $finder->find();
	}

	function getEvents($returnArray = false,$eventType='CalendarEvent',$criteria=false){
		if($criteria !== false) {
			$criteria .= ' AND ';
		} else {
			$criteria = '';
		}
		$criteria .= "eg.schedule_id=".$this->dbHelper->quote($this->get('id'));
		$joins = 'INNER JOIN schedule_event se ON se.event_id = event.event_id INNER JOIN event_group eg ON se.event_group_id = eg.event_group_id';
		$finder =& new ORDOFinder($eventType,$criteria,'',null,$joins);
		$events =& $finder->find();
		if($returnArray == true) {
			$events = $events->toArray();
		}
		return $events;
	}
	
	function &getChildren_EventGroup() {
		$db =& $this->dbHelper;
		$finder =& new ORDOFinder('EventGroup','event_group.schedule_id='.$db->quote($this->get('id')));
		$events = $finder->find();
		return $events;
	}

	/**
	 * Creates an EventGroup based on name, schedule, and room,
	 * but does not return a persisted ORDO (in case you just need one for reference)
	 *
	 * @param string $name
	 * @param int $roomid
	 * @return ORDataObject
	 */
	function &getEventGroupByNameAndRoom($name,$roomid) {
		$db =& $this->dbHelper;
		$sql = "SELECT event_group_id FROM event_group WHERE schedule_id = ".$db->quote($this->get('id'))." AND title = ".$db->quote($name)." AND room_id = ".$db->quote($roomid);
		$res = $db->execute($sql);
		if($res && !$res->EOF) {
			$eg =& Celini::newORDO('EventGroup',$res->fields['event_group_id']);
		} else {
			$eg =& Celini::newORDO('EventGroup');
			$eg->set('schedule_id',$this->get('id'));
			$eg->set('title',$name);
			$eg->set('room_id',$roomid);
		}
		return $eg;
	}

} // end of Class

?>
