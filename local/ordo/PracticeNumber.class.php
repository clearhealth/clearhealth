<?php
require_once CELINI_ROOT."/ordo/ORDataObject.class.php";
ORdataObject::factory_include('Number');
class PracticeNumber extends Number {
	var $_relation = "practice_number";
	var $_fkey = "practice_id";

	function setup($id = 0,$parent=false,$parent_type="practice") {
		parent::setup($id,$parent,$parent_type);
	}
}
?>
