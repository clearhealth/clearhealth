<?php

require_once CELLINI_ROOT . "/ordo/ORDataObject.class.php";
require_once APP_ROOT . "/local/ordo/PhoneNumber.class.php";
require_once APP_ROOT . "/local/ordo/Address.class.php";

/**
 * 
 */
 
class Practice extends ORDataObject{
	
	/**
	 *	
	 *	@var name
	 */
	var $name;
	
	/**
	 *	
	 *	@var website
	 */
	var $website;
	
	/**
	 *	
	 *	@var phone_numbers
	 */
	var $phone_numbers;
	
	/**
	 *	
	 *	@var main address
	 */
	var $main_address;
	
	/**
	 *	
	 *	@var secondary address
	 */
	var $secondary_address;
	
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function Practice($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
		
		//shore up the most basic ORDataObject bits
		$this->id = $id;

		$this->name = "";
		$this->website = "";
		$this->addresses = array();
		$this->phone_numbers = array();
		$this->main_address = new Address();
		$this->main_address->set_type("main");
		$this->secondary_address = new Address();
		$this->secondary_address->set_type("secondary");
	
		$this->_table = "practices";
		
		if ($id != "") {
			$this->populate();
		}
	}
	
	function populate() {
		parent::populate();
		$this->main_address = Address::factory_address($this->id,"main");
		$this->secondary_address = Address::factory_address($this->id,"secondary");
		$this->phone_numbers = PhoneNumber::factory_phone_numbers($this->id);
	}

	function persist() {
		parent::persist();
		$this->main_address->persist($this->id);
		$this->secondary_address->persist($this->id);
		foreach ($this->phone_numbers as $phone) {
			$phone->persist($this->id);
		}
	}
	
	/**
	 * Convenience function to get an array of many objects
	 * 
	 * @param int $foreign_id optional id use to limit array on to a specific relation, otherwise every document object is returned 
	 */
	function practices_factory() {
		$practices = array();
		
		$s = new Practice();
		$sql = "SELECT id FROM  " . $s->_prefix . $s->_table;
		$result = $s->_Execute($sql);
		
		while ($result && !$result->EOF) {
			$practices[] = new Practice($result->fields['id']);
			$result->MoveNext();
		}

		return $practices;
	}
	
	/**
	 * Convenience function to generate string debug data about the object
	 */
	function toString($html = false) {
		$string .= "\n"
		. "ID: " . $this->id."\n"
		."name:" . $this->name."\n"
		."website:" . $this->website."\n"
		. "\n";
		if ($html) {
			return nl2br($string);
		}
		else {
			return $string;
		}
	}

	/**#@+
	*	Getter/Setter methods used by reflection to affect object in persist/poulate operations
	*	@param mixed new value for given attribute
	*/
	function set_id($id) {
		$this->id = $id;
	}
	function get_id() {
		return $this->id;
	}
	
	
	function set_name($value) {
		$this->name = $value;
	}
	function get_name() {
		return $this->name;
	}

	function set_website($value) {
		$this->website = $value;
	}
	function get_website() {
		return $this->website;
	}
	
	function get_phone1() {
		foreach($this->phone_numbers as $phone) {
			if ($phone->type == "work") {
				return $phone->get_phone_display();
			}
		}
		return "";
	}
	function set_phone1($phone) {
		$this->_set_number($phone, "work");
	}
	function get_phone2() {
		foreach($this->phone_numbers as $phone) {
			if ($phone->type == "home") {
				return $phone->get_phone_display();
			}
		}
		return "";
	}
	function set_phone2($phone) {
		$this->_set_number($phone, "home");
	}
	function get_fax() {
		foreach($this->phone_numbers as $phone) {
			if ($phone->type == "fax") {
				return $phone->get_phone_display();
			}
		}
		return "";
	}
	function set_fax($phone) {
		$this->_set_number($phone, "fax");
	}
	
	function _set_number($num, $type) {
		$found = false;
		for ($i=0;$i<count($this->phone_numbers);$i++) {
			if ($this->phone_numbers[$i]->type == $type) {
				$found = true;
				$this->phone_numbers[$i]->set_phone($num);
			}
		}
		if ($found == false) {
			$p = new PhoneNumber("",$this->id);
			$p->set_type($type);
			$p->set_phone($num);
			$this->phone_numbers[] = $p;
			//print_r($this->phone_numbers);
			//echo "num is now:" . $p->get_phone_display()  . "<br />";
		}
	}
	
	function set_main_address_line1($line) {
		$this->main_address->set_line1($line);
	}
	function set_main_address_line2($line) {
		$this->main_address->set_line2($line);
	}
	function set_main_city($city) {
		$this->main_address->set_city($city);
	}
	function set_main_state($state) {
		$this->main_address->set_state($state);
	}
	function set_main_zip($zip) {
		$this->main_address->set_zip($zip);
	}
	
	function set_secondary_address_line1($line) {
		$this->secondary_address->set_line1($line);
	}
	function set_secondary_address_line2($line) {
		$this->secondary_address->set_line2($line);
	}
	function set_secondary_city($city) {
		$this->secondary_address->set_city($city);
	}
	function set_secondary_state($state) {
		$this->secondary_address->set_state($state);
	}
	function set_secondary_zip($zip) {
		$this->secondary_address->set_zip($zip);
	}
	
	function get_delete_message() {
		$string = "Practice Name: " . $this->get_name() . "\n";
		$buildings = $this->get_buildings();
		foreach ($buildings as $building) 	{
			$string .= $building->get_delete_message();
		}	
		return $string;
	}
	
	function get_buildings() {
		$buildings = array();
		$sql = "SELECT * from ".$this->_prefix."buildings where practice_id =" . $this->_db->qstr($this->id);
		$result = $this->_Execute($sql);
		while ($result && !$result->EOF) {
			$buildings[] = new Building($result->fields['id']);	
			$result->MoveNext();
		}	
		return $buildings;
	}
	
	function delete() {
		$sql = "DELETE from " . $this->_prefix . $this->_table . " where id=" . $this->_db->qstr($this->id);
		$result = $this->_db->Execute($sql);
		$result = $this->_db->ErrorMsg();
		$buildings = $this->get_buildings();
		$retval = true;
		foreach ($buildings as $building) {
			$val = $building->delete();
			($val && $retval) ? $retval=true: $retval = false;	
		}
		if (empty($result) > 0 && $retval) {
			return true;
		}
		return false;
	}
	
	

} // end of Class

?>
