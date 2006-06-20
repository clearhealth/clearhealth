<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRule.abstract.php');
$loader->requireOnce('includes/ClearhealthCalendarData.class.php');
class AppointmentRuleDoubleBook extends AppointmentRule {

	function isApplicable() {
		return true;
	}

	function isValid() {
		$cd = new ClearhealthCalendarData();
		$appointments = $cd->appointmentsOverlapping($this->appointment->get('start'),$this->appointment->get('end'));

		$valid = true;


		// build some counts
		$roomCount = array();
		foreach($appointments as $appointment) {
			if (!isset($roomCount[$appointment['room_id']])) {
				$roomCount[$appointment['room_id']] = 0;
			}
			$roomCount[$appointment['room_id']]++;
		}


		// check for room double booking - this looks at number of beds/seats in the room
		$messages = array();
		$roomId = $this->appointment->get('room_id');
		foreach($appointments as $appointment) {
			if ($roomId == $appointment['room_id'] && ($roomCount[$appointment['room_id']] + 1) > $appointment['roomMax']) {
				$room = $appointment['room'];
				$messages[] = $this->appointmentSummary($appointment);
			}
		}
		if (count($messages) > 0) {
			$this->errorMessage = "<b>Conflicting appointments in <i>$room</i>:</b><br>".implode('<br>',$messages);
			$valid = false;
		}


		// check for provider double booking - looks at how many appointments a provider is doing at once
		$messages = array();
		$providerId = $this->appointment->get('provider_id');
		foreach($appointments as $appointment) {
			if ($providerId == $appointment['provider_id']) {
				$provider = $appointment['providerName'];
				$messages[] = $this->appointmentSummary($appointment);
			}
		}
		if (count($messages) > 0) {
			$this->errorMessage = "<b>Conflicting appointments for <i>$provider</i>:</b><br>".implode('<br>',$messages);
			$valid = false;
		}


		return $valid;
	}

	function appointmentSummary($data) {
		return "{$data['patientName']}: {$data['startTime']} to {$data['endTime']}"; 
	}
}
?>
