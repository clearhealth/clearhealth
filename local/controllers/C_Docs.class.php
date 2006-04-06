<?php

class C_Docs extends Controller {

	var $template_mod;

	function C_Docs ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;
	}

	function api_action() {
		header("Location: $this->base_dir/Celini/docs/");
	}


}

?>
