<?php

require_once CELINI_ROOT . "/controllers/Manager.class.php";

class M_Main extends Manager {
	function preProcess() {
		$me =& Me::getInstance();
		$user =& $me->get_user();
		if(isset($_GET['changepractice'])){
			$_SESSION['defaultpractice']=$_GET['changepractice'];
		}
		if(!isset($_SESSION['defaultpractice'])){
			$_SESSION['defaultpractice']=$user->get_DefaultPracticeId();
		}
	}

	function postProcess() {
		$patient_id = $this->controller->get('patient_id','c_patient');
		if ($patient_id > 0) {
			$patient =& ORDataObject::factory('Patient',$patient_id);
			$this->controller->assign_by_ref('selectedPatient',$patient);
		}
	}
}
?>
