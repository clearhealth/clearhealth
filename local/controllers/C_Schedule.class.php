<?php
$loader->requireOnce('includes/clni/clniData.class.php');

class ScheduleWizardData extends clniData{
	var $schedule_type = '';
	var $days = array();
	var $groups = array();
	var $starts = array();
	var $ends = array();
	var $multi_group = false;
	var $provider_id = '';
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
		$schedule =& Celini::newORDO('Schedule', $wizard->get('provider_id'), 'ByProvider');

		$schedule->set('title',$wizard->get('name'));
		$schedule->set('provider_id',$wizard->get('provider_id'));
		$schedule->set('schedule_code',$wizard->get('schedule_type'));

		$room =& Celini::newOrdo('Room',$wizard->get('room_id'));
		$provider =& Celini::newORDO('Provider',$wizard->get('provider_id'));

		$schedule->persist();

	if($wizard->get('multi_group') == false) {
		$egTitle = $wizard->get('group');
		if (empty($egTitle)) {
			$egTitle = 'General Hours';
		}
		
		$db =& $schedule->dbHelper;
		$sql = "
			SELECT
				event_group_id
			FROM
				event_group eg
			WHERE
				eg.schedule_id=".$db->quote($schedule->get('id'))."
				AND eg.room_id=".$db->quote($room->get('id'));
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
			$recurrence['end_date'] = $wizard->get('date_end');
			$recurrence['start_time'] = $time['start'];
			$recurrence['end_time'] = $time['end'];
			$recurrence['event_group'] = $eventGroupId;

			$pattern = array('pattern_type'=> 'dayweek');
			$pattern['days'] = $wizard->get('days');

			$rec =& $schedule->createRecurrence($recurrence,$pattern);
			if($rec !== false) {
				$eg =& $rec->getParent('EventGroup');
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
					$recurrence['end_date'] = $wizard->get('date_end');
					$recurrence['start_time'] = $starts[$id];
					$recurrence['end_time'] = $ends[$id];
					$recurrence['event_group'] = $egs[$group]->get('id');
					$rec =& $schedule->createRecurrence($recurrence,$pattern);
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
