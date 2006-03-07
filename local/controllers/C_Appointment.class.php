<?php

require_once CELINI_ROOT."/controllers/Controller.class.php";

class C_Appointment extends Controller {

	var $template_mod;

	function C_Appointment ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;
	}

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

	function actionPullList() {

		$r =& Celini::newOrdo('Report','/Appointment/pullList','BySystemName');
		$lastPull = false;

		// were storing the last pull date using storage on the report, so if there isn't a pullList System report setup we can't store it
		// were storing a timestamp as an int, there is no storage for timestamps
		if ($r->isPopulated()) {
			$r->storage_metadata['text']['lastPull'] = true;

			$lastPull = $r->get('lastPull');
			if (!empty($lastPull)) {
				$lastPull = unserialize($lastPull);
			}
		}
		if (!$lastPull) {
			$lastPull = array();
		}

		// get upcoming appointments

		$start = date('Y-m-d H:i:s',strtotime('today 01:01:01'));
		$end = date('Y-m-d H:i:s',strtotime('today 23:59:59'));
		$sql = array();
		$sql['cols'] = "
			e.id event_id,
			pt.record_number,
			p.last_name,
			p.first_name,
			b.name building_name,
			concat(pro.last_name,', ',pro.first_name) provider,
			date_format(o.start,'%m/%d/%Y %H:%i') appointment_time
			";
		$sql['from'] = "
			occurences o
			inner join events e on o.event_id = e.id
			left join user u on o.user_id = u.user_id
			inner join patient pt on o.external_id = pt.person_id
			left join person p on pt.person_id = p.person_id
			left join person pro on u.person_id = pro.person_id
			left join rooms r on o.location_id = r.id
			left join buildings b on r.building_id = b.id
		";

		// build not in
		$notin = "";
		$today = date('Y-m-d');
		if (isset($lastPull[$today]) && count($lastPull[$today]) > 0) {
			$notin = " and e.id not in (".implode($lastPull[$today],',').') ';
		}
		$sql['where']  = "o.start between '$start' and '$end'$notin";

		$ds = new Datasource_sql();
		$ds->setup(Celini::dbInstance(),$sql,
			array(
				'record_number'=>'Record #',
				'last_name'=>'Last Name',
				'first_name'=>'First Name',
				'building_name'=>'Treating Facility',
				'provider'=>'Treating Provider',
				'appointment_time'=>'Appointment Time'
			));
		$grid =& new cGrid($ds);
		$this->view->assign_by_ref('grid',$grid);

		$store = $ds->toArray('event_id','event_id');

		if (!isset($lastPull[$today])) {
			$lastPull = array();
			$lastPull[$today] = $store;
		}
		else {
			$tmp = $lastPull[date('Y-m-d')];
			$lastPull = array();
			$lastPull[$today] = array_merge($tmp,$store);
		}
		$r->set('lastPull',serialize($lastPull));
		$r->persist();

		if (isset($this->noRender) && $this->noRender === true) {
			return "pullList.html";
		}
		return $this->view->render('pullList.html');
		
	}
}
?>
