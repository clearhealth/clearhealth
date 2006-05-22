<?php
class AppointmentRuleset {
	var $rulesetId = false;
	var $rulesets = array();

	function AppointmentRuleset($appointmentRulesetId) {
		$this->rulesetId = $appointmentRulesetId;

		$this->_populate();
	}

	function isValid($appointment) {
		return false;
	}

	function getMessage() {
	}

	function _populate() {
	}
}
?>
