<?php

class C_Admin extends Controller {

	var $template_mod;

	function C_Admin ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;

		$this->view->path = 'default';
	}

	function default_action_view() {
		return $this->view->render("default.html");
	}

	function acl_action_edit() {
		return "<iframe src='{$this->base_dir}celini/lib/phpgacl/admin/index.php' width='1024px' height='768px'></iframe>";
	}


}

?>
