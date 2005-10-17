<?php
require_once CELINI_ROOT."/ordo/ORDataObject.class.php";
ORdataObject::factory_include('Number');
class PersonNumber extends Number {
	var $_relation = "person_number";
	var $_fkey = "person_id";

	function setup($id = 0,$parent=false,$parent_type="person") {
		parent::setup($id,$parent,$parent_type);
	}
}
?>
