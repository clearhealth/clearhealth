<?php

class CalendarEventRender {

	function CalendarEventRender() {
		$colors = array();
		$user =& Celini::newORDO('User');
		$colors = $user->valueList('color');
	}
	function prepare() {
	}
	function render(&$event, $mode){

		$view = new clniView();
		$view->caching = true;
		// Cache for 15 minutes
		$view->cache_lifetime = 900;
		switch($mode){
			case 'day':
			default:
				if(is_array($event)){
					$appt =& Celini::newORDO('Appointment',$event['appointment_id']);
				} else {
					$appt =& Celini::newOrdo('Appointment',$event->get('id'),'byEventId');
				}
				if($view->is_cached('general_singleappointment.html',$appt->get('patient_id'),$appt->get('id'))) {
					return $view->fetch('general_singleappointment.html',$appt->get('patient_id'),$appt->get('id'));
				}
				$view->assign('ev_edit',1);
				$view->assign_by_ref('appointment',$appt);
				$innerappt = $view->fetch('appointment/general_innerappointment.html',$appt->get('patient_id'),$appt->get('id'));
				$view->assign('innerappt',$innerappt);
				return $view->fetch('appointment/general_singleappointment.html',$appt->get('patient_id'),$appt->get('id'));
		}
	}
}
?>
