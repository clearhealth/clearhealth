<?php

// manager for extended calendar actions
class M_Calendar extends Manager {

	function process_setFilter($date,$filter) {
		$this->controller->sec_obj->acl_qcheck("usage",$this->controller->_me,"","calendar",$this,false);
		$filters = $_SESSION['calendar']['filters'];
		
		$segments = split("\|",$filter);
		foreach($segments as $segment) {
			$ts = split("/",$segment);
			$subseg[$ts[0]] = $ts[1];
			foreach ($subseg as $type => $seg) {
				$filters[$type] = $seg;	
			}
		}
		
		$_SESSION['calendar']['filters'] = $filters;

		$this->controller->_setupFilterDisplay();
	}
}
?>
