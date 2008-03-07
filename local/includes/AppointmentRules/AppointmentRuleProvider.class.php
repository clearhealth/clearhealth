<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRuleAbstract.class.php');
class AppointmentRuleProvider extends AppointmentRuleAbstract {

	function isApplicable() {
		if ($this->excludeCheck()) {
			return true;
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
		if ($this->excludeCheck()) {
			$em =& Celini::enumManagerInstance()
;
                        $this->errorMessage = 'Provider has a type of <i>'.$em->lookup('person_type',$this->ruleData->provider_type_id).'</i>';
			return false;
		}
		return true;
	}

	function excludeCheck() {
		if (!isset($this->ruleData->rule_type) || $this->ruleData->rule_type == 'include') {
			return false;
		}
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
