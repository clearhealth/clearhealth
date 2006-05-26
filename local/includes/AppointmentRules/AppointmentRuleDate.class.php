<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRule.abstract.php');
class AppointmentRuleDate extends AppointmentRule {

	var $days = array(0=>'Sn',1=>'M',2=>'T',3=>'W',4=>'Th',5=>'F',6=>'St');

	function isApplicable() {
		$start = strtotime($this->appointment->get('start'));
		if ($this->ruleData->rule_type == 'limit') {
			switch($this->ruleData->date_type) {
				case 'dayofweek':
					if ($this->inDayOfWeek($start)) {
						return true;
					}
					break;
			}
		}
		return false;
	}

	function isValid() {
		$start = strtotime($this->appointment->get('start'));
		$end = strtotime($this->appointment->get('end'));

		switch($this->ruleData->rule_type) {
			case 'limit':
				return true;
			break;
			default:
				switch($this->ruleData->date_type) {
					case 'dayofweek':
						if ($this->inDayOfWeek($start)) {
							return true;
						}
						$days = $this->dayRange();
						$this->errorMessage = "Day of week out of allowable range ($days)";
						return false;
						break;
					default:
						return true;
						break;
				}
			break;
		}
		return false;
	}

	function inDayOfWeek($start) {
		$compare = date('w',$start);
		if (in_array($compare,$this->ruleData->days)) {
			return true;
		}
		return false;
	}

	function dayRange() {
		$days = array();
		foreach($this->ruleData->days as $day) {
			$days[] = $this->days[$day];
		}
		$days = implode(',',$days);
	}
}
?>
