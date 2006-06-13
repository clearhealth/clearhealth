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
						$this->applicableMessage = "Day is ".date('l',$start);
						return true;
					}
					break;
				case 'dayofmonth':
					$monthDay = $this->ruleData->monthday;
					if ($this->isMonthDay($monthDay)) {
						if ($monthDay == 'last') {
							$this->applicableMessage = 'Day is the end of the month';
						}
						else {
							$this->applicableMessage = "Day is the ".date('jS',$start) .' of the month';
						}
						return true;
					}
					break;
				case 'lastofday':
					if ($this->isLastBlock()) {
						$length = $this->ruleData->lunch_time_block_length * 60;
						$time = $this->formatSeconds($length);
						$this->applicableMessage = "Last $time of day";
						return true;
					}
					break;
				case 'lastbeforelunch':
					if ($this->isLastBeforeLunch()) {
						$length = $this->ruleData->lunch_time_block_length * 60;
						$time = $this->formatSeconds($length);
						$this->applicableMessage = "Last block ($time) before lunch";
						return true;
					}
					break;
			}
			return false;
		}
		$this->applicableMessage = false;
		return true;
	}

	function getSchedules() {
		$providerId = $this->appointment->get('provider_id');
		$GLOBALS['loader']->requireOnce('includes/ClearhealthCalendarData.class.php');
		$cd = new ClearhealthCalendarData();
		$schedules = $cd->scheduleByProviderDay($providerId,$this->appointment->get('start'));

		if (!isset($schedules[$providerId]) || count($schedules[$providerId]) == 0) {
			// if no schedule were not applicable
			return false;
		}
		return $schedules;
	}

	function formatSeconds($seconds) {
		$mins = $seconds/60;
		if ($mins == 60) {
			return '1h';
		}
		if ($mins > 60) {
			floor($mins/60).'h '.($mins%60).' m';
		}
		return $mins.'m';
	}

	function isValid() {
		$start = strtotime($this->appointment->get('start'));
		$end = strtotime($this->appointment->get('end'));

		switch($this->ruleData->rule_type) {
			case 'limit':
				return true;
			break;
			case 'enforcepos':
				switch($this->ruleData->date_type) {
					case 'dayofweek':
						if ($this->inDayOfWeek($start)) {
							return true;
						}
						$days = $this->dayRange();
						$this->errorMessage = "Day of week out of allowable range ($days)";
						return false;
						break;
					case 'dayofmonth':
						$monthDay = $this->ruleData->monthday;
						if ($this->isMonthDay($monthDay)) {
							return true;
						}
						else {
							$this->errorMessage = "Day of month not allowed";
							return false;
						}
						break;
					case 'lastofday':
						if (!$this->isLastBlock()) {
							$time = $this->formatSeconds($length);
							$this->errorMessage = "Appointment not in last $time of day";
							return false;
						}
						return true;
						break;
					case 'lastbeforelunch':
						if (!$this->isLastBeforeLunch()) {
							$time = $this->formatSeconds($length);
							$this->errorMessage = "Appointment not in last block ($time) before lunch";
							return false;
						}
						return true;
						break;
					default:
						return true;
						break;
				}
			break;
			case 'enforceneg':
				switch($this->ruleData->date_type) {
					case 'dayofweek':
						if ($this->inDayOfWeek($start)) {
							$days = $this->dayRange();
							$this->errorMessage = "Day of week in excluded range ($days)";
							return false;
						}
						return true;
						break;
					case 'dayofmonth':
						$monthDay = $this->ruleData->monthday;
						if (!$this->isMonthDay($monthDay)) {
							return true;
						}
						else {
							$this->errorMessage = "Day of month excluded";
							return false;
						}
						break;
					case 'lastofday':
						if ($this->isLastBlock()) {
							$length = $this->ruleData->lunch_time_block_length * 60;
							$time = $this->formatSeconds($length);
							$this->errorMessage = "Appointment in last $time of day";
							return false;
						}
						return true;
					case 'lastbeforelunch':
						if ($this->isLastBeforeLunch()) {
							$length = $this->ruleData->lunch_time_block_length * 60;
							$time = $this->formatSeconds($length);
							$this->errorMessage = "Appointment in last block ($time) before lunch";
							return false;
						}
						return true;
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
		return $days;
	}

	function isMonthDay($monthDay) {
		$start = strtotime($this->appointment->get('start'));
		if ($monthDay == 'last') {
			// this is kinda hackish but works
			$monthDay = date('d',
				strtotime(
					'-1 day',
					strtotime(
						date('Y').'-'.
						(date('m')+1).
						'-1'
					)
				)
			);
		}

		if ($monthDay == date('d',$start)) {
			return true;
		}
		return false;
	}

	function isLastBlock() {
		$start = strtotime($this->appointment->get('start'));
		$providerId = $this->appointment->get('provider_id');
		$schedules = $this->getSchedules();
		if (!$schedules) {
			return false;
		}
		
		$length = $this->ruleData->last_time_block_length * 60;
		$start = strtotime($this->appointment->get('start'));
		$schedule = array_pop($schedules[$providerId]);
		if ($start >= ($schedule['end'] - $length)) {
			return true;
		}
		return false;
	}

	function isLastBeforeLunch() {
		$start = strtotime($this->appointment->get('start'));
		$schedules = $this->getSchedules();
		if (!$schedules) {
			return false;
		}
		
		$providerId = $this->appointment->get('provider_id');
		$length = $this->ruleData->lunch_time_block_length * 60;
		$start = strtotime($this->appointment->get('start'));
		if (count($schedules[$providerId]) > 1) {
			$schedule = array_shift($schedules[$providerId]);
			if ($start >= ($schedule['end'] - $length)) {
				return true;
			}
		}
		return false;
	}
}
?>
