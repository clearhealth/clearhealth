<?php

require_once CELINI_ROOT."/controllers/Controller.class.php";

class C_Main extends Controller {

	var $template_mod;

	function C_Main ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod; 
		$this->assign("FORM_ACTION", Celini::link(true). $_SERVER['QUERY_STRING']);
		$this->assign("TOP_ACTION",  $_SERVER['SCRIPT_NAME']."/main/");
		if (!isset($GLOBALS['style'])) {
			$GLOBALS['style'] = array();
		}
		$this->assign("STYLE", $GLOBALS['style']);

		if (isset($_GET['set_print_view'])) {
			$this->_print_view = true;
		}
		
	}

	function default_action($display = "") {
		$this->assign("display",$display);
		
		if ($this->_print_view) {
			echo $this->view->fetch("main/" . $this->template_mod . "_print.html");
		}
		else {
			echo $this->view->fetch("main/" . $this->template_mod . "_list.html");
		}
	}

	function location_action($arg) {
		$c = new Controller();
		//this dance is so that the controller system which only cares about the name part of the first two arguments get what it wants
		//and the rest gets passed as normal argument values, really this all goes back to workarounds for problems with call_user_func
		//and value passing

		$fga = func_get_args();
		$fga = array_slice($fga,1);
		$args = array_merge(array("location" => "",$arg => ""),$fga);
		$display = $c->act($args);
		$this->default_action($display);
	}
	
	function calendar_action($arg) {
		$c = new Controller();
		//this dance is so that the controller system which only cares about the name part of the first two arguments get what it wants
		//and the rest gets passed as normal argument values, really this all goes back to workarounds for problems with call_user_func
		//and value passing

		$fga = func_get_args();
		$fga = array_slice($fga,1);
		$tmp = array();
		foreach($fga as $val) {
			$tmp[$val] = $val;
		}
		$args = array_merge(array("calendar" => "",$arg => ""),$tmp);
		$display = $c->act($args);
		$this->default_action($display);
	}
	
	function person_schedule_action($arg) {
		$c = new Controller();
		//this dance is so that the controller system which only cares about the name part of the first two arguments get what it wants
		//and the rest gets passed as normal argument values, really this all goes back to workarounds for problems with call_user_func
		//and value passing

		$fga = func_get_args();
		$fga = array_slice($fga,1);
		$args = array_merge(array("person_schedule" => "",$arg => ""),$fga);
		$display = $c->act($args);
		$this->default_action($display);
	}

	function personSchedule_action($arg) {
		$c = new Controller();
		//this dance is so that the controller system which only cares about the name part of the first two arguments get what it wants
		//and the rest gets passed as normal argument values, really this all goes back to workarounds for problems with call_user_func
		//and value passing

		$fga = func_get_args();
		$fga = array_slice($fga,1);
		$args = array_merge(array("personSchedule" => "",$arg => ""),$fga);
		$display = $c->act($args);
		$this->default_action($display);
	}
	
	function access_action($arg) {
		$c = new Controller();
		//this dance is so that the controller system which only cares about the name part of the first two arguments get what it wants
		//and the rest gets passed as normal argument values, really this all goes back to workarounds for problems with call_user_func
		//and value passing

		$fga = func_get_args();
		$fga = array_slice($fga,1);
		$args = array_merge(array("access" => "",$arg => ""),$fga);
		$display = $c->act($args);
		$this->default_action($display);
	}
	
	function preferences_action($arg) {
		$c = new Controller();
		//this dance is so that the controller system which only cares about the name part of the first two arguments get what it wants
		//and the rest gets passed as normal argument values, really this all goes back to workarounds for problems with call_user_func
		//and value passing

		$fga = func_get_args();
		$fga = array_slice($fga,1);
		$args = array_merge(array("preferences" => "",$arg => ""),$fga);
		$display = $c->act($args);
		$this->default_action($display);
	}

	function report_action($arg) {
		$c = new Controller();
		//this dance is so that the controller system which only cares about the name part of the first two arguments get what it wants
		//and the rest gets passed as normal argument values, really this all goes back to workarounds for problems with call_user_func
		//and value passing

		$fga = func_get_args();
		$fga = array_slice($fga,1);
		$args = array_merge(array("report" => "",$arg => ""),$fga);
		$display = $c->act($args);
		$this->default_action($display);
	}

	function PatientFinder_action($arg) {
		$c = new Controller();
		//this dance is so that the controller system which only cares about the name part of the first two arguments get what it wants
		//and the rest gets passed as normal argument values, really this all goes back to workarounds for problems with call_user_func
		//and value passing

		$fga = func_get_args();
		$fga = array_slice($fga,1);
		$args = array_merge(array("PatientFinder" => "",$arg => ""),$fga);
		$display = $c->act($args);
		echo $display;
	}
}
?>
