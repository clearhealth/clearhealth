<?php
class C_AppointmentRuleset extends Controller {

	function actionList() {
		$GLOBALS['loader']->requireOnce('datasources/AppointmentRuleset_DS.class.php');
		$ds =& new AppointmentRuleset_DS();
		$grid =& new cGrid($ds);
		$this->view->assign_by_ref('grid',$grid);

		$this->view->assign('ADD_ACTION',Celini::link('add'));

		return $this->view->render('list.html');
	}

	function actionAdd() {
		$session =& Celini::sessionInstance();
		$session->set('AppointmentRuleset:appointment_ruleset_id',0);
		return $this->actionEdit();
	}

	function actionSummary() {
		$session =& Celini::sessionInstance();
		$arId = $session->get('AppointmentRuleset:appointment_ruleset_id');
		$ar =& Celini::newOrdo('AppointmentRuleset',$arId);

		$this->view->assign_by_ref('ar',$ar);

		return $this->view->render('summary.html');
	}

	function _setupRule() {
		$id = $this->GET->getTyped('rule_id','int');

		$rule =& Celini::newOrdo('AppointmentRule',$id);
		$this->view->assign_by_ref('rule',$rule);

		$data = unserialize($rule->get('data'));
		$this->view->assign('data',$data);

		$this->em =& Celini::enumManagerInstance();
		$this->view->assign_by_ref('em',$this->em);
	}

	function actionDate() {
		$this->_setupRule();
		return $this->view->render('date.html');
	}

	function actionProcedure() {
		$this->_setupRule();

		$list =& $this->em->enumList('appointment_reasons',array('listAll'));

		$reasons = array();
		for($list->rewind(); $list->valid(); $list->next()) {
			$reasons[] = get_object_vars($list->current());
		}
		$this->view->assign('reasons',$reasons);

		return $this->view->render('procedure.html');
	}

	function actionPatient() {
		$this->_setupRule();
		return $this->view->render('patient.html');
	}

	function actionProvider() {
		$this->_setupRule();

		$provider =& Celini::newOrdo('Provider');
		$this->view->assign('providers',$provider->valueList('fullPersonId'));

		$list =& $this->em->enumList('person_type');
		$types = array();
		for($list->rewind(); $list->valid(); $list->next()) {
			$row = $list->current();
			if ($row->extra1) {
				$types[$row->key] = $row->value;
			}
		}
		$this->view->assign('types',$types);

		return $this->view->render('provider.html');
	}

	function actionLocation() {
		$this->_setupRule();

		$db = new clniDb();

		$sql = "select id, name from practices order by name";
		$this->view->assign('practices',$db->getAssoc($sql));

		$sql = "select b.id, concat(p.name,' -> ',b.name) name from practices p inner join buildings b on b.practice_id = p.id order by p.name,b.name";
		$this->view->assign('buildings',$db->getAssoc($sql));

		$sql = "select r.id, concat(p.name,' -> ',b.name,' -> ',r.name) name from practices p inner join buildings b on b.practice_id = p.id inner join rooms r on r.building_id = b.id order by p.name,b.name";
		$this->view->assign('rooms',$db->getAssoc($sql));

		return $this->view->render('location.html');
	}

	function actionEdit() {
		$id = $this->GET->getTyped('ruleset_id','int');
		$session =& Celini::sessionInstance();
		if ($id > 0) {
			$session->set('AppointmentRuleset:appointment_ruleset_id',$id);
		}
		$id = $session->get('AppointmentRuleset:appointment_ruleset_id');


		$ar =& Celini::newOrdo('AppointmentRuleset',$id);
		$this->view->assign_by_ref('ar',$ar);

		$this->view->assign('ADD_ACTION',Celini::link('add'));
		$this->view->assign('LIST_ACTION',Celini::link('list'));

		$this->view->assign('contentUrl',substr(Celini::link(false,'AppointmentRuleset',false),0,-1));
		return $this->view->render('layout.html');
	}

	function process($payload) {
		$session =& Celini::sessionInstance();
		$arId = $session->get('AppointmentRuleset:appointment_ruleset_id');

		if ($payload['tab'] != 'summary' && $arId == 0) {
			$ar =& Celini::newOrdo('AppointmentRuleset',$arId);
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
			default:
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
