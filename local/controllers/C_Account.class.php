<?php

require_once CELLINI_ROOT."/controllers/Controller.class.php";
require_once CELLINI_ROOT."/includes/Grid.class.php";
require_once CELLINI_ROOT."/includes/Datasource_sql.class.php";
require_once APP_ROOT."/local/includes/Datasource_AccountHistory.class.php";
require_once APP_ROOT."/local/includes/Grid_Renderer_AccountHistory.class.php";

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

	function default_action() {
	}
	
	function history_action_view($patient_id) {
		
		$hds =& new Datasource_AccountHistory($this->filters);
		$hds->setup($patient_id);
		$renderer =& new Grid_Renderer_AccountHistory();
		$history_grid =& new cGrid($hds,$renderer);
		$this->assign_by_ref("history_grid",$history_grid);
		
		return $this->fetch(Cellini::getTemplatePath("/account/" . $this->template_mod . "_history.html"));
	}

	function history_action_process($patient_id) {
		$this->filters = $_SESSION['clearhealth']['filters'][get_class($this)] = $_POST['filter'];
		$this->assign("filters",$this->filters);
	}
}

?>
