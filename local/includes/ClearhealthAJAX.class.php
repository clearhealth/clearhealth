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
}
?>