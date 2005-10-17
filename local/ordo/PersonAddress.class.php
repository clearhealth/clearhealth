<?php
require_once CELINI_ROOT."/ordo/ORDataObject.class.php";
ORdataObject::factory_include('Address');
class PersonAddress extends Address {
	var $_relation = "person_address";
	var $_fkey = "person_id";

	function setup($id = 0,$parent = false, $type = "person") {
		//var_dump("new PersonAddress",get_class($this));
		parent::setup($id,$parent,$type);
	}
}
