<?php

$loader->requireOnce("/controllers/Controller.class.php");
$loader->requireOnce("/includes/Grid.class.php");
$loader->requireOnce("/includes/Datasource_sql.class.php");
$loader->requireOnce("/local/includes/Datasource_AccountHistory.class.php");
$loader->requireOnce("/local/includes/Grid_Renderer_AccountHistory.class.php");

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
		
		return $this->fetch(Celini::getTemplatePath("/account/" . $this->template_mod . "_history.html"));
	}

	function processHistory_view($patient_id) {
		$this->filters = $_SESSION['clearhealth']['filters'][get_class($this)] = $_POST['filter'];
		$this->assign("filters",$this->filters);
	}
}

?>
