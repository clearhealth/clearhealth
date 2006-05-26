<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRule.abstract.php');
class AppointmentRuleProcedure extends AppointmentRule {

	function isApplicable() {
		switch($this->ruleData->rule_type) {
			case 'disallowed':
				return true;
			break;
			case 'norep':
				if ($this->appointment->get('reason') == $this->ruleData->procedure) {
					return true;
				}
				return false;
			break;
		}
		return false;
	}

	function isValid() {
		switch($this->ruleData->rule_type) {
			case 'disallowed':
				if ($this->appointment->get('reason') == $this->ruleData->procedure) {
					
					$pro = $this->procedureName($this->ruleData->procedure_id);
					$this->errorMessage = "Procedure <i>$pro</i> isn't allowed";
					return false;
				}
				return true;
			break;
			case 'norep':
				if ($this->appointment->get('reason') == $this->ruleData->procedure) {
					$GLOBALS['loader']->requireOnce('includes/ClearhealthCalendarData.class.php');
					$cd = new ClearhealthCalendarData();

					$start = date('Y-m-d H:i:s',strtotime($this->appointment->get('start_time')));
					$end = date('Y-m-d H:i:s',strtotime($this->appointment->get('end_time')));

					$prevAppointments = $cd->prevAppointments($this->appointment->get('provider_id'),$start,$end);
					foreach($prevAppointments as $na) {
						if ($na['reason'] == $this->ruleData->procedure) {
							$pro = $this->procedureName($this->ruleData->procedure_id);
							$this->errorMessage = "Repetition of procedure <i>$pro</i> isn't allowed";
							return false;
						}
					}
				}
				return true;
		}
		return true;
	}

	function procedureName($id) {
		$em =& Celini::enumManagerInstance();
		$list =& $em->enumList('appointment_reasons',array('listAll'));
		$reasons = array();
		for($list->rewind(); $list->valid(); $list->next()) {
			$row = get_object_vars($list->current());
			$reasons[$row['enumeration_value_id']] = $row['value'];
		}
		return $reasons[$id];
	}
}
?>
