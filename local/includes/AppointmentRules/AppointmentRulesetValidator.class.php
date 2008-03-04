<?php
class AppointmentRulesetValidator {
	var $rulesetId = false;
	var $rules = array();
	var $messages = array();
	var $errorMessage;
	var $status;
	var $any;
	var $statuses = array();

	function AppointmentRulesetValidator($appointmentRulesetId) {
		$this->rulesetId = EnforceType::int($appointmentRulesetId);

		$this->_populate();
	}

	function isValid($appointment) {
		//var_dump($this->errorMessage);
		$this->status = true;
		foreach($this->rules as $rule) {
			
			$rule->setAppointment($appointment);
			$rule->_validator = &$this;
			if ($rule->isApplicable()) {
				//var_dump('Applicable: '.$rule->label);
				$s = $rule->isValid();
				if (!$s ) {
					$this->status = false;
					$this->statuses[] = 0;
					//var_dump('not valid - '.$rule->label.': '.$rule->getMessage());
					$this->messages[] = $rule->getMessage();
				}
				else {
					$this->statuses[] = true;
					//var_dump($rule->getApplicableMessage());
					$this->messages[] = $rule->getApplicableMessage();
				}
			}
			else {
				$this->statuses[] = true;
				//var_dump('Not Applicable: '.$rule->label);
				//$this->messages[] = 'Not Applicable: '.$rule->label; // enable for debug
				//return true; // disable for debug
			}
		}
		if ($this->any == false) {
		// all rules must be false to return false
		foreach ($this->statuses as $status) {
			if ($status == true) {return true; }
		}
		return false;
		}
		
		$return = true;
		foreach ($this->statuses as $status) {
			if ($status == false) $return = false;
		}
		return $return;
	}

	function getMessage() {
		$message = '<b>'.$this->errorMessage.'</b><ul>';
		foreach($this->messages as $m) {
			if ($m != false) {
				$message .= "<li>$m</li>";
			}
		}
		return $message.'</ul>';
	}

	function _populate() {
		$db = new clniDb();
		$sql = "select * from appointment_ruleset where appointment_ruleset_id = ".$this->rulesetId;
		$res= $db->execute($sql);
		while ($res && !$res->EOF) {
			$this->any = $res->fields['any'];
			$this->errorMessage = $res->fields['error_message'];
			$res->MoveNext();
		}


		$sql = "select * from appointment_rule where appointment_ruleset_id = ".$this->rulesetId;
		$res = $db->execute($sql);
		while($res && !$res->EOF) {

			$class = 'AppointmentRule'.ucfirst($res->fields['type']);
			if (!class_exists($class)) {
				$GLOBALS['loader']->requireOnce('includes/AppointmentRules/'.$class.'.class.php');
			}
			$this->rules[$res->fields['appointment_rule_id']] = new $class($res->fields['label'],unserialize($res->fields['data']));
			$res->MoveNext();
		}

	}
	
	function canOverride() {
		if(Auth::canI('override',$this->rulesetId)) {
			return true;
		}
		return false;
	}

}
?>
