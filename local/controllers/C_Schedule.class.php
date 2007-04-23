<?php
$loader->requireOnce('includes/clni/clniData.class.php');
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('datasources/Person_ScheduleList_DS.class.php');
$loader->requireOnce('datasources/Person_ScheduleLinearList_DS.class.php');

class ScheduleWizardData extends clniData{
	var $schedule_type = '';
	var $days = array();
	var $groups = array();
	var $group = '';
	var $starts = array();
	var $ends = array();
	var $multi_group = false;
	var $provider_id = '';
	var $time_start = '';
	var $time_end = '';
	var $lunch_start = '';
	var $lunch_end = '';
	var $date_start = '';
	var $date_end = '';
	
	function ScheduleWizardData() {
		$this->date_start = date('m/d/Y',time());
		$this->date_end = date('m/d/Y',strtotime('+3 Months'));
	}
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
	function ajaxAddEvents($startDate='', $endDate='') {
		$em =& Celini::enumManagerInstance();
		$this->view->assign_by_ref('em',$em);
		$wizardData =& new ScheduleWizardData();
		$this->view->assign('WIZARD_ACTION',Celini::link('wizard'));
		$this->view->assign_by_ref('wizard',$wizardData);
		return $this->view->render('wizard-31.html');

	}
	var $wizardPage = 1;
	function actionWizard() {
		$em =& Celini::enumManagerInstance();
		$this->view->assign_by_ref('em',$em);

		if(isset($_GET['clearData'])) {
			$wizardData =& new ScheduleWizardData();
			$this->_setWizardData($wizardData);
		} else {
			$wizardData = $this->_getWizardData();
		}	
		$provider =& Celini::newOrdo('Provider');
		$providers = $provider->valueList('usernamePersonIdUnfiltered');

		$room =& Celini::newORDO('Room');
		$practice =& Celini::newOrdo('Practice');
		$room =& Celini::newOrdo('Room');
		$pa = $practice->practices_factory();
		$this->view->assign("rooms",$room->rooms_practice_factory($pa,false));
		$this->view->assign("providers",$providers);

		$this->view->assign_by_ref('wizard',$wizardData);

		$head =& Celini::HTMLHeadInstance();
		$head->addJs('clnipopup');
		$this->view->assign('WIZARD_ACTION',Celini::link('wizard'));
		return $this->view->render('wizard-'.$this->wizardPage.'.html');
	}

	function processWizard($data = '') {
		$session =& Celini::sessionInstance();

		$wizardData = $this->_getWizardData();
		if (is_array($data)) { $wizardData->populate($data); }
		else {$wizardData->populate($this->POST->get('wizard'));}
		// Validation
		$error = false;
		$time_error = array();
		$date_error = array();
		if($wizardData->get('schedule_type') == 'RS' || $wizardData->get('schedule_type') == 'PS') {
			if($wizardData->get('date_start') != '') {
				if(strtotime($wizardData->get('date_start')) > strtotime($wizardData->get('date_end'))) {
					$date_error[] = 'Start date must be prior to or equal to the end date.';
					$error = true;
				}
				if($wizardData->get('time_start') != '') {
					if(strtotime($wizardData->get('date_start').' '.$wizardData->get('time_start')) >= strtotime($wizardData->get('date_start').' '.$wizardData->get('time_end'))) {
						$error = true;
						$time_error[] = 'Schedule start time must be before schedule end time.';
					}
				}
				if($wizardData->get('lunch_start') != '') {
					if(strtotime($wizardData->get('date_start').' '.$wizardData->get('lunch_start')) > strtotime($wizardData->get('date_start').' '.$wizardData->get('lunch_end'))) {
						$error = true;
						$time_error[] = 'Break start time must be before break end time.';
					}
					if(
						strtotime($wizardData->get('date_start').' '.$wizardData->get('lunch_start')) <
						strtotime($wizardData->get('date_start').' '.$wizardData->get('time_start'))
					) {
						$error = true;
						$time_error[] = 'Break start must be after start of schedule time.';
					}
					if(
						strtotime($wizardData->get('date_start').' '.$wizardData->get('lunch_start')) >
						strtotime($wizardData->get('date_start').' '.$wizardData->get('time_end'))
					) {
						$error = true;
						$time_error[] = 'Break start must be before end of schedule time.';
					}
					if(
						strtotime($wizardData->get('date_start').' '.$wizardData->get('lunch_end')) <
						strtotime($wizardData->get('date_start').' '.$wizardData->get('time_start'))
					) {
						$error = true;
						$time_error[] = 'Break end must be after start of schedule time.';
					}
					if(
						strtotime($wizardData->get('date_start').' '.$wizardData->get('lunch_end')) >
						strtotime($wizardData->get('date_start').' '.$wizardData->get('time_end'))
					) {
						$error = true;
						$time_error[] = 'Break end must be before end of schedule time.';
					}
				}
			}
		}
		$this->_setWizardData($wizardData);
		if($error) {
			if(count($date_error) > 0) {
				$this->messages->addMessage('Date Error',implode('<br />',$date_error),'warningMessage');
			}
			if(count($time_error) > 0) {
				$this->messages->addMessage('Time Error',implode('<br />',$time_error),'warningMessage');
			}
			$this->wizardPage = $this->POST->getTyped('current_page','int');
			return $error;
		}		

		if (is_array($data)) {$this->wizardPage = $data['wizard_page'];}
		else {$this->wizardPage = $this->POST->getTyped('next_page','int');}

		switch ($this->wizardPage) {
			case 99:
				$this->createSchedule($wizardData);

				// clear wizard data
				$wizardData =& new ScheduleWizardData();
				$this->_setWizardData($wizardData);
				break;
			case 11:
				$this->createAdminMeeting($wizardData);
				
				// clear wizard data
				$wizardData =& new ScheduleWizardData();
				$this->_setWizardData($wizardData);
				break;
			case 30:
				$this->interactiveProviderSchedule();
				$this->_setWizardData($wizardData);
				break;
			case 31:
				$ret = $this->createSchedule($wizardData);
				$this->view->assign("success", true);

				break;
		}
	}
	function ajaxDeleteEvents($eventIds) {
		$this->assign('eventIds',$eventIds);
		$evAr = split(',',$eventIds);
		$this->assign('eventId1',$evAr[0]);
		
		return $this->view->render('deleteEvents.html');
	}
	function ajaxDelDaysEvents($eventIds) {
		$evIdAr = split(',',$eventIds);
		foreach ($evIdAr as $evId) {
			$ev = ORDataObject::factory("CalendarEvent",$evId);
			$ev->drop();
		}
		$this->assign('success',true);
		$this->messages->addMessage('Events deleted');
		return $this->view->render('addEventsMessages.html');

	}
	function ajaxDelAllEvents($eventId, $personId, $roomId) {
		$ev = ORDataObject::factory("CalendarEvent",$eventId);
		$start = $ev->get('start');
		$scheduleDS =& new Person_ScheduleLinearList_DS($personId,$roomId,$start,'2024-12-31');
			for($scheduleDS->rewind();$scheduleDS->valid();$scheduleDS->next()) {
			  $row = $scheduleDS->get();	
			  $ev = ORDataObject::factory("CalendarEvent");
			  $ev->set('event_id',$row['event_id']);
			  $ev->drop();
		}
		$this->assign('success',true);
		$this->messages->addMessage("Events deleted");
		return $this->view->render('addEventsMessages.html');
	}
	function ajaxEditEvent($event_id,$action ='',$data = array())	{
		$ev = ORDataObject::factory("CalendarEvent",$event_id);
		switch($action) {
		  case 'update':
			$ev->populateArray($data);
			$ev->persist();
			$this->messages->addMessage("Event updated");
			$this->assign("event",$ev);
			break;
		  case 'addevents':
			$data['schedule_type'] = 'PS';
			$data['group'] = $data['name'];
			$data['wizard_page'] = '31';
			$this->processWizard($data);
			return $this->view->render("addEventsMessages.html");
			break;
		  default:
			$this->assign("event",$ev);
		}
		return $this->view->render("editEvent.html");
	}

	function ajaxInteractiveProviderGrid($personId, $roomId, $start, $end) {
		$scheduleDS =& new Person_ScheduleList_DS($personId,$roomId,$start,$end);
		//return print_r($scheduleDS->toArray(),true);
		//return $scheduleDS->preview();
		$sgrid =& new cGrid($scheduleDS);
		$sgrid->registerTemplate("start1",'&nbsp;&nbsp;<a href="javascript:editEvent({$event_id_1})">{$start1}</a>&nbsp;&nbsp;');
		$sgrid->registerTemplate("start2",'&nbsp;&nbsp;<a href="javascript:editEvent({$event_id_2});">{$start2}</a>&nbsp;&nbsp;');
		$sgrid->registerTemplate("note",'&nbsp;&nbsp;<a href="#" onclick="deleteEvent(\'{$event_ids}\');">del</a>&nbsp;&nbsp;{$note}&nbsp;&nbsp;');
		if ($scheduleDS->numRows() == 0)
			return "No matching schedule data found";
		//true without paging header
		return $sgrid->render(true);
		
	}

	function interactiveProviderSchedule() {
		$this->ajaxInteractiveProviderGrid('1000119','1000091','2007-04-04','2007-04-05');
		$providerId = '';
		$prov = ORDataObject::factory('Provider',$providerId);
		$provList = $prov->getProviderList();
		$this->assign('providerList',$provList);

		$userProfile =& Celini::getCurrentUserProfile();
                $curPracticeId = $userProfile->getCurrentPracticeId();	
		$curPractice = ORDataObject::factory('Practice',$curPracticeId);
		$room =& Celini::newOrdo('Room');
		$this->view->assign("roomList",$room->rooms_practice_factory($curPracticeId,false));



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
	function createSchedule(&$wizard) {
		// create a new schedule (if one does not already exist)
		$shorterror = false;
		switch ($wizard->get('schedule_type')) {
			case 'PS' :
				$schedule =& Celini::newORDO('Schedule', $wizard->get('provider_id'), 'ByProvider');
				break;
			
			case 'RS' :
				$schedule =& Celini::newORDO('Schedule', $wizard->get('room_id'), 'ByRoomId');
				break;
			
			case 'ADM' :
				$schedule =& Celini::newORDO('Schedule', $wizard->get('room_id'), 'ByMeetingRoomId');
				break;
		}
		

		// An array to hold the events so we can put them in a grid
		$eventArray = array();
		
		$schedule->set('title',$wizard->get('name'));
		$schedule->set('provider_id',$wizard->get('provider_id'));
		$schedule->set('schedule_code',$wizard->get('schedule_type'));

		$room =& Celini::newOrdo('Room',$wizard->get('room_id'));
		$provider =& Celini::newORDO('Provider',$wizard->get('provider_id'));
		$schedule->persist();
	
	if($wizard->get('multi_group') == false) {
		$db =& $schedule->dbHelper;
		$egTitle = $wizard->get('group');
		$whereTmp = " AND eg.title=".$db->quote($egTitle);
		if (empty($egTitle)) {
			$egTitle = 'General Hours';
			$whereTmp = " AND eg.room_id=".$db->quote($room->get('id'));
		}
		
		$sql = "
			SELECT
				event_group_id
			FROM
				event_group eg
			WHERE
				eg.schedule_id=".$db->quote($schedule->get('id')).$whereTmp;
		$res = $db->execute($sql);

		if($res && !$res->EOF) {
			$eventgroup =& Celini::newORDO('EventGroup',$res->fields['event_group_id']);
		} else {
			$eventgroup =& Celini::newORDO('EventGroup');
			$eventgroup->set('title',$egTitle);
			$eventgroup->set('room_id',$room->get('id'));
			$eventgroup->set('schedule_id',$schedule->get('id'));
			$eventgroup->persist();
		}
		
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

		$practice =& Celini::newOrdo('Practice',$schedule->get('practice_id'));

		// create recurrences
		foreach($times as $time) {
			$recurrence = array();
			$recurrence['id'] = '';
			$recurrence['start_date'] = $wizard->get('date_start');
			$end_ts = strtotime($wizard->get('date_end'));
			$plustwoyears = strtotime($recurrence['start_date'].' +2 years');
			if($plustwoyears < $end_ts) {
				$newend = DateObject::createFromISO(date('Y-m-d',$plustwoyears));
				$recurrence['end_date'] = $newend->toISO();
				if($shorterror != true) {
					$shorterror = true;
					$this->messages->addMessage('Schedule Shortened',"The schedule creation wizard is limited to two years maximum.  Your schedule's end date was change to ".$newend->toString().".");
				}
			} else {
				$recurrence['end_date'] = $wizard->get('date_end');
			}
			$recurrence['start_time'] = $time['start'];
			$recurrence['end_time'] = $time['end'];
			$recurrence['event_group'] = $eventGroupId;
			$pattern = array('pattern_type'=> 'dayweek');
			$pattern['days'] = $wizard->get('days');

			$rec =& $schedule->createRecurrence($recurrence,$pattern);
			if($rec !== false) {
				$eg =& $rec->getParent('EventGroup');
				$eventids = $rec->getChildrenIds('ScheduleEvent');
				$events = $rec->getChildren('ScheduleEvent');
				$db =& $eg->dbHelper;
				for($events->rewind();$events->valid();$events->next()) {
					$event =& $events->current();
					// Add to the eventsArray
					$eventArray[] = array('title'=>$egTitle."&nbsp;",'start'=>$event->get('start')."&nbsp;",'end'=>$event->get('end')."&nbsp;");
					// Clear out old calendar columns from this date
					$date =& $event->start->getDate();
					$date = $date->toISO();
					$this->view->regexClearCache('/^'.$date.'-/');
					$id = $event->get('id');
					$qScheduleEventId = $db->quote($id);
					$qEventGroupId = $db->quote($eg->get('id'));
					$sql = "
						INSERT INTO schedule_event
							(`event_id`,`event_group_id`)
						VALUES ({$qScheduleEventId},{$qEventGroupId})";
					$db->execute($sql);
				}
			}
		}

	} else {
			// Multiple groups
			$egs = array();
			foreach($wizard->get('groups') as $id=>$group) {
				if($group != '') {
					if(isset($egs[$group])) {
						$eg =& $egs[$group];
					} else {
						$eg =& $schedule->getEventGroupByNameAndRoom($group,$room->get('id'));
						if($eg->get('id') < 1) {
							$eg->persist();
						}
						$egs[$group] = $eg;
					}
				}
			}
			$pattern = array('pattern_type'=> 'dayweek');
			$pattern['days'] = $wizard->get('days');

			$starts = $wizard->get('starts');
			$ends = $wizard->get('ends');
			$groups = $wizard->get('groups');
			foreach($groups as $id=>$group) {
				if($group != '' && $starts[$id] != '' && $ends[$id] != '') {
					$recurrence = array();
					$recurrence['id'] = '';
					$recurrence['start_date'] = $wizard->get('date_start');
					$end_ts = strtotime($wizard->get('date_end'));
					$plustwoyears = strtotime($recurrence['start_date'].' +2 years');
					if($plustwoyears < $end_ts) {
						$newend = DateObject::createFromISO(date('Y-m-d',$plustwoyears));
						$recurrence['end_date'] = $newend->toISO();
						if($shorterror != true) {
							$shorterror = true;
							$this->messages->addMessage('Schedule Shortened',"The schedule creation wizard is limited to two years maximum.  Your schedule's end date was change to ".$newend->toString().".");
						}
					} else {
						$recurrence['end_date'] = $wizard->get('date_end');
					}
					$recurrence['start_time'] = $starts[$id];
					$recurrence['end_time'] = $ends[$id];
					$recurrence['event_group'] = $egs[$group]->get('id');
					$rec =& $schedule->createRecurrence($recurrence,$pattern);
					$events = $rec->getChildren('ScheduleEvent');
					for($events->rewind();$events->valid();$events->next()) {
						$event =& $events->current();
						// Add to the eventsArray
						$eventArray[] = array('title'=>$group."&nbsp;",'start'=>$event->get('start')."&nbsp;",'end'=>$event->get('end')."&nbsp;");
					}
					if($rec !== false) {
						$eg =& $egs[$group];
						$eventids = $rec->getChildrenIds('ScheduleEvent');
						$db =& $eg->dbHelper;
						foreach($eventids as $id) {
							//$sql = "UPDATE event SET `title`=".$eg->dbHelper->quote($eg->get('title'))." WHERE event_id=".$eg->dbHelper->quote($id);
							//$db->execute($sql);
							$qScheduleEventId = $db->quote($id);
							$qEventGroupId = $db->quote($eg->get('id'));
							$sql = "
						INSERT INTO schedule_event
							(`event_id`,`event_group_id`)
						VALUES ({$qScheduleEventId},{$qEventGroupId})";
							$db->execute($sql);
						}
					}
				}
			}
		}
		$GLOBALS['loader']->requireOnce('includes/Datasource_array.class.php');
		$ds =& new Datasource_array();
		$ds->setup(array('title'=>'Title','start'=>'Start','end'=>'End'),$eventArray);
		$egrid =& new cGrid($ds);
		$egrid->orderLinks = false;
		$egrid->_allowHideColumns = false;
		$this->view->assign_by_ref('eventGrid',$egrid);
		$this->messages->addMessage('Schedule Created','Your schedule was added successfully.');
		$this->view->assign('EDIT_ACTION',Celini::link('edit','Schedule').'schedule_id='.$schedule->get('id'));
		return true;

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
	function actionEdit($schedule_id=false) {
		$this->sec_obj->acl_qcheck("edit",$this->_me,"","schedule",$this,false);
		if($schedule_id !== false) {
			$schedule =& Celini::newORDO('Schedule', $schedule_id);
		} elseif(is_null($this->schedule)){
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

		$event =& Celini::newORDO('ScheduleEvent', $this->GET->getTyped('event_id', 'int'));
		$this->view->assign_by_ref('event',$event);
		
		$delete_eg = $this->GET->get('delete_event_group');
		if($delete_eg > 0) {
			$eg =& Celini::newORDO('EventGroup',$delete_eg);
			// Remove recurrences
			$recs = $eg->getRecurrences();
			for($recs->rewind();$recs->valid();$recs->next()) {
				$rec =& $recs->current();
				$rec->drop();
			}
			// Remove single events left over
			$events = $eg->getEvents();
			for($events->rewind();$events->valid();$events->next()) {
				$event =& $events->current();
				$event->drop();
			}
			$title = $eg->get('title');
			$eg->drop();
			$this->messages->addMessage("Event Group '{$title}' Removed");
		}
		
		$eventGroup =& Celini::newORDO('EventGroup', $this->getDefault('event_group_id', 0));
		$this->view->assign_by_ref('eventGroup',$eventGroup);

		if($schedule->get('id') > 0) {
			$eventGroups =& $schedule->getChildren('EventGroup');
			$this->view->assign_by_ref('eventGroups',$eventGroups);
		}
		
		if($this->GET->getTyped('event_group_id','int') > 0) {
			$this->view->assign('egid',$this->GET->getTyped('event_group_id','int'));
		}
		
		return $this->view->render('edit.html');
	}
	
	function _processEvent() {
		$eventarray = $this->POST->getRaw('Event');
		if(!empty($eventarray['title'])){
			$event =& Celini::newORDO('ScheduleEvent',$eventarray['id']);
			$event->populate_array($eventarray);
			$event->persist();
			if($eventarray['id'] > 0){
				$this->messages->addMessage('Event Updated');
			} else {
				// This should never happen now with the wizard.
				$this->messages->addMessage('Event Added');
			}
		}
	}
	
	/**
	 * No longer needed(?)
	 *
	 */
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
//		$schedule->persist();
		$this->schedule =& $schedule;
		$this->practice =& $schedule->getParent('Practice');
		$this->provider =& $schedule->getParent('Provider');
		
		$this->room =& $schedule->getParent('Room');
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
}
?>
