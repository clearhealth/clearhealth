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
			$patient->get('date_of_birth'),
			$today,
			$today
			);
	
		$el =& Celini::newOrdo('EligibilityLog');
		$el->set('patient_id',$patientId);
		$el->set('log_time',date('Y-m-d H:i:s'));
		$el->set('message',$checker->getLastCheckOutput());
		$el->persist();
		return $el->get('message');
	}

	function actionBatchCheck() {
	}
}
?>
