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

		return $this->fetch(Celini::getTemplatePath("/appointment/" . $this->template_mod . "_editGroup.html"));
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
}
?>
