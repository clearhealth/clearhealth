<?php
class C_AppointmentRuleset extends Controller {

	function actionAdd() {
	}

	function actionSummary() {
		$session =& Celini::sessionInstance();
		$arId = $session->get('AppointmentRuleset:appointment_ruleset_id');
		$ar =& Celini::newOrdo('AppointmentRuleset',$arId);

		$this->view->assign_by_ref('ar',$ar);

		return $this->view->render('summary.html');
	}

	function actionDate() {
		$id = $this->GET->getTyped('rule_id','int');

		$rule =& Celini::newOrdo('AppointmentRule',$id);
		$this->view->assign_by_ref('rule',$rule);

		$data = unserialize($rule->get('data'));
		$this->view->assign('data',$data);

		$em =& Celini::enumManagerInstance();
		$this->view->assign_by_ref('em',$em);

		return $this->view->render('date.html');
	}

	function actionProcedure() {
	}

	function actionPatient() {
	}

	function actionProvider() {
	}

	function actionLocation() {
	}

	function actionEdit() {
		$this->view->assign('contentUrl',substr(Celini::link(false,'AppointmentRuleset',false),0,-1));
		return $this->view->render('layout.html');
	}

	function process($payload) {
		$session =& Celini::sessionInstance();
		$arId = $session->get('AppointmentRuleset:appointment_ruleset_id');

		if ($payload['tab'] != 'summary' && $arId == 0) {
			$ar =& Celini::newOrdo('AppointmentRuleset',$id);
			$ar->set('name','Unknown');
			$ar->persist();
			$session->set('AppointmentRuleset:appointment_ruleset_id',$ar->get('id'));
			$arId = $ar->get('id');
		}

		switch($payload['tab']) {
			case 'summary':
				$id = EnforceType::int($payload['appointment_ruleset_id']);
				$ar =& Celini::newOrdo('AppointmentRuleset',$id);
				$ar->populateArray($payload);
				$ar->persist();

				$session->set('AppointmentRuleset:appointment_ruleset_id',$ar->get('id'));
				break;
			case 'date':
				$id = EnforceType::int($payload['appointment_rule_id']);
				$rule =& Celini::newOrdo('AppointmentRule',$id);
				$rule->populateArray($payload);

				$data = new stdClass();
				foreach($payload['data'] as $key => $val)  {
					$data->$key = $val;
				}
				$rule->set('data',serialize($data));
				$rule->set('appointment_ruleset_id',$arId);
				$rule->persist();

				$this->GET->set('rule_id',$rule->get('id'));
				break;
		}
	}
}
?>
