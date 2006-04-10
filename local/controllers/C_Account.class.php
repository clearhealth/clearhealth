<?php

$loader->requireOnce("controllers/Controller.class.php");
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce("includes/Datasource_AccountHistory.class.php");
$loader->requireOnce("includes/Grid_Renderer_AccountHistory.class.php");
$loader->requireOnce("datasources/AccountNote_DS.class.php");
$loader->requireOnce("lib/PEAR/HTML/AJAX/Serializer/JSON.php");

/**
 * Actions for working with an Account in Clearhealth, in this context this is a Patients Account
 *
 * Current contains an procedure/payment report
 */
class C_Account extends Controller {

	var $filters = "";

	function C_Account ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;

		if (!isset($_SESSION['clearhealth']['filters'][get_class($this)])) {
			$_SESSION['clearhealth']['filters'][get_class($this)] = array();
		}
		$this->filters = $_SESSION['clearhealth']['filters'][get_class($this)];
		$this->assign("filters",$this->filters);
	}

	function actionHistory_view($patient_id) {
		
		$hds =& new Datasource_AccountHistory($this->filters);
		$hds->setup($patient_id);
		$renderer =& new Grid_Renderer_AccountHistory();
		$history_grid =& new cGrid($hds,$renderer);
		$this->assign_by_ref("history_grid",$history_grid);

		$building =& ORDataObject::Factory('Building');
		$this->assign_by_ref('building',$building);

		$ip =& ORDataObject::Factory('InsuranceProgram');
		$this->assign_by_ref('insuranceProgram',$ip);

		$nds =& new AccountNote_DS($patient_id);
		$tmp = $nds->toArray();

		$data = array();
		foreach($tmp as $row) {
			$row['note'] = nl2br($row['note']);
			if ($row['claim_id'] == 0) {
				$data['general'][] = $row;
			}
			else {
				$data[$row['claim_id']][] = $row;
			}
		}

		$serializer = new HTML_AJAX_Serializer_Json();
		$this->view->assign('notes',$serializer->serialize($data));


		$an =& Celini::newOrdo('AccountNote');
		$this->assign_by_ref('accountNote',$an);

		$this->assign('FORM_ACTION',celini::link(true,true,true,$patient_id));
		
		return $this->view->render("history.html");
	}

	function processHistory_view($patient_id) {
		if ($this->POST->exists('filter')) {
			$this->filters = $_SESSION['clearhealth']['filters'][get_class($this)] = $_POST['filter'];
		}
		$this->assign("filters",$this->filters);

		if ($this->POST->exists('account_note')) {
			$an =& Celini::newOrdo('AccountNote');
			$an->populateArray($this->POST->get('account_note'));
			$an->set('patient_id',$patient_id);
			$an->set('user_id',$this->_me->get_id());
			$an->set('date_posted',date('Y-m-d H:i:s'));
			$an->persist();
		}
	}
}

?>
