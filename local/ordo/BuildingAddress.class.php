<?php

$loader->requireOnce('ordo/Address.class.php');

class BuildingAddress extends Address {
	var $_relation = "building_address";
	var $_fkey = "building_id";

	function setup($id = 0,$parent = false, $type = "building") {
		//var_dump("new PersonAddress",get_class($this));
		parent::setup($id,$parent,$type);
	}
}
