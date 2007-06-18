<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRule.abstract.php');
$loader->requireOnce('includes/ClearhealthCalendarData.class.php');
class AppointmentRuleOutOfSchedule extends AppointmentRule {

	function isApplicable() {
		return true;
	}

	function isValid() {
		$valid = true;
		$db =& new clniDB();
		$start = date('Y-m-d H:i:s',strtotime($this->appointment->get('start')));
		$end = date('Y-m-d H:i:s',strtotime($this->appointment->get('end')));
		$provider_id = $this->appointment->get('provider_id');
		$room_id = (int)$this->appointment->get('room_id');
		$sql = "
		SELECT schedule.schedule_id
		FROM
			schedule
			INNER JOIN event_group USING(schedule_id)
			INNER JOIN schedule_event USING(event_group_id)
			INNER JOIN event on event.event_id = schedule_event.event_id 
		WHERE
			event.start <= ".$db->quote($start)."
			AND event.end >= ".$db->quote($end)."
			AND schedule.provider_id = ".$db->quote($provider_id)."
			AND event_group.room_id = ".$db->quote($room_id)."
		";
		$res = $db->execute($sql);
		if ($res->EOF) {
			$this->errorMessage = "<b>The appointment is not within a schedule.";
			$valid = false;
		}

		return $valid;
	}

	function appointmentSummary($data) {
		return "{$data['patientName']}: {$data['startTime']} to {$data['endTime']}"; 
	}
}
?>
