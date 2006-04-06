<?php

$loader->requireOnce('ordo/Address.class.php');

class CompanyAddress extends Address {
	var $_relation = "company_address";
	var $_fkey = "company_id";

	function setup($id = 0,$parent = false, $type = "company") {
		//var_dump("new CompanyAddress",get_class($this));
		parent::setup($id,$parent,$type);
	}
}
?>
