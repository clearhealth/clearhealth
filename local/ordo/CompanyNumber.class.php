<?php
require_once CELINI_ROOT."/ordo/ORDataObject.class.php";
ORdataObject::factory_include('Number');
class CompanyNumber extends Number {
	var $_relation = "company_number";
	var $_fkey = "company_id";

	function setup($id = 0,$parent=false,$parent_type="company") {
		parent::setup($id,$parent,$parent_type);
		$this->_parent[$parent]['phone_relation_type'] = 1;
	}

        function getTypeList() {
                $list = $this->_load_enum('company_number_type',true);
                return array_flip($list);
        }
}
?>
