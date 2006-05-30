<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRule.abstract.php');
class AppointmentRuleProvider extends AppointmentRule {

	function isApplicable() {
		if ($this->excludeCheck()) {
			return false;
		}
		switch($this->ruleData->provider_type) {
			case 'single':
				if ($this->appointment->get('provider_id') == $this->ruleData->provider_id) {
					$provider =& Celini::newOrdo('Provider',$this->appointment->get('provider_id'));
					$this->applicableMessage = 'Provider is '.$provider->value('name');
					return true;
				}
				break;
			case 'type':
				$provider =& Celini::newOrdo('Provider',$this->appointment->get('provider_id'));
				if ($provider->get('type') == $this->ruleData->provider_type_id) {
					$em =& Celini::enumManagerInstance();
					$this->applicableMessage = 'Provider has a type of <i>'.$em->lookup('person_type',$this->ruleData->provider_type_id).'</i>';
					return true;
				}
				break;
		}
		return false;
	}

	function isValid() {
		return true;
	}

	function excludeCheck() {
		switch($this->ruleData->provider_type) {
			case 'single':
				if ($this->appointment->get('provider_id') == $this->ruleData->provider_id) {
					return true;
				}
				break;
			case 'type':
				$provider =& Celini::newOrdo('Provider',$this->appointment->get('provider_id'));
				if ($provider->get('type') == $this->ruleData->provider_type_id) {
					return true;
				}
				break;
		}
		return false;
	}
}
?>
