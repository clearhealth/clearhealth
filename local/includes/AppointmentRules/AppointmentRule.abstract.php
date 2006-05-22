<?php
class AppointmentRule {

	var $ruleData = false;
	var $appointment = false;
	var $errorMessage = false;

	function setRuleData($ruleData) {
		$this->ruleData = $ruleData;
	}

	function setAppointment($appointment) {
	}

	function isValid() {
	}

	function isApplicable() {
	}

	function getMessage() {
		return $this->errorMessage;
	}
}
?>
