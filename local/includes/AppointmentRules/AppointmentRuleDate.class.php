<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRule.abstract.php');
class AppointmentRuleDate extends AppointmentRule {

	var $days = array(0=>'Sn',1=>'M',2=>'T',3=>'W',4=>'Th',5=>'F',6=>'St');

	function isApplicable() {
		if ($this->ruleData->rule_type == 'limit') {
			var_dump($this->appointment->toString());
		}
		return true;
	}

	function isValid() {
		$start = strtotime($this->appointment->get('start_time'));
		$end = strtotime($this->appointment->get('end_time'));

		switch($this->ruleData->date_type) {
			case 'dayofweek':
				$compare = date('w',$start);
				if (in_array($compare,$this->ruleData->days)) {
					return true;
				}
				$days = array();
				foreach($this->ruleData->days as $day) {
					$days[] = $this->days[$day];
				}
				$days = implode(',',$days);
				$this->errorMessage = "Day of week out of allowable range ($days)";
				return false;
				break;
			default:
				return true;
				break;
		}
	}
}
?>
