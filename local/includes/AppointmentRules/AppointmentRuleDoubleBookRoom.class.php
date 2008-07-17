<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRuleAbstract.class.php');
$loader->requireOnce('includes/ClearhealthCalendarData.class.php');
class AppointmentRuleDoubleBookRoom extends AppointmentRuleAbstract {

	function isApplicable() {
		return true;
	}

	function isValid() {
		$cd = new ClearhealthCalendarData();
		$appointments = $cd->appointmentsOverlapping($this->appointment->get('start'),$this->appointment->get('end'));

		$valid = true;

		$appId = $this->appointment->get('id');

		//this is an edit rather than an add, if times are unchanged then return true
		if ($appId > 0) {
			$dbApp = ORDataObject::factory("Appointment",$appId);
			if ($dbApp->get('start') == $this->appointment->get("start") 
			&& $dbApp->get('end') == $this->appointment->get("end")) {
				return $valid;
			}
		}

		// build some counts
		$roomCount = array();
		foreach($appointments as $key => $appointment) {
			if ($appointment['appointment_id'] == $appId) {
				unset($appointments[$key]);
				//continue;

			}
			if (!isset($roomCount[$appointment['room_id']])) {
				$roomCount[$appointment['room_id']] = 0;
			}
			$roomCount[$appointment['room_id']]++;
		}

		//trigger_error(print_r($roomCount,true));

		// check for room double booking - this looks at number of beds/seats in the room
		$messages = array();
		$roomId = $this->appointment->get('room_id');
		$room = '';
		foreach($appointments as $appointment) {
			if ($roomId == $appointment['room_id'] && ($roomCount[$appointment['room_id']] + 1) > $appointment['roomMax']) {
				if ($appointment['appointment_code'] == "CAN") continue;
				$room = $appointment['room'];
				$messages[] = $this->appointmentSummary($appointment);
			}
		}
		if (count($messages) > 0) {
			$this->errorMessage = "<b>Conflicting appointments in Room <i>$room</i>:</b><br>".implode('<br>',$messages);
			$valid = false;
		}


		return $valid;
	}

	function appointmentSummary($data) {
		return "{$data['patientName']}: {$data['startTime']} to {$data['endTime']}"; 
	}
}
?>
