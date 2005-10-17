<?php
require_once CELINI_ROOT."/ordo/ORDataObject.class.php";
ORdataObject::factory_include('Address');
class BuildingAddress extends Address {
	var $_relation = "building_address";
	var $_fkey = "building_id";

	function setup($id = 0,$parent = false, $type = "building") {
		//var_dump("new PersonAddress",get_class($this));
		parent::setup($id,$parent,$type);
	}
}
