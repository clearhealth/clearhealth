<?php
$GLOBALS['loader']->requireOnce('controllers/C_PatientFinder.class.php');

class C_VistaPatientFinder extends Controller {

	function actionFind() {
		$pat = ORDataObject::factory("Patient");
		$this->view->assign("pat",$pat);
		return $this->view->render("find.html");
	}
	function ajaxFind($searchString) {
		//$resArray[] = array("id" =>1,"pubpid"=>1234, "name" => "blah blah", "DOB" => "11/11/1111", "ss" =>"123123", "person_type"=>1,"string"=>"thisis a test");
		$oldPatientFinder = new C_PatientFinder();
		$resArray = $oldPatientFinder->SmartSearch($searchString);
		return $resArray;
	}
	function ajaxPatientDetailBlock($patientId) {
		$pat = ORDataObject::factory("Patient",(int)$patientId);
		$this->view->assign("pat",$pat);
		return $this->view->render("patientDetail.html");
	}
}
?>
