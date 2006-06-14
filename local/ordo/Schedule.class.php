<?php

$GLOBALS['loader']->requireOnce("ordo/CalendarSchedule.class.php");

/**
 * ORDO extender for the calendar's Schedule class
 * 
 * Relationships:
 * Optional:
 * 	Parent: Practice
 * 	Parent: Provider
 * 	Parent: Room
 * 	Children:	Event
 */
 
class Schedule extends CalendarSchedule{
	
	/**
	 *	
	 *	@var schedule_code
	 */
	var $schedule_code = '';
	
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
				INNER JOIN relationship AS provider ON(
					provider.parent_type = 'Provider' AND
					provider.parent_id = {$qProviderId} AND
					provider.child_id = s.schedule_id
				)";
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

	function set_provider_id($id){
		if($this->get('id') < 1) {
			$this->persist();
		}

		if ($this->get('provider_id') > 0) {
			$this->removeRelationship('Provider',$this->get('provider_id'));
		}

		$provider =& Celini::newORDO('Provider',$id);
		$this->setParent($provider);
	}
		
	function get_provider_id(){
		$provider =& $this->getParent('Provider');
		return $provider->get('id');
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
		$children = $user->getChildren('Schedule');
		$children = $children->toArray();
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
			/*
				$oc =& Celini::newORDO('ScheduleEvent',$ocid);
				$eg->setChild($oc);
				$oc->set('title',$eg->get('title'));
				$oc->persist();
				$this->setChild($oc);
			*/
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
		$sevent =& Celini::newORDO('ScheduleEvent');
		$finder =& $sevent->relationshipFinder();
		if($provider_id > 0)
			$finder->addParent($provider);
		if($room_id > 0) {
			$room =& Celini::newORDO('Room',$room_id);
			$finder->addParent($room);
		}
		if(!is_null($schedule_code) && !empty($schedule_code)) {
			$finder->_joins .=" LEFT JOIN relationship ES ON ES.parent_type='Schedule' AND ES.child_type='ScheduleEvent' AND ES.child_id = event.event_id ";
			$finder->_joins .=" JOIN schedule ON schedule.schedule_id = ES.parent_id AND schedule.schedule_code = ".$db->quote($schedule_code);
		}
		$finder->_orderBy = 'event.start';
		$finder->addCriteria('UNIX_TIMESTAMP(event.start) >= '.strtotime($start).' AND UNIX_TIMESTAMP(event.start) <= '.strtotime($end));
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

} // end of Class

?>
