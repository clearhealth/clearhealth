<?php
class AppointmentRuleAbstract {

	var $ruleData = false;
	var $appointment = false;
	var $errorMessage = false;
	var $applicableMessage = false;
	var $label = false;
	var $_validator;

	function AppointmentRuleAbstract($label,$data) {
		$this->label = $label;
		$this->ruleData = $data;
	}

	function setRuleData($ruleData) {
		$this->ruleData = $ruleData;
	}

	function setAppointment($appointment) {
		$this->appointment = $appointment;
	}

	function isValid() {
		return true;
	}

	function isApplicable() {
		return false;
	}

	function getMessage() {
		return $this->errorMessage;
	}

	function getApplicableMessage() {
		return $this->applicableMessage;
	}
}
?>
