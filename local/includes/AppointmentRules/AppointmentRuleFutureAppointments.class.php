<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRuleAbstract.class.php');
$loader->requireOnce('includes/ClearhealthCalendarData.class.php');
class AppointmentRuleFutureAppointments extends AppointmentRuleAbstract {

	function isApplicable() {
		return true;
	}

	function isValid() {
		$valid = true;
		$db =& new clniDB();
		$patient_id = $this->appointment->get('patient_id');
		//administrative appointments have no patient
		if (!$patient_id >0 ) {
			return true;
		}
		$sql = "
		SELECT DATE_FORMAT(ev.start,'%m/%d/%Y') as 'start'
		FROM
			appointment ap
			INNER JOIN event ev on ev.event_id = ap.event_id
		WHERE
			ev.start > now() 
			and ev.end < (now() + INTERVAL 1 YEAR) 
			and ap.patient_id = $patient_id
		";
		$res = $db->execute($sql);
		$appMsgs = array();
		while ($res && !$res->EOF) {
			$valid = false;
			$appMsgs[] = $res->fields['start'];
			$res->moveNext();
			$valid = false;
		} 
		if ($valid == false) {	
			$this->errorMessage = "<b>This patient has " .count($appMsgs). " future appointments.<br>" .implode(",",$appMsgs);
		}
		return $valid;
	}

	function appointmentSummary($data) {
		return "{$data['patientName']}: {$data['startTime']} to {$data['endTime']}"; 
	}
}
?>
