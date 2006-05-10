<?php

$loader->requireOnce('includes/clni/clniData.class.php');

class ScheduleWizardData extends clniData{
	var $schedule_type = '';
}

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

	function _getWizardData() {
		$session =& Celini::sessionInstance();

		$str = $session->get('schedule:wizardData');
		if (!empty($str)) {
			return unserialize($str);
		}
		return new ScheduleWizardData();
	}
	function _setWizardData($data) {
		$str = serialize($data);

		$session =& Celini::sessionInstance();
		$session->set('schedule:wizardData',$str);
	}

	var $wizardPage = 1;
	function actionWizard() {
		$em =& Celini::enumManagerInstance();
		$this->view->assign_by_ref('em',$em);


		$wizardData = $this->_getWizardData();
		var_dump($wizardData);
	
		$provider =& Celini::newOrdo('Provider');
		$providers = $provider->valueList('usernamePersonId');

		$room =& Celini::newORDO('Room');
		$practice =& Celini::newOrdo('Practice');
		$room =& Celini::newOrdo('Room');
		$pa = $practice->practices_factory();
		$this->view->assign("rooms",$room->rooms_practice_factory($pa,false));
		$this->view->assign("providers",$providers);


		$this->view->assign_by_ref('wizard',$wizardData);
		return $this->view->render('wizard-'.$this->wizardPage.'.html');
	}

	function processWizard() {
		$session =& Celini::sessionInstance();

		$wizardData = $this->_getWizardData();
		$wizardData->populate($this->POST->get('wizard'));
		$this->_setWizardData($wizardData);

		$this->wizardPage = $this->POST->getTyped('next_page','int');

		if ($this->wizardPage == 99) {
			$this->createSchedule($wizardSchedule);
		}
	}

	function createSchedule($data) {
		$schedule =& Celini::newORDO('Schedule');

		$wizard = $this->_getWizard();

		$schedule->set('title',$wizard->get('name'));
		$schedule->set('provider_id',$wizard->get('provider_id'));
		$schedule->set('room_id',$wizard->get('room_id'));

		$room =& Celini::newOrdo('Room',$wizard->get('room_id'));
		$building =& Celini::newOrdo('Building',$room->get('building_id'));
		$schedule->set('practice_id',$building->get('practice_id'));

		$schedule->persist();
		$schedule->createRecurrence();

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
		$this->view->assign_by_ref('schedules',$schedules);
		$c =& Celini::newORDO('Schedule');
		return $this->view->render("schedules.html");
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
		$schedule->populateArray($this->POST->getRaw('Schedule'));
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
	
	function _processDeletes() {
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
}
?>
