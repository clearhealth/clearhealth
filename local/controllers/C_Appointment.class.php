<?php
$loader->requireOnce('datasources/CopySchedule_DS.class.php');
class C_Appointment extends Controller {

	var $uiMode = 'normal';
	var $appointment = null;
	var $_patient_id = '';

	function actionAdd() {
		return $this->actionEdit();
	}
	
	function actionEdit() {
		if(is_null($this->appointment)) {
			$appointmentId = $this->getDefault('appointment_id',0);
			$apt =& Celini::newOrdo('Appointment',$appointmentId);
		} else {
			$apt =& $this->appointment;
		}
		$this->view->assign_by_ref('appointment',$apt);
		
		if($apt->get('id') > 0) {
			$this->view->assign('queue_id',$apt->get('queue_id'));
			$this->view->assign_by_ref('visitqueue',Celini::newORDO('VisitQueue',$apt->get('queue_id')));
			$this->view->assign('qreason_id',$apt->get('qreason_id'));
			$this->view->assign_by_ref('visitqueuereason',Celini::newORDO('VisitQueueReason',$apt->get('qreason_id')));
			$this->view->assign('provider_id',$apt->get('provider_id'));
			$this->view->assign_by_ref('provider',Celini::newORDO('Provider',$apt->get('provider_id')));
			$this->view->assign('patient_id',$apt->get('patient_id'));
			$this->view->assign_by_ref('patient',Celini::newORDO('Patient',$apt->get('patient_id')));
		}
		
		if($this->GET->get('queue_id') > 0) {
			$this->view->assign('queue_id',$this->GET->get('queue_id'));
			$this->view->assign('visitqueue',Celini::newORDO('VisitQueue',$this->GET->get('queue_id')));
			$this->view->assign('doappointmentpopup',true);
		}
		if($this->GET->get('qreason_id') > 0) {
			$this->view->assign('qreason_id',$this->GET->get('qreason_id'));
			$this->view->assign('visitqueuereason',Celini::newORDO('VisitQueueReason',$this->GET->get('qreason_id')));
			$this->view->assign('doappointmentpopup',true);
		}
		if($this->GET->get('provider_id') > 0) {
			$this->view->assign('provider_id',$this->GET->get('provider_id'));
			$this->view->assign_by_ref('provider',Celini::newORDO('Provider',$this->GET->get('provider_id')));
			$this->view->assign('doappointmentpopup',true);
		}
		if($this->GET->get('patient_id') > 0 || $this->_patient_id > 0) {
			$pid = $this->_patient_id;
			if ($this->GET->get('patient_id')> 0) {
				$pid = $this->GET->get('patient_id');
			}
			$this->view->assign('patient_id',$pid);
			$patient = Celini::newORDO('Patient',$pid);
			$this->view->assign('patient',$patient);
			if (!isset($this->view->_tpl_vars['provider_id'])) {
			$this->view->assign('provider_id',$patient->get('default_provider'));
			}
			$this->view->assign('doappointmentpopup',true);
		}

		
		if($this->GET->get('start_time') != '') {
			$this->view->assign('start_time',$this->GET->get('start_time'));
			$this->view->assign('end_time',$this->GET->get('end_time'));
			$this->view->assign('doappointmentpopup',true);
		}
		if($this->GET->get('date') != '') {
			$this->view->assign('apt_date',date('m/d/Y',strtotime($this->GET->get('date'))));
		}
		else {
			$session =& Celini::sessionInstance();
			$this->view->assign('apt_date', date('m/d/Y',strtotime($session->get('calendar:selectedDay',date('Y-m-d')))));
		}

		$head =& Celini::HTMLHeadInstance();
		$head->addNewJs('C_Appointment','templates/appointment/appointment.js');
		$head->addJs('scriptaculous');
		$head->addJs('clniPopup');
		$head->addExternalCss('suggest');
		$room =& Celini::newORDO('Room',$apt->get('room_id'));
		$provider =& Celini::newORDO('Provider',$apt->get('provider_id'));
		$person =& Celini::newORDO('Person');
		$users_array = array();
		$users_array[0]=$person->getPersonList(0);
		$users_array[2]=$person->getPersonList(2);
		$users_array[3]=$person->getPersonList(3);
		$users_array[4]=$person->getPersonList(4);
		$users_array[5]=$person->getPersonList(5);
		$this->view->assign('users_array',$users_array);
		$this->view->assign_by_ref('room',$room);
		$this->view->assign('mode',$this->uiMode);

		// If we're editing an admin appointment, return the special form.
		if($apt->get('appointment_code') == 'ADM') {
			return $this->view->render('editmeeting.html');
		}
		// appointment template stuff
		$manager =& EnumManager::getInstance();
		$list =& $manager->enumList('appointment_reasons');

		$templates = array();
		for($list->rewind();$list->valid();$list->next()) {
			$row = $list->current();
			if ($row->extra1 !== '') {
				$templates[$row->key] =& Celini::newOrdo('AppointmentTemplate',$row->extra1);
			}
			else {
				$templates[$row->key] = false;
			}
		}
		$this->assign('appointment_templates',$templates);
		
		return $this->view->render('edit.html');
	}

	function ajax_edit($id,$type='',$patient_id = '',$provider_id = '',$start_time ='',$end_time = '') {
		$this->appointment =& Celini::newOrdo('Appointment',$id);
		$this->appointment->set('appointment_code',$type);
		$this->view->assign('ajaxedit',true);
		$this->_patient_id = $patient_id;
		$this->assign('patient_id',$patient_id);
		if ($provider_id > 0) {
		$this->assign('provider_id',$provider_id);
		}
		if ($start_time > 0) {
		$this->assign('start_time',$start_time);
		}
		if ($end_time > 0) {
		$this->assign('end_time',$end_time);
		}

		$this->assign('FORM_ACTION',Celini::link('Day','CalendarDisplay'));
		return array($id,$this->actionEdit(),$type);
	}

	function process($data) {
		if(isset($data['appointment_code']) && $data['appointment_code'] == 'ADM') {
			$this->_processMeeting($data);
		} else {
			$this->_createAppointmentOrdo($data);
			$this->appointment->persist();
		}
	}
	
	/**
	 * Processes an administrative meeting edit
	 *
	 * @param array $data
	 */
	function _processMeeting($data) {
		$db =& new clniDB();
		$apt =& Celini::newORDO('Appointment',$data['appointment_id']);
		$date = DateObject::create($data['date']);
		if($apt->get('id') > 0) {
			if($apt->get('event_group_id') > 0 && $data['allproviders'] == 1) {
				$qstart = date('Y-m-d H:i:s',strtotime($apt->get('start')));
				$qend = date('Y-m-d H:i:s',strtotime($apt->get('end')));
				
				$sql = "
				SELECT
					a.appointment_id,e.event_id
				FROM
					".$apt->tableName()." AS a
					INNER JOIN event AS e USING(event_id)
				WHERE
					a.event_group_id = ".$apt->get('event_group_id')."
					AND e.start=".$db->quote($qstart)."
					AND e.end=".$db->quote($qend);
				$res = $db->execute($sql);
				// Only worry about schedule stuff if this event was part of a group of meetings
				$eg =& Celini::newORDO('EventGroup',$apt->get('event_group_id'));
				$schedule =& Celini::newORDO('Schedule',$data['room_id'],'ByMeetingRoomId');
				if($schedule->get('id') < 1) {
					$schedule->populateArray($data);
					$schedule->set('schedule_code','ADM');
					$room =& Celini::newORDO('Room',$data['room_id']);
					$schedule->set('title','Meetings For '.$room->value('fullname'));
					$schedule->persist();
				}
				if($eg->get('schedule_id') != $schedule->get('id')) {
					$eg->set('schedule_id',$schedule->get('id'));
					$eg->persist();
				}
				$apt->populateArray($data);
				$qstart = date('Y-m-d H:i:s',strtotime($apt->get('start')));
				$qend = date('Y-m-d H:i:s',strtotime($apt->get('end')));
				
				for($res->MoveFirst();!$res->EOF;$res->MoveNext()) {
					$sql = "
					UPDATE
						".$apt->tableName()." AS a
						INNER JOIN event AS e USING(event_id)
					SET
						a.title=".$db->quote($apt->get('title')).",
						e.title=".$db->quote($apt->get('title')).",
						e.start=".$db->quote($qstart).",
						e.end=".$db->quote($qend).",
						a.room_id=".$db->quote($apt->get('room_id'))."
					WHERE
						a.appointment_id='{$res->fields['appointment_id']}'
						AND e.event_id='{$res->fields['event_id']}'
					";
					$db->execute($sql);
				}
			} else {
				// Just updating a single appointment
				$apt->populateArray($data);
				// If we're just updating a single appointment that used to be part of a group, clear the group.
				$apt->set('event_group_id',0);
				$apt->persist();
			}
		} elseif($data['allproviders'] > 0) {
			$schedule =& Celini::newORDO('Schedule',$data['room_id'],'ByMeetingRoomId');
			if($schedule->get('id') == 0) {
				$schedule->populateArray($data);
				$schedule->set('schedule_code','ADM');
				$room =& Celini::newORDO('Room',$data['room_id']);
				$schedule->set('title','Meeting Schedule: '.$room->value('fullname'));
				$schedule->persist();
			}
			$eg =& Celini::newORDO('EventGroup');
			$eg->set('title',$data['title']);
			$eg->set('room_id',$data['room_id']);
			$eg->set('schedule_id',$schedule->get('id'));
			$eg->persist();
			$p =& Celini::newORDO('Provider');
			$providers = $p->getProviderList();
			$apt->populateArray($data);
			$apt->set('event_group_id',$eg->get('id'));
			foreach($providers as $pid=>$pname) {
				$apt->set('id',0);
				$apt->_event->set('id',0);
				$apt->set('provider_id',$pid);
				$apt->persist();
			}
		} else {
			// Just updating a single appointment
			$apt->populateArray($data);
			$apt->persist();
		}
	}
	
	/**
	 * Handles creating an {@link Appointment} ordo for saving.
	 *
	 * @see    process(), processAjax()
	 * @access private
	 */
	function _createAppointmentOrdo($data) {
		if (isset($data['users'])) {
			$data['provider_id'] = current($data['users']);
		}
		$db =& Celini::dbInstance();
//		$db->debug = true;
		$user =& $this->_me->get_user();
		$appointmentId = 0;
		if (isset($data['appointment_id'])) {
			$appointmentId = enforceType::int($data['appointment_id']);
		}
		$appointment =& Celini::newOrdo('Appointment',$appointmentId);
		$appointment->populateArray($data);
		if($appointment->get('id') > 0) {
			$appointment->set('last_change_date',date('Y-m-d'));
			$appointment->set('last_change_id',$user->get('id'));
		} else {
			// Check for consecutive no-shows
			$noshows = $appointment->checkNoShows();
			if($noshows > 2) {
				$this->messages->addMessage('Patient has not shown up for three consecutive appointments prior to this one.');
			}
			$appointment->set('created_date',date('Y-m-d'));
		}
		// Was this event in a schedule?
		$sql = "
			SELECT eg.event_group_id,s.schedule_code 
			FROM 
				schedule s
				INNER JOIN event_group eg ON eg.schedule_id=s.schedule_id
				INNER JOIN schedule_event se ON se.event_group_id=eg.event_group_id
				INNER JOIN event e ON e.event_id=se.event_id
			WHERE
				s.provider_id=".$db->quote($appointment->get('provider_id'))."
				AND eg.room_id=".$db->quote($appointment->get('room_id'))."
				AND e.start <= ".$db->quote($appointment->get('start_time'))."
				AND e.start > ".$db->quote($appointment->get('end_time'));
		$res = $db->execute($sql);
		if($res && !$res->EOF) {
			$appointment->set('event_group_id',$res->fields['event_group_id']);
			$appointment->set('appointment_code',$res->fields['schedule_code']);
		}
		
		$this->appointment =& $appointment;
		if (isset($data['users']) && count($data['users']) > 1) {
			$appointment->breakdowns = array();
			foreach($data['users'] as $breakdown_id=>$person_id) {
				$appointment->breakdowns[$breakdown_id] =& Celini::newORDO('AppointmentBreakdown');
				$appointment->breakdowns[$breakdown_id]->set('id',0);
				$appointment->breakdowns[$breakdown_id]->set('appointment_id',$appointment->get('id'));
				$appointment->breakdowns[$breakdown_id]->set('occurence_breakdown_id',$breakdown_id);
				$appointment->breakdowns[$breakdown_id]->set('person_id',$person_id);
			}
		}
	}

	function ajax_cancel($id) {
		$apt =& Celini::newORDO('Appointment',$id);
		$apt->set('appointment_code','CAN');
		$apt->persist();
		return array($id,'<strong>CANCELLED</strong>');
	}
	
	function ajax_ns($id) {
		$apt =& Celini::newORDO('Appointment',$id);
		$apt->set('appointment_code','NS');
		$apt->persist();
		$noshows = $apt->checkNoShows(3,true);
		if(count($noshows) == 2) {
			$note =& Celini::newORDO('PatientNote');
			$note->set('user_id',$this->_me->get_user_id());
			$note->set('patient_id',$apt->get('patient_id'));
			$note->set('note_date',date('Y-m-d'));
			$note->set('note','Patient has three consecutive no-shows: '.implode(', ',$noshows));
			$note->persist();
		}
		$noshows = count($noshows);
		$noshows ++;
		return array($id,"<strong>NO SHOW #$noshows</strong>");
	}

	function ajax_delete($id) {
		$apt =& Celini::newORDO('Appointment',$id);
		$queue =& $apt->getParent('VisitQueue');
		$apt->drop();
		$qtext = '';
		if($queue->get('id') > 0) {
			$qtext .= '  This appointment was part of a queue.  Please <a href="'.Celini::link('Edit','VisitQueue').'queue_id='.$queue->get('id').'">reschedule</a>.';
		}
		return array($id,'<strong>DELETED</strong>'.$qtext);
	}
	
	/* Old stuff below needs to be updated */
	function editGroup_action_edit($appointment_id) {

		$oc =& ORDataobject::factory('Occurence',$appointment_id);
		$this->assign('start',$oc->get('start'));
		$this->assign('title',$oc->get('notes'));
		$this->assign('duration',$oc->get('duration'));
		$this->assign('FORM_ACTION',Celini::link(true,true,true,$appointment_id));
		$this->assign('ENCOUNTER_ACTION',Celini::link('Encounter','patient',true,0)."occurence_id=$appointment_id&");

		$go =& ORDataObject::Factory('GroupOccurence');
		$patientList = $go->getPatientlist($appointment_id);
		$patientListCount = count($patientList);

		$this->assign('patientList',$patientList);
		$this->assign('patientListCount',$patientListCount);

		return $this->view->render("editGroup.html");
	}

	function editGroup_action_process($appointment_id) {
		$go =& ORDataObject::factory('GroupOccurence');

		foreach($_POST['patient'] as $patient => $status) {
			if ($status) {
				$go->quickAdd($appointment_id,$patient);
			}
			else {
				$go->quickDrop($appointment_id,$patient);
			}
		}
		$oc =& ORDataobject::factory('Occurence',$appointment_id);
		if ($oc->get('group_appointment') != 1) {
			$oc->set('group_appointment',1);
			$oc->persist();
		}
	}

	var $pullDate = false;
	var $pullType = 'new';
	function actionPullList() {
		if ($this->POST->exists('date')) {
			$this->pullDate = date('Y-m-d',strtotime($this->POST->get('date')));
		}
		if ($this->POST->exists('type')) {
			$this->pullType = $this->POST->get('type');
		}

		$pullDate = $this->pullDate;
		if ($this->pullDate == false) {
			$pullDate = date('Y-m-d');
		}

		$type = $this->pullType;

		// get upcoming appointments
		$start = date('Y-m-d H:i:s',strtotime($pullDate.' 01:01:01'));
		$end = date('Y-m-d H:i:s',strtotime(date('Y-m-d',strtotime($pullDate,'+1 day')).' 23:59:59'));

		$format = TimestampObject::getFormat();
		$sql = array();
		$sql['cols'] = "
			a.appointment_id,
			pt.record_number,
			p.last_name,
			p.first_name,
			b.name building_name,
			concat(pro.last_name,', ',pro.first_name) provider,
			date_format(e.start,'$format') appointment_time,
			date_format(pl.pull_date,'$format') pull_date
			";
		$sql['from'] = "
			event e
			inner join appointment a on e.event_id = a.event_id
			inner join patient pt on a.patient_id = pt.person_id
			left join person p on pt.person_id = p.person_id
			left join person pro on a.provider_id = pro.person_id
			left join user u on pro.person_id = u.person_id
			left join rooms r on a.room_id = r.id
			left join buildings b on r.building_id = b.id
			left join pull_list pl on a.appointment_id = pl.appointment_id
		";

		$sql['where']  = "e.start between '$start' and '$end'";


		$db = new clniDb();
		$now = $db->quote(date('Y-m-d H:i:s'));

		$insert = 'insert into pull_list select a.appointment_id, '.$now.' from '.$sql['from'].' where '.$sql['where'].' and pl.pull_date is null';
		$db->execute($insert);

		if ($type == 'new') {
			$sql['where'] .= " and pl.pull_date = $now";
		}

		$ds = new Datasource_sql();
		$ds->setup(Celini::dbInstance(),$sql,
			array(
				'record_number'=>'Record #',
				'last_name'=>'Last Name',
				'first_name'=>'First Name',
				'building_name'=>'Treating Facility',
				'provider'=>'Treating Provider',
				'appointment_time'=>'Appointment Time',
				'pull_date'=>'Pulled At'
			));
		$grid =& new cGrid($ds);
		$this->view->assign_by_ref('grid',$grid);

		$pd = DateObject::create($pullDate);
		$this->view->assign('date',$pd->toString());
		$this->view->assign('type',$type);

		if (isset($this->noRender) && $this->noRender === true) {
			return "pullList.html";
		}
		return $this->view->render('pullList.html');
		
	}

	function actionView() {
		if(is_null($this->appointment)) {
			$apid = $this->GET->getTyped('appointment_id','int');
			$appointment =& Celini::newORDO('Appointment',$apid);
		} else {
			$appointment =& $this->appointment;
		}
		$this->view->assign_by_ref('appointment',$appointment);
		$event =& $appointment->getParent('CalendarEvent');
		$this->view->assign_by_ref('event',$event);
		$provider =& $event->getParent('Provider');
		$patients =& $event->getChildren('Patient');
		$this->view->assign_by_ref('patients',$patients);
		$this->view->assign_by_ref('provider',$provider);
		$room =& $event->getParent('Room');
		$this->view->assign('ev_edit',true);
		$user =& User::fromPersonId($provider->get('id'));
		$this->view->assign_by_ref('provideruser',$user);
		$p =& $patients->current();
		$prsn =& Celini::newORDO('Person',$p->get('id'));
		$numbers = array_values($prsn->get_numbers());
		$this->view->assign('phone',isset($numbers[0]) ? $numbers[0]['number'] : '');
		return $this->view->render('singleappointment.html');
	}

	function getAJAXMethods(){
		return array('actionView', 'ajax_edit', 'ajax_cancel', 'ajax_ns', 'ajax_delete', 'ajax_process','ajax_viewalt');
	}

	function actionSearch($patient_id = '') {
		$post = $this->POST->getRaw('Search');
		if (isset($post['patient_id'])) {
			$patient_id = (int)$post['patient_id'];
		}
		$head =& Celini::HTMLHeadInstance();
		$head->addExternalCss('calendar','calendar');
		$head->addExternalCss('suggest');
		if(!is_null($this->appointment)) {
			return $this->appointment;
		}
		$search = $this->POST->getRaw('Search');
		$this->assign('search',$search);
		$providers =& Celini::newORDO('Provider');
		$providers = $providers->valueList('fullName',true);
		$this->assign('providers',$providers);
		$userProfile =& Celini::getCurrentUserProfile();
		$pid = $userProfile->getCurrentPracticeId();
		$r =& Celini::newORDO('Room');
		$patient = ORDataobject::factory('Patient',(int)$patient_id);
		$this->assign("patient",$patient);
		$roomArray = $r->rooms_practice_factory($pid,false);
		if ($patient->get('id') > 0 && !is_array($search)) {
			$this->assign("default_provider",$patient->get('default_provider'));
			$search = array('find_first' => true, 'from' => date('Y-m-d',strtotime(" +1 week")),'to' => date('Y-m-d', strtotime(" +9 days")));
			$rk = array_keys($roomArray);
			$search['facility'] = $rk[0];
			$this->assign("search",$search);
		}
		
		$this->assign("facility",$roomArray);
		return $this->view->render("search.html");
	}

	function processSearch() {
		//$GLOBALS['startSearch'] = microtime(true);
		$appointment =& Celini::newORDO('Appointment');
		$where = array();
		$search = $this->POST->getRaw('Search');
		// Assume today if no start date
		if(empty($search['from'])){
			$search['from']=date('Y-m-d');
		}
		// Assume a week from the start date if no end date
		if(empty($search['to'])){
			$search['to']=date('Y-m-d',strtotime('+1 Week',strtotime($search['from'])));
		}
		if (!isset($search['find_first'])) {
			// Regular search

			$db =& Celini::dbInstance();
			$breakdownsql = "CASE WHEN ab.person_id=".(int)$search['provider']." AND ab.appointment_id=appointment.appointment_id THEN 1 ELSE 0 END";
			$where = array();
			if($search['provider'] != '') {
//				$where[] = " prov.person_id =" .(int)$search['provider'];
				$where[] = " (prov.person_id =" .(int)$search['provider']." OR $breakdownsql )";
			}
			if(!empty($search['facility'])) {
				$where[] = " rooms.id =" .(int)$search['facility'];
			}
			if(!empty($search['patient_id'])) {
				$where[] = " pat.person_id =" .(int)$search['patient_id'];
			}
			if(!empty($search['reason'])) {
				$where[] = " enumeration_value.value =" .(int)$search['reason'];
			}
			if(!empty($search['schedule_code'])) {
				$where[] = " event_group.title =" .$db->quote($search['schedule_code']);
			}
			
			$where[] = " event.start BETWEEN ".$db->quote($search['from'])." AND ".$db->quote($search['to']) ;
			$where[] = " event.event_id NOT IN(SELECT event_id FROM schedule_event)";
			$apts = '';
			$res =& $appointment->search('WHERE '.implode(' AND ',$where));
			$this->view->assign('search',true);
			while ($res && !$res->EOF) {
				$this->view->assign('apt',$res->fields);
				$appointment =& Celini::newORDO('Appointment',$res->fields['appointment_id']);
				$this->view->assign_by_ref('appointment',$appointment);
				$apts.='<br />'.$this->view->render('singlefromarray.html');
				$res->MoveNext();
			}
			$this->view->assign('searchresults',$apts);
			return;
		} else {
			if(strpos($search['time_amount'],':') !== false) {
				list($h,$m) = explode(':',$search['time_amount']);
				$amount=((int)$m+((int)$h*60))*60;
			} else {
				$amount = (int)$search['time_amount'] * 60;
			}
			$sched =& Celini::newORDO('Schedule');
			$ts = $sched->findFirst($search['provider'],$search['facility'],$search['from'],$search['to'],$amount,$search['schedule_code']);
			if(!$ts) {
				$this->messages->addMessage('No scheduled time in that amount found.');
			} else {
				$appLinks = array();
				if (isset($search['patient_id'])) {
					$patient = ORDataobject::factory('Patient',$search['patient_id']);
				}
				$rm = ORDataObject::factory('Room',$search['facility']);
				foreach($ts as $stamp) {
					$appLinks[] = '<a href="' . Celini::link('Day','CalendarDisplay') . 'date='. date('Y-m-d',$stamp). '&patient_id=' . (int)$search['patient_id']  . '">'. date('m/d/Y',$stamp) . '</a> '.
	'<a href="' . Celini::link('Day','CalendarDisplay') . 'date=' . date('Y-m-d',$stamp) . '&start_time=' . date('H:i',$stamp) .'&end_time=' . date('H:i',$stamp+$amount) . '&patient_id='. (int)$search['patient_id'] . '&provider_id=' . (int)$search['provider'] . '&Filter[building][]=' . $rm->get('building_id') . '">Schedule Appointment for ' . date('H:i',$stamp) . ' - ' . date('H:i',$stamp+$amount) . '</a>';
				}
				$this->assign('appLinks',$appLinks);

				$this->assign_by_ref('findfirstProvider',$provider);
			}
		}
	}
	
	function ajax_viewalt($appointmentId) {
		//$event_id=str_replace('elementInfo','',$event_id);
		$appt =& Celini::newORDO('Appointment',$appointmentId);
		$this->view->assign_by_ref('appointment',$appt);
		return $this->view->render('innerappointmentalt.html');
	}
	
	function ajax_process($keysarray,$valuesarray) {
		$data = array();
		foreach($keysarray as $key=>$keyname) {
			$keyname = str_replace('Appointment[','',$keyname);
			$keyname = str_replace(']','',$keyname);
			$data[$keyname] = $valuesarray[$key];
		}
//		trigger_error(print_r($data,true));
		$db =& Celini::dbInstance();
		$user =& $this->_me->get_user();
		$appointmentId = 0;
		if (isset($data['appointment_id'])) {
			$appointmentId = enforceType::int($data['appointment_id']);
		}
		$appointment =& Celini::newOrdo('Appointment',$appointmentId);
		$appointment->populateArray($data);
		if($appointment->get('id') > 0) {
			$appointment->set('last_change_date',date('Y-m-d'));
			$appointment->set('last_change_id',$user->get('id'));
		} else {
			// No need to check for no-shows because the output will show it
			$appointment->set('created_date',date('Y-m-d'));
		}
		$oldappointmentid = $appointment->get('id');
		// Was this event in a schedule?
		$schedule =& Celini::newORDO('ScheduleEvent');
		$sfinder =& $schedule->relationshipFinder();
		$sfinder->addParent(Celini::newORDO('Provider',$appointment->get('provider_id')));
		$sfinder->addCriteria('event.start <= '.$db->quote($appointment->get('start_time')).' AND event.start > '.$db->quote($appointment->get('end_time')));
		$scheds = $sfinder->find();
		if($scheds->count() > 0) {
			$sched =& $scheds->current();
			$sch =& $sched->getParent('Schedule');
			$appointment->set('appointment_code',$sch->get('schedule_code'));
		}
		$appointment->persist();
		$GLOBALS['loader']->requireOnce('includes/CalendarDescription.class.php');
		$conflicts = $appointment->conflicts();
		$datets = strtotime($appointment->get('date'));
		$d = array(date('Y',$datets),date('m',$datets),date('d',$datets));
		$dayIterator =& new CalendarDescriptionDay($d);
		$sql = "SELECT user.color FROM user WHERE user.person_id=".$db->quote($appointment->get('provider_id'));
		$res = $db->execute($sql);
		if($res && !$res->EOF) {
			$color = $res->fields['color'];
			$GLOBALS['loader']->requireOnce('lib/PEAR/Image/Color.php');
			$ic = new Image_Color();
			$ic->setColors($color,$color);
			$ic->changeLightness(-20);
			$border = $ic->_returnColor($ic->color1);
			$ic->changeLightness(40);
			$background = $ic->_returnColor($ic->color1);
			$bgcss = "background-color:#$background;";
		} else {
			$bgcss = '';
		}
		$this->view->assign('eventcss', "top: ".$dayIterator->timestampToPosition(strtotime($appointment->get('date').' '.$appointment->get('start_time')))."px;height:".$dayIterator->timeDifferenceToHeight(strtotime($appointment->get('start_time')),strtotime($appointment->get('end_time')))."px;left:25px;$bgcss");
		
		$this->appointment =& $appointment;
		$this->view->assign_by_ref('appointment',$appointment);
		$event =& $appointment->getParent('CalendarEvent');
		$this->view->assign_by_ref('event',$event);
		$provider =& $event->getParent('Provider');
		$patients =& $event->getChildren('Patient');
		$this->view->assign_by_ref('patients',$patients);
		$this->view->assign_by_ref('provider',$provider);
		$room =& $event->getParent('Room');
		$this->view->assign('ev_edit',true);
		$user =& User::fromPersonId($provider->get('id'));
		$this->view->assign_by_ref('provideruser',$user);
		$p =& $patients->current();
		$prsn =& Celini::newORDO('Person',$p->get('id'));
		$numbers = array_values($prsn->get_numbers());
		$this->view->assign('phone',isset($numbers[0]) ? $numbers[0]['number'] : '');
		if($oldappointmentid > 0) {
			$out = $this->view->render('innerappointment.html');
		} else {
			$out = $this->view->render('singleappointment.html');
		}
		return array(0,$appointment->get('provider_id'),$oldappointmentid > 0 ? $appointment->get('event_id') : 0,$out,$appointment->get('id'));
	}

	/**
	 * This is where we'll be doing all the checks
	 *
	 */
	function check_rules($aptdata) {
		$apt =& Celini::newORDO('Appointment');
                $aptdata = $this->GET->getRaw('Appointment');

                if (isset($aptdata['users']) && count($aptdata['users']) > 0) {
                        $tmp = $aptdata['users'];
                        $aptdata['provider_id'] = array_shift($tmp);
                }
                $apt->populateArray($aptdata);
		return $this->checkRulesObj($apt);
	}

	function checkRulesObj($apt,$showOverrideBox=true)	{
		$alerts = array();

		if($apt->get('id') > 0) {
			$origapt =& Celini::newORDO('Appointment',$apt->get('id'));
			if($origapt->get('patient_id') > 0 && $apt->get('patient_id') < 1) {
				$alerts[] = 'You must provide a patient for an appointment.';
			}
		} elseif($apt->get('patient_id') < 1) {
			$alerts[] = 'You must provide a patient for an appointment.';
		}
		// Alert code
		// First check related for the day.
		$p =& Celini::newORDO('Patient',$apt->get('patient_id'));
		$related = $p->valueList('related_people');
		if(count($related) > 0) {
			$relatedids = array_keys($related);
			$db =& Celini::dbInstance();
			$sql = "SELECT a.appointment_id,a.patient_id,
					DATE_FORMAT(ae.start,'%H:%i') start,
					DATE_FORMAT(ae.end,'%H:%i') end 
				FROM appointment a LEFT JOIN event ae ON a.event_id=ae.event_id
				WHERE a.patient_id IN (".implode(',',$relatedids).") 
				AND a.practice_id=".$db->quote($apt->get('practice_id'))." 
				AND DATE_FORMAT(ae.start,'%Y-%m-%d') = ".$db->quote($apt->get('date'));
			$res = $db->execute($sql);
			while($res && !$res->EOF) {
				$this->view->assign('relapt',$res->fields);
				$rp =& Celini::newORDO('Patient',$res->fields['patient_id']);
				$this->view->assign_by_ref('related',$rp);
				if($rp->get('confidentiality') > 1) {
					$this->view->assign('relatedConf',1);
				} else {
					$this->view->assign('relatedConf',0);
				}
				$alerts[] = $this->view->render('alertrelatedapt.html');
				$res->MoveNext();
			}
		}



		// appointment rules engine checks
		$GLOBALS['loader']->requireOnce('includes/AppointmentRules/AppointmentRuleManager.class.php');
		$ruleMan = new AppointmentRuleManager();
		$override = true;
		if (!$ruleMan->isValid($apt)) {
			$alerts[] = $ruleMan->getMessage();
			$override = $ruleMan->override;
		}

		if(count($alerts) > 0 && $override == true && $showOverrideBox) {
			$alerts[] = $this->view->render('overridecheckbox.html');
		}
		//$alerts[] = 'Debug Debug Debug';
		return $alerts;
	}

	function check_rules_local($aptdata) {
		$apt =& Celini::newORDO('Appointment');
		$aptdata = $this->GET->getRaw('Appointment');

$fp = fopen('/tmp/aptdata.txt', 'w');
fwrite($fp, serialize($aptdata));
fclose($fp);

		if (isset($aptdata['users']) && count($aptdata['users']) > 0) {
			$tmp = $aptdata['users'];
			$aptdata['provider_id'] = array_shift($tmp);
		}
		$apt->populateArray($aptdata);
		$alerts = array();

		if($apt->get('id') > 0) {
			$origapt =& Celini::newORDO('Appointment',$apt->get('id'));
			if($origapt->get('patient_id') > 0 && $apt->get('patient_id') < 1) {
				$alerts[] = 'You must provide a patient for an appointment.';
			}
		} elseif($apt->get('patient_id') < 1) {
			$alerts[] = 'You must provide a patient for an appointment.';
		}
		// Alert code
		// First check related for the day.
		$p =& Celini::newORDO('Patient',$apt->get('patient_id'));
		$related = $p->valueList('related_people');
		if(count($related) > 0) {
			$relatedids = array_keys($related);
			$db =& Celini::dbInstance();
			$sql = "SELECT a.appointment_id,a.patient_id,
					DATE_FORMAT(ae.start,'%H:%i') start,
					DATE_FORMAT(ae.end,'%H:%i') end 
				FROM appointment a LEFT JOIN event ae ON a.event_id=ae.event_id
				WHERE a.patient_id IN (".implode(',',$relatedids).") 
				AND a.practice_id=".$db->quote($apt->get('practice_id'))." 
				AND DATE_FORMAT(ae.start,'%Y-%m-%d') = ".$db->quote($apt->get('date'));
			$res = $db->execute($sql);
			while($res && !$res->EOF) {
				$this->view->assign('relapt',$res->fields);
				$rp =& Celini::newORDO('Patient',$res->fields['patient_id']);
				$this->view->assign_by_ref('related',$rp);
				if($rp->get('confidentiality') > 1) {
					$this->view->assign('relatedConf',1);
				} else {
					$this->view->assign('relatedConf',0);
				}
				$alerts[] = $this->view->render('alertrelatedapt.html');
				$res->MoveNext();
			}
		}



		// appointment rules engine checks
		$GLOBALS['loader']->requireOnce('includes/AppointmentRules/AppointmentRuleManager.class.php');
		$ruleMan = new AppointmentRuleManager();
		$override = true;
		if (!$ruleMan->isValid($apt)) {
			$alerts[] = $ruleMan->getMessage();
			$override = $ruleMan->override;
		}

		if(count($alerts) > 0 && $override == true) {
			$alerts[] = $this->view->render('overridecheckbox.html');
		}
		//$alerts[] = 'Debug Debug Debug';
		return $alerts;
	}

        function ajaxReschedule($from_provider, $appointment_html, $copySchedule= false) {
		$applist = "";
                preg_match_all("/div class=\"eventBody element\" id=\"([0-9]+)\">/",$appointment_html,$appIds);
                if(isset($appIds) && isset($appIds[1]) && count($appIds[1]) > 0) {
                        $this->view->assign("appCount",count($appIds[1]));
                        $this->view->assign("provider",ORDataobject::factory('Person',$from_provider));
                        $this->view->assign("confirmTime",time());
                        $this->view->assign("appList",implode(",",$appIds[1]));
			$prov = ORDataobject::factory('Provider');
                	$provider_list = $prov->valueList_fullName(true);
                	$this->view->assign("providerList",$provider_list);
                }
                else {
                        $this->messages->addMessage('No appointments were found to move.');
                        $this->view->assign("error",true);
                }
                
		preg_match_all("/div schedule_event_id=\"([0-9]+)\"/",$appointment_html,$seIds);
                if(isset($seIds) && isset($seIds[1]) && count($seIds[1]) > 0) {
                        $this->view->assign("scheduleList",implode(",",$seIds[1]));

		}

		return $this->view->render('reschedule.html');
        }
	
	function ajaxRescheduleCheckRules($appointmentIds,$newProviderId) {
		$ids = explode(",",$appointmentIds);
		$alerts = array();
		foreach ($ids as $id) {
			$app = ORDataObject::Factory('Appointment', (int)$id);
			$app->set("provider_id",(int)$newProviderId);
			$ta = $this->checkRulesObj($app, false);
			$alerts = array_merge($alerts,$ta);
		}
		if (count($alerts > 0)) {
			array_unshift($alerts, $this->view->render('overridecheckbox.html'));
		}
		return  $alerts;
	}

	function ajaxRescheduleConfirm($new_provider_id, $appointment_html) {
		$applist = "";
		preg_match_all("/div class=\"eventBody element\" id=\"([0-9]+)\">/",$appointment_html,$appIds);
		if(isset($appIds) && isset($appIds[1]) && count($appIds[1]) > 0) {
			$this->view->assign("appCount",count($appIds[1]));
			$this->view->assign("provider",ORDataobject::factory('Person',$new_provider_id));
			$this->view->assign("confirmTime",time());
			$this->view->assign("appList",implode(",",$appIds[1]));
		}
		else {
			$this->messages->addMessage('No appointments were found to move.');
			$this->view->assign("error",true);
		}


		return $this->view->render('reschedule_confirm.html') . print_r($applist,true);
	}
	
	function ajaxDoReschedule($appIds,$newProviderId,$appointmentOverride,$appointmentOverrideNeeded,$scheduleIds) {
		$scheduleText = '';
		if (strlen($appIds) > 0 && (int)$newProviderId >0) {
			if ($appointmentOverrideNeeded == 1 && $appointmentOverride != 1) {
				$this->view->assign("NOTICE","You must select to override the alerts in order to perform the rescheduling.");
				return $this->view->render('overridecheckbox.html'); 

			}
			else {
				$appIdArray = explode(',',$appIds);
				$counter = 0;
				foreach($appIdArray as $appId) {
					$app = ORDataobject::factory('Appointment',$appId);
					$app->set("provider_id",(int)$newProviderId);
					$app->persist();
					$counter++;
				}

				$s = '';
				$eg = '';
				if (strlen($scheduleIds) >0 ) {
					$csDS = new CopySchedule_DS($scheduleIds);
					for($csDS->rewind();$csDS->valid();$csDS->next()) {
                          		$row = $csDS->get();
                          		$ev = ORDataObject::factory("CalendarEvent");
                          		$ev->set('start',$row['start']);
                          		$ev->set('end',$row['end']);
                          		$ev->set('title',$row['title']);
					$ev->fnord = "event";
                          		$ev->persist();
					if (!is_object($s)) {
                          		$prov = ORDataObject::factory("Person",$newProviderId);
                          		$s = ORDataObject::factory("Schedule");
					$s->set('provider_id',$newProviderId);
					$s->set('title',$prov->get('first_name') . " " .$prov->get('last_name') . "'s temp schedule");
					$s->set('schedule_code',$row['schedule_code']);
					$s->persist();
					}
					if (!is_object($eg)) {
                          		$eg = ORDataObject::factory("EventGroup");
					$eg->set('title',$row['title']);
					$eg->set('room_id',$row['room_id']);
					$eg->set('schedule_id',$s->get('schedule_id'));
					$eg->persist();
					}
					//use direct when you need to make a simple persist to the schedule_event table, the regular scheduleEvent ordo is fancified for complex calendar use
                          		$se = ORDataObject::factory("ScheduleEventDirect");
					$se->set('event_id',$ev->get('event_id'));
					$se->set('event_group_id',$eg->get('event_group_id'));
					$se->persist();
					$scheduleText = "Schedules Copied.";
					
                }


				}
				return $counter . ' Appointment(s) Updated. ' .  $scheduleText. 'Click <a href="javascript:window.location.reload();">here</a> to refresh screen.';

			}
				

		}
		return "There was an error performing the reschedule." . $appIds;
	}



}
?>
