<?php
$loader->requireOnce('includes/MediCalEligibilityChecker.class.php');
class C_MediCalEligibility extends Controller {

	function actionSingleCheck() {
		$patientId = $this->GET->getTyped('patient_id','int');

		$patient =& Celini::newOrdo('Patient',$patientId);

		$checker = new MediCalEligibilityChecker();

		$today = date('m/d/Y');
		$checker->login();
		$checker->checkEligibility(
			$patient->get('identifier'),
			$patient->get('dob'),
			$today,
			$today
			);
	
		return $checker->getLastCheckOutput();
	}
}
?>
