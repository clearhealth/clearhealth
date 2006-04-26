<?php

class C_Schedule extends Controller
{
	var $schedule = null;
	var $practice = null;
	var $provider = null;
	var $room = null;

	/**
	 * Generic process handling, saves an {@link Schedule}
	 *
	 * {@inheritdoc}
	 */
	function process() {
		$rawPost = $this->POST->getRaw('Schedule');
		$schedule =& Celini::newORDO('Schedule', (int)$rawPost['id']);
		$schedule->populate_array($rawPost);
		$schedule->persist();
	}

	function actionList() {
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		if($this->GET->getTyped('delete_schedule_id','int') > 0) {
			$schedule =& Celini::newORDO('Schedule', $this->GET->getTyped('delete_schedule_id', 'int'));
			if($schedule->get('id') > 0) {
				$schedule->drop();
				$this->messages->addMessage('Schedule Deleted');
			}
		}
		$finder =& new ORDOFinder('Schedule',"schedule.schedule_code != ''");
		$schedules = $finder->find();
		$schedules = $schedules->toArray();
		$this->view->assign('schedules',$schedules);
		$c =& Celini::newORDO('Schedule');
		return $this->view->render("schedules.html");
	}


	/**
	 * If an event exists during the time of the event fed to this function,
	 * the event(s) will be returned in an array.
	 * 
	 * $matches array should look like this:
	 * $matches[]=array('child','User',$user->get('id'));
	 *
	 * @param ORDataObject Event $oc
	 * @param array of ORDOs to be related $matches
	 * @return ORDOCollection
	 */
	function _checkAvailability(&$oc, $matches) {
		$start = $oc->get('start');
		$end   = $oc->get('end');
		$criteria="event.start BETWEEN ".$db->quote($start)." AND ".$db->quote($end)." OR event.end BETWEEN ".$db->quote($start)." AND ".$db->quote($end);
		$ocs=&$this->getRelated($matches,'CalendarEvent',null,null,$criteria);
		
		$startObj = TimestampObject::create($start);
		$sdate = $startObj->toString('%Y-%m-%d %H:%i:00');
		$endObj =& TimestampObject::create($end);
		$edate  = $endObj->toString('%Y-%m-%d %H:%i:00');

		if($ocs !== false){
			$this->assign("availability_message", "It does not appear that that resource is available during $sdate - $edate.");
			return false;
	}
		return $ocs;
	}

	/**
	 * Handle adding an {@link Schedule}
	 */
	function actionAdd() {
		$this->view->assign('addMode', true);
		return $this->actionEdit();
	}
	
		
	/**
	 * Handle editing an {@link Schedule}
	 */
	function actionEdit() {
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		if(is_null($this->schedule)){
			$schedule =& Celini::newORDO('Schedule', $this->GET->getTyped('schedule_id', 'int'));
		} else {
			$schedule =& $this->schedule;
		}
		$this->view->assign_by_ref('schedule', $schedule);
		$practice=&$schedule->getParent('Practice');

		$this->view->assign_by_ref('practice',$practice);
		$provider=&$schedule->getParent('Provider');

		$this->view->assign_by_ref('provider',$provider);
		$room=&$schedule->getParent('Room');

		$this->view->assign_by_ref('room',$room);

		$events = array();
		if($schedule->get('id') > 0) {
			$events = $schedule->getChildren('ScheduleEvent');
			$events = $events->toArray();
		}
		$this->view->assign_by_ref('events',$events);
		
		$room =& Celini::newORDO('Room');
		$pa = $practice->practices_factory();
		$this->view->assign("rooms_practice_array",$room->rooms_practice_factory($pa,false));
		$em =& Celini::enumManagerInstance();
		$this->view->assign_by_ref('em',$em);
		
		$recurrence =& Celini::newORDO('Recurrence',
		$this->GET->getTyped('recurrence_id', 'int'));
		$this->view->assign_by_ref('recur',$recurrence);
		
		$event =& Celini::newORDO('ScheduleEvent',
		$this->GET->getTyped('event_id', 'int'));
		$this->view->assign_by_ref('event',$event);
		
		$eventGroup =& Celini::newORDO('EventGroup', $this->getDefault('event_group_id', 0));
		$this->view->assign_by_ref('eventGroup',$eventGroup);

		if($schedule->get('id') > 0) {
			$eventGroups =& $schedule->getChildren('EventGroup');
			$this->view->assign_by_ref('eventGroups',$eventGroups);
		}
		
		return $this->view->render('edit.html');
		}
		
	function _processEvent() {
		$eventarray = $this->POST->getRaw('Event');
		if(!empty($eventarray['title'])){
			$event =& Celini::newORDO('ScheduleEvent',$eventarray['id']);
			$event->populate_array($eventarray);
			$event->persist();
			$this->schedule->setChild($event);
			$this->provider->setChild($event);
			$this->practice->setChild($event);
			$this->room->setChild($event);
			if($eventarray['id'] > 0){
				$this->messages->addMessage('Event Updated');
			} else {
				$this->messages->addMessage('Event Added');
	}
		}
	}
	
	function _processEventGroup() {
		$egroup = $this->POST->getRaw('EventGroup');
		if (!empty($egroup['title'])) {
			$eventgroup =& Celini::newORDO('EventGroup');
			$eventgroup->populate_array($egroup);
			$eventgroup->persist();
			$this->schedule->setChild($eventgroup);
		}
	}
		
	function _deleteEvents($events){
		foreach($events as $eventid){
			$event =& Celini::newORDO('ScheduleEvent',$eventid);
			$event->drop();
		}
		$this->messages->addMessage('Calendar Items Removed');
	}
	
	function _deleteRecurrences($recurs){
		foreach($recurs as $recurid){
			$recurrence =& Celini::newORDO('Recurrence',$recurid);
			$ocs=$recurrence->getChildren('ScheduleEvent');
			while($oc=&$ocs->current() && $ocs->valid()){
				$oc->drop();
				$oc=&$ocs->next();
			}
			
			$recurrence->drop();
		}
		$this->messages->addMessage('Calendar Items and Recurrence Removed');
	}

	function processAdd() {
		$this->processEdit();
	}
	
	function processEdit() {
		if ($this->POST->get('process') != "true")
			return;
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		$schedule =& Celini::newORDO('Schedule');
		$schedule->populate_array($this->POST->getRaw('Schedule'));
		$schedule->persist();
		$this->schedule =& $schedule;
		$this->practice =& $schedule->getParent('Practice');
		$this->provider =& $schedule->getParent('Provider');
		
		$this->room =& $schedule->getParent('Room');
		$this->_processEventGroup();
		$this->_processRecurrence();
		$this->_processDeletes();
		$this->_processEvent();
		$this->messages->addMessage('Schedule Updated');
	}
	
	function _processDeletes(){
		if(isset($_POST['DeleteEvent'])){
			$this->_deleteEvents($this->POST->getRaw('DeleteEvent'));
		}
		if(isset($_POST['DeleteRecurrence'])){
			$this->_deleteRecurrences($this->POST->getRaw('DeleteRecurrence'));
		}

		}
	
	function _processRecurrence(){
		if(isset($_POST['Recurrence']) && !empty($_POST['RecurrencePattern']['pattern_type'])){
			$rec =& $this->schedule->createRecurrence($this->POST->getRaw('Recurrence'),$this->POST->getRaw('RecurrencePattern'));
			if($rec !== false) {
				$eg =& $rec->getParent('EventGroup');
				$events =& $rec->getChildren('ScheduleEvent');
				while($event =& $events->current() && $events->valid()){
					$event->set('title',$eg->get('title'));
					$event->setParent($this->schedule);
					$event->setParent($this->provider);
					$event->setParent($this->practice);
					$event->setParent($this->room);
					$event->setParent($eg);
					$events->next();
				}
				$this->messages->addMessage('Recurrence Set and Populated.');
			} else {
				$this->messages->addMessage('There was a problem with the data you entered for recurrence.  Please try again.');
			}
		}
	}
	
	function edit_event_action($id = "", $fid= "") {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","event",$this,false);
		$this->assign("edit_event",new Event($id));
		
		return $this->edit_schedule_action($fid);
	}
	
	function edit_timeplace_action($id = "", $fid= "") {
		
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","occurence",$this,false);
		$this->assign("edit_timeplace",new Occurence($id));
		
		return $this->edit_schedule_action($fid);
	}
	
	function edit_event_action_process($schedule_id = "") {
		if ($_POST['process'] != "true")
			return;
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","event",$this,false);
		$this->location = new Event($_POST['id']);
		$this->location->populate_array($_POST);
		
		$this->location->persist();
		
		$this->location->populate();
		$_POST['process'] = "";
		$this->_state = false;
		$this->location = null;
		header("Location: ".Celini::link("edit_schedule").$_SERVER['QUERY_STRING']);
		return $this->edit_schedule_action($schedule_id);
	}
	
	function edit_appointment_action_process($event_id = "",$confirm =false) {
		if (!isset($_POST['occurence_id'])) {
			$_POST['occurence_id'] = 0;
		}
		$oc = new Occurence($_POST['occurence_id']);
		if($this->_me->get_user_id() == $oc->get_user_id()) {
			$this->sec_obj->acl_qcheck("edit",$this->_me,"","event",$this,false);
		}
		else {
			$this->sec_obj->acl_qcheck("edit_owner",$this->_me,"","event",$this,false);
		}
		
		$_POST['process'] = "";
		$this->_state = false;
		
		if (is_array($event_id)) {
			$this->event = new Event();
			$this->event->populate_array($event_id);
		}
		else {
			$this->event = new Event($event_id,false);
			$this->event->populate_array($_POST);
		}
			
		$users = array();
		if (isset($_POST['users']) && count($_POST['users']) > 0) {
			$tmp =  $_POST['users'];
			$_POST['user_id'] = array_shift($tmp);
			$users = $_POST['users'];
		}
		
		$oc->populate_array($_POST);
		
		$cs = new C_Schedule();
		//check for availability of provider
		if (is_numeric($oc->get_user_id())) {
			$availability = $cs->check_availability($oc, $this->event);
			if ($availability) {
				//echo "this event is within providers schedule<br>";	
		}
			else {
				//echo "this event is NOT within providers schedule<br>";
			}
		}
				
		// get a Appointment template from the reason
		$manager =& EnumManager::getInstance();
		$list =& $manager->enumList('appointment_reasons');
		$reason = false;
		for($list->rewind();$list->valid();$list->next()) {
			$row = $list->current();
			if ($row->key == $this->POST->get('reason_id')) {
				$reason = $row;
			}
		}
		if ($reason && $reason->extra1 !== '') {
			$template = Celini::newOrdo('AppointmentTemplate',$reason->extra1);
		}
		else {
			$template = Celini::newOrdo('AppointmentTemplate');
		}
		
		// check for the walkin flag
		$walkin = 0;
		if (isset($_POST['walkin'])) {
			$walkin = $_POST['walkin'];
		}
		
		//check for double book
		$double = false;
		if (is_numeric($oc->get_user_id()) && $_POST['occurence_id'] < 1 && $walkin != 1) {
			$double = $cs->check_double_book($oc,$this->event,$template->breakdownSum($users));
			if ($double) {
				if(!$this->sec_obj->acl_qcheck("double_book",$this->_me,"","event",$this,true)) {
					echo "The event you are trying to add collides with another event. You do not have permission to double book events. You can use the back button of your browser to alter the event so that it does not collide and try again.";
					exit;
		}
				//echo "this event is double booking<br>";	
			}
		else {
				//echo "this event is NOT double booking<br>";
		}
	}
	
		if (!($availability && !$double) && !$confirm) {
			return $cs->confirm_action($_POST);	
		}
	
		$this->event->persist();
		$this->event->populate();
		
		$oc->set_event_id($this->event->get_id());
		if (isset($_POST['walkin'])) {
			$oc->set('walkin',$_POST['walkin']);
	}
		if (isset($_POST['group_appointment'])) {
			$oc->set('group_appointment',$_POST['group_appointment']);
		}
		if (isset($_POST['reason_id'])) {
			$oc->set('reason_code',$_POST['reason_id']);
		}
	
		$oc->persist();
		$oc->populate();
	
		
		if (isset($_POST['users'])) {
			$template->fillTemplate($oc->get('id'),$_POST['users']);
			}
		else {
			$template->resetTemplate($oc->get('id'));
		}
		
				
		$this->location = null;

		$trail =& Celini::trailInstance();
		$trail->skipActions = array('edit_appointment','confirm','find','appointment_popup');
		$action = $trail->lastItem();
		header("Location: " . $action->link());
		exit;
	}
			
	
	function edit_occurence_action_process($schedule_id = "") {
		if ($_POST['process'] != "true")
			return;
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","occurence",$this,false);
		$id = "";
		if (isset($_POST['id'])) $id =$_POST['id'];
		$this->location = new Occurence($id);
		$this->location->populate_array($_POST);
		$this->location->persist();
		
		$this->location->populate($this->location->get('id'));
		$_POST['process'] = "";
		$this->_state = false;
		$this->location = null;
		header("Location: " . Celini::link("edit_schedule") . "id=" . $_POST['schedule_id']);
		return;
				}				
	function update_schedule_action_process($id = "",$schedule_code ="",$group_name ="") {
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","occurence",$this,false);
	
		if ($_POST['process'] != "true") {
			return;
		}

		if(empty($id) && empty($object_class)) {
			$id = $_POST['id'];	
		}
		
		if (is_numeric($id) && !empty($schedule_code) && !empty($group_name)) {
			
			$oc = new Occurence($id);
			$s = new Schedule();
			$sa = $s->schedules_factory();
			
			foreach ($sa as $schedule) {
				if ($schedule->get_schedule_code() == $schedule_code) {
					$ea = $schedule->get_events();
					foreach ($ea as $event) {
						if ($event->get_title() == urldecode($group_name)) {
							$oc->set_event_id($event->get_id());
							$oc->persist();
				break;
			}
		}
					break;	
				}	
			}	

		}
		
		$location = Celini::link('list','location');
		$trail =& Celini::trailInstance();
		$trail->skipActions = array('update_schedule');
		$action = $trail->lastItem();
		header("Location: ".$action->link());
		exit;
	}
		


}

