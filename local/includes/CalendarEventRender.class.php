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

		switch($mode){
			case 'day':
			default:
				if(is_array($event)){
					$appt =& Celini::newORDO('Appointment',$event['appointment_id']);
				} else {
					$appt =& Celini::newOrdo('Appointment',$event->get('id'),'byEventId');
				}
				$view->assign('ev_edit',1);
				$view->assign_by_ref('appointment',$appt);
				return $view->fetch('appointment/general_singleappointment.html');
		}
	}
}
?>
