<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRule.abstract.php');
class AppointmentRuleProvider extends AppointmentRule {

	function isApplicable() {
		switch($this->ruleData->provider_type) {
			case 'single':
				if ($this->appointment->get('provider_id') == $this->ruleData->provider_id) {
					return true;
				}
				return false;
				break;
		}
		return false;
	}

	function isValid() {
		return true;
	}
}
?>
