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
		$view->assign('mode',$mode);
		if(is_array($event)){
			$appt =& Celini::newORDO('Appointment',$event['appointment_id']);
		} else {
			$appt =& Celini::newOrdo('Appointment',$event->get('id'),'byEventId');
		}
		$cache_id = $appt->get('patient_id').'-'.$appt->get('id');
		$appt->_event =& Celini::newORDO('CalendarEvent',$appt->get('event_id'));
		switch($mode){
			case 'day':
			case 'week':
			default:
				if($view->is_cached('general_singleappointment.html',$cache_id."-$mode")) {
					return $view->fetch('general_singleappointment.html',$cache_id."-$mode");
				}
				$view->assign('ev_edit',1);
				$view->assign_by_ref('appointment',$appt);
				$innerappt = $view->fetch('appointment/general_innerappointment.html',$cache_id);
				$view->assign('innerappt',$innerappt);
				return $view->fetch('appointment/general_singleappointment.html',$cache_id."-$mode");
		}
	}
}
?>
