<?php

require_once CELINI_ROOT."/controllers/Controller.class.php";

class C_Default extends Controller {

	var $template_mod;

	function C_Default ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;
	}

	function default_action() {
		return $this->view->render("default.html");
	}


}

?>
