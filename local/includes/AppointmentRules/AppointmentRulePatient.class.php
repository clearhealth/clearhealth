<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRuleAbstract.php');
class AppointmentRulePatient extends AppointmentRule {

	function isApplicable() {
		$patient =& Celini::newOrdo('Patient',$this->appointment->get('patient_id'));
		switch($this->ruleData->patient_type) {
			case 'gender':
				if ($patient->get('gender') == $this->ruleData->gender) {
					$this->applicableMessage = 'Patient gender is '.$patient->value('print_gender');
					return true;
				}
			break;
			case 'age':
				$age = $patient->get('age');
				$agemax = $this->ruleData->age_max;
				if ($agemax == 0) {
					$agemax = 999999;
				}
				if ($age >= $this->ruleData->age_min && $age <= $agemax) {
					$this->applicableMessage = "Patient age is >= {$this->ruleData->age_min} and <= $agemax";
					return true;
				}
			break;
		}
		return false;
	}

	function isValid() {
		return true;
	}
}
?>
