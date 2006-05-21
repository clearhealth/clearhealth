<?php
if( !function_exists('memory_get_usage') )
{
   function memory_get_usage()
   {
       //If its Windows
       //Tested on Win XP Pro SP2. Should work on Win 2003 Server too
       //Doesn't work for 2000
       //If you need it to work for 2000 look at http://us2.php.net/manual/en/function.memory-get-usage.php#54642
       if ( substr(PHP_OS,0,3) == 'WIN')
       {
               $output = array();
               exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );
      
               return preg_replace( '/[\D]/', '', $output[5] ) * 1024;           
       }else
       {
           //We now assume the OS is UNIX
           //Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
           //This should work on most UNIX systems
           $pid = getmypid();
           exec("ps -eo%mem,rss,pid | grep $pid", $output);
           $output = explode("  ", $output[0]);
           //rss is given in 1024 byte units
           return $output[1] * 1024;
       }
   }
}
function memory() {
	$m = memory_get_usage();
	return ($m/1024).'KB';
}


$loader->requireOnce('includes/clni/clniData.class.php');

class ScheduleWizardData extends clniData{
	var $schedule_type = '';
	var $days = array();
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
	
		$provider =& Celini::newOrdo('Provider');
		$providers = $provider->valueList('usernamePersonId');

		$room =& Celini::newORDO('Room');
		$practice =& Celini::newOrdo('Practice');
		$room =& Celini::newOrdo('Room');
		$pa = $practice->practices_factory();
		$this->view->assign("rooms",$room->rooms_practice_factory($pa,false));
		$this->view->assign("providers",$providers);

		$this->view->assign_by_ref('wizard',$wizardData);

		$head =& Celini::HTMLHeadInstance();
		$head->addJs('clnipopup');
		return $this->view->render('wizard-'.$this->wizardPage.'.html');
	}

	function processWizard() {
		$session =& Celini::sessionInstance();

		$wizardData = $this->_getWizardData();
		$wizardData->populate($this->POST->get('wizard'));
		$this->_setWizardData($wizardData);

		$this->wizardPage = $this->POST->getTyped('next_page','int');

		switch ($this->wizardPage) {
			case 99:
				$this->createSchedule($wizardData);
				break;
			case 11:
				$this->createAdminMeeting($wizardData);
				break;
		}
	}

	function createAdminMeeting($wizard) {
		$p =& Celini::newORDO('Provider');
		$providers = $p->getProviderList();
		foreach($providers as $pid=>$pname) {
			$apt =& Celini::newORDO('Appointment');
			$apt->populateEvent();
			$apt->_event->set('title','Meeting: '.$wizard->get('title'));
			$apt->set('title','Meeting: '.$wizard->get('title'));
			$apt->set('appointment_code','ADM');
			$apt->set('provider_id',$pid);
			$apt->set('room_id',$wizard->get('room_id'));
			$apt->set('date',$wizard->get('date_start'));
			$apt->set('start_time',$wizard->get('time_start'));
			$apt->set('end_time',$wizard->get('time_end'));
			$apt->persist();
		}
	}
	
	/** 
	 * @todo need to detect matching schedules
	 * @todo need to detect matching groups
	 */
	function createSchedule($wizard) {

		// create a new schedule
		$schedule =& Celini::newORDO('Schedule');

		$schedule->set('title',$wizard->get('name'));
		$schedule->set('provider_id',$wizard->get('provider_id'));
		$schedule->set('room_id',$wizard->get('room_id'));
		$schedule->set('schedule_code',$wizard->get('schedule_type'));

		$room =& Celini::newOrdo('Room',$wizard->get('room_id'));
		$building =& Celini::newOrdo('Building',$room->get('building_id'));
		$schedule->set('practice_id',$building->get('practice_id'));

		$schedule->persist();

		// create a new event group 
		// TODO: Need to have code to keep from making a new group if the title already exists
		$egTitle = $wizard->get('group');
		if (empty($egTitle)) {
			$egTitle = 'General Hours';
		}
		$eventgroup =& Celini::newORDO('EventGroup');
		$eventgroup->set('title',$egTitle);
		$eventgroup->persist();
		$schedule->setChild($eventgroup);
		$eventGroupId = $eventgroup->get('id');

		// load up times to create occurences for
		$lunchStart = $wizard->get('lunch_start');
		$lunchEnd = $wizard->get('lunch_end');

		$times = array();
		if (!empty($lunchStart) && !empty($lunchEnd)) {
			$times[] = array('start'=>$wizard->get('time_start'),'end'=>$wizard->get('lunch_start'));
			$times[] = array('start'=>$wizard->get('lunch_end'),'end'=>$wizard->get('time_end'));
		}
		else {
			$times[] = array('start'=>$wizard->get('time_start'),'end'=>$wizard->get('time_end'));
		}

		$provider =& Celini::newOrdo('Provider',$schedule->get('provider_id'));
		$practice =& Celini::newOrdo('Practice',$schedule->get('practice_id'));
		$room =& Celini::newOrdo('Room',$schedule->get('room_id'));

		// create recurrences
		foreach($times as $time) {
			$recurrence = array();
			$recurrence['id'] = '';
			$recurrence['start_date'] = $wizard->get('date_start');
			$recurrence['end_date'] = $wizard->get('date_end');
			$recurrence['start_time'] = $time['start'];
			$recurrence['end_time'] = $time['end'];
			$recurrence['event_group'] = $eventGroupId;

			$pattern = array('pattern_type'=> 'dayweek');
			$pattern['days'] = $wizard->get('days');

			//var_dump('Pre createRecurrence: '.memory());
			$rec =& $schedule->createRecurrence($recurrence,$pattern);
			//var_dump('Post createRecurrence: '.memory());
			if($rec !== false) {
				//var_dump('Pre ScheduleEvent Loop: '.memory());
				$eg =& $rec->getParent('EventGroup');
				$events =& $rec->getChildren('ScheduleEvent');
				for($events->rewind(); $events->valid(); $events->next()) {
					$event =& $events->current();
					$event->set('title',$eg->get('title'));
					$event->setParent($schedule);
					$event->setParent($provider);
					$event->setParent($practice);
					$event->setParent($room);
					$event->setParent($eg);
					//$event->destroy();
					//unset($event);
				}
				//var_dump('Post ScheduleEvent Loop: '.memory());
			}
		}

		$this->view->assign('EDIT_ACTION',Celini::link('edit','Schedule').'schedule_id='.$schedule->get('id'));

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
