<?php
$loader->requireOnce('includes/AppointmentRules/AppointmentRuleAbstract.class.php');
$loader->requireOnce('ordo/InsuredRelationship.class.php');
$loader->requireOnce('ordo/InsuranceProgram.class.php');
class AppointmentRuleInsurance extends AppointmentRuleAbstract {

	var $notifyAny = false;

	function isApplicable() {
		return true;
	}

	function isValid() {
		$patientId = $this->appointment->get('patient_id');
		$irArray = InsuredRelationship::fromPersonId($patientId);
		foreach($irArray as $ir) {
			if ($ir->get('insurance_program_id') > 0 && $ir->get('insurance_program_id') == $this->ruleData->insurance_program_id && ($ir->get('program_order') == 0 || $ir->get('program_order')== 1)) {
				$ip = ORDataObject::factory('InsuranceProgram',$this->ruleData->insurance_program_id);
				$this->errorMessage = 'Insurance Program: ' . $ip->get('name');
				return false;
			}
		}
		return true;
	}

}
?>
