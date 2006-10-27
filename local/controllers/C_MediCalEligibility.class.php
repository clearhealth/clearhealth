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
		// grab patient_id's for all of tomorrows patients
		$serviceDate = 'tomorrow';
		if ($this->GET->exists('serviceDate')) {
			$serviceDate = $this->GET->get('serviceDate');
		}
		$tomorrow = date('Y-m-d',strtotime($serviceDate));
		$sql = "select 
				a.patient_id, p.identifier, date_format(p.date_of_birth,'%m/%d/%Y') date_of_birth
			from appointment a
			inner join event using(event_id) 
			inner join person p on a.patient_id = p.person_id
			where date_format(start,'%Y-%m-%d') = '$tomorrow'";

		$db = new clniDb();
		$res = $db->execute($sql);


		$checker = new MediCalEligibilityChecker();
		$checker->login();


		$today = date('m/d/Y');
		$tomorrow = date('m/d/Y',strtotime($serviceDate));
		while($res && !$res->EOF) {
			$checker->checkEligibility(
				$res->fields['identifier'],
				$res->fields['date_of_birth'],
				$today,
				$tomorrow
				);
			$el =& Celini::newOrdo('EligibilityLog');
			$el->set('patient_id',$res->fields['patient_id']);
			$el->set('log_time',date('Y-m-d H:i:s'));
			$el->set('message',$checker->getLastCheckOutput());
			$el->persist();

			$res->MoveNext();
		}

	}
}
?>
