<?php

require_once CELLINI_ROOT."/controllers/Controller.class.php";
require_once CELLINI_ROOT."/includes/Grid.class.php";
require_once CELLINI_ROOT."/includes/Datasource_sql.class.php";
require_once APP_ROOT."/local/includes/Datasource_AccountHistory.class.php";
require_once APP_ROOT."/local/includes/Grid_Renderer_AccountHistory.class.php";

class C_Account extends Controller {

	function C_Account ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;
	}

	function default_action() {
	}
	
	function history_action_view($patient_id) {
		
		$hds =& new Datasource_AccountHistory();
		$hds->setup($patient_id);
		$renderer =& new Grid_Renderer_AccountHistory();
		$history_grid =& new cGrid($hds,$renderer);
		$this->assign_by_ref("history_grid",$history_grid);
		
		return $this->fetch(Cellini::getTemplatePath("/account/" . $this->template_mod . "_history.html"));
	}
}

?>
