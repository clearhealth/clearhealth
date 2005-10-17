<?php

require_once CELINI_ROOT."/controllers/Controller.class.php";

class C_Admin extends Controller {

	var $template_mod;

	function C_Admin ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;
	}

	function default_action_view() {
		return $this->fetch($GLOBALS['frame']['config']['template_dir'] ."/default/" . $this->template_mod . "_default.html");
	}

	function acl_action_edit() {
		return "<iframe src='{$this->base_dir}Celini/lib/phpgacl/admin/index.php' width='800px' height='700px'></iframe>";
	}


}

?>
