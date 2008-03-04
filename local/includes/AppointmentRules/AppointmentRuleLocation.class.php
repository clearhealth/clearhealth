<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRuleAbstract.class.php');
class AppointmentRuleLocation extends AppointmentRuleAbstract {

	function isApplicable() {
		$roomId = $this->appointment->get('room_id');
		switch($this->ruleData->location_type) {
			case 'practice':
				$room =& Celini::newOrdo('room',$roomId);
				$building =& Celini::newOrdo('building',$room->get('building_id'));
				if ($this->ruleData->practice_id == $building->get('practice_id')) {
					return true;
				}
				break;
			case 'building':
				$room =& Celini::newOrdo('room',$roomId);
				if ($this->ruleData->building_id == $room->get('building_id')) {
					return true;
				}
				break;
			case 'room':
				if ($this->ruleData->room_id == $roomId) {
					return true;
				}
				break;
		}
		return false;
	}

	function isValid() {
		return true;
	}
}
?>
