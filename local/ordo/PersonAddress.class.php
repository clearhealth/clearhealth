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
	
	
	/**
	 * Returns true if this address is tied to multiple people
	 *
	 * @return boolean
	 */
	function value_isMultiple() {
		if (!$this->isPopulated()) {
			return false;
		}
		
		$qAddressId = $this->dbHelper->quote($this->get('address_id'));
		$sql = "SELECT COUNT(*) AS `total` FROM {$this->_relation} WHERE address_id = {$qAddressId}";
		$row = $this->dbHelper->getOne($sql);
		return ($row['total'] > 1);
			
	}
}
