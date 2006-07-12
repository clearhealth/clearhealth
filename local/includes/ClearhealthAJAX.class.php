<?php
class ClearhealthAJAX {

	function initActiveFeeSchedule() {
		$GLOBALS['loader']->requireOnce('datasources/FeeSchedule_DS.class.php');
		$ds =& new FeeSchedule_DS();

		$this->server->registerClass($ds,'ActiveFeeSchedule',$ds->ajaxMethods());
	}

	function initPatientFinder() {
		$GLOBALS['loader']->requireOnce('controllers/C_PatientFinder.class.php');
		$pf =& new C_PatientFinder();
		$this->server->registerClass($pf,'PatientFinder',array('SmartSearch'));
	}
	
	function initzipcode(){
		$GLOBALS['loader']->requireOnce('includes/zipserver.php');
		$zipserver=&new zipcode();
		$this->server->registerClass($zipserver,'zipcode',array('getData'));
	}
	
	function initquicksave() {
		$GLOBALS['loader']->requireOnce('includes/QuickSave.class.php');
		$quick =& new QuickSave();
		$this->server->registerClass($quick,'quicksave',array('saveForm','saveItem','loadForm'));
	}
	
	function initappointment(){
		$GLOBALS['loader']->requireOnce('controllers/C_Appointment.class.php');
		$appointment=&new C_Appointment();
		$this->server->registerClass($appointment,'appointment');
	}

	function initcoding(){
		$GLOBALS['loader']->requireOnce('controllers/C_Coding.class.php');
		$coding=&new C_Coding();
		$this->server->registerClass($coding,'coding',array('cpt_search','icd_search','cdt_search','procedure_search','diagnosis_search'));
	}

	function initReport() {
		$report= Celini::newOrdo('Report');
		$this->server->registerClass($report,'Report', $report->ajaxMethods());
	}

	function initMenuReport(){
		$GLOBALS['loader']->requireOnce("ordo/MenuReport.class.php");
		$report=&new MenuReport();
		$this->server->registerClass($report,'MenuReport', $report->ajaxMethods());
	}

	function initMenuForm(){
		$GLOBALS['loader']->requireOnce("ordo/MenuForm.class.php");
		$form=&new MenuForm();
		$this->server->registerClass($form,'MenuForm');
	}

	function initForm(){
		$GLOBALS['loader']->requireOnce("ordo/Form.class.php");
		$form=&new Form();
		$this->server->registerClass($form,'Form');
	}

	function initEncounter() {
		$encounter =& Celini::newOrdo('Encounter');
		$this->server->registerClass($encounter,'Encounter',array('appointmentlist_remoting'));
	}

	function initFeeEstimator() {
		$GLOBALS['loader']->requireOnce("includes/FeeEstimator.class.php");
		$fe = new FeeEstimator();
		$this->server->registerClass($fe,'FeeEstimator',array('standardFeeForCode','standardFeeForCodeId'));
	}
}
?>
