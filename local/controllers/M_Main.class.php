<?php

require_once CELLINI_ROOT . "/controllers/Manager.class.php";

class M_Main extends Manager {
	function postProcess() {
		$patient_id = $this->controller->get('patient_id','c_patient');
		if ($patient_id > 0) {
			$patient =& ORDataObject::factory('Patient',$patient_id);
			$this->controller->assign_by_ref('selectedPatient',$patient);
		}
	}
}
?>
