<?php

require_once CELLINI_ROOT."/controllers/Controller.class.php");

class C_Default extends Controller {

	var $template_mod;

	function C_Default ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;
	}

	function default_action() {
		return $this->fetch($GLOBALS['frame']['config']['template_dir'] ."/default/" . $this->template_mod . "_default.html");
	}


}

?>
