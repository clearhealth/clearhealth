<?php
ORdataObject::factory_include('Address');
ORdataObject::factory_include('PracticeAddress');
ORdataObject::factory_include('PersonNumber');
ORdataObject::factory_include('PhoneNumber');

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
	 * Phone types
	 */
	var $phone_types;
	
	/**
	 * Contains the identifier (generally an EIN) for a given practice
	 *
	 * @var string
	 */
	var $identifier = '';
	
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
		$this->main_address =& new PracticeAddress();
		$tlist = array_flip($this->main_address->getTypeList());
		if(isset($tlist['Main'])) {
			$this->main_address->set_type($tlist["Main"]);
		}

		$this->secondary_address =& new PracticeAddress();
		if(isset($tlist['Secondary'])) {
			$this->secondary_address->set_type($tlist["Secondary"]);
		}

		$this->_table = "practices";

		$number =& ORDataobject::factory('Number');
		$this->phone_types = array_flip($number->getTypeList());

		if ($id != "") {
			$this->populate();
		}
	}

	function populate() {
		parent::populate();

		$tlist = array_flip($this->main_address->getTypeList());
		$res = $this->_execute("select * from practice_address where practice_id = ".(int)$this->id);
		while($res && !$res->EOF) {
			switch ($res->fields['address_type']) { 
				case $tlist['Main']:
					$this->main_address->setup($res->fields['address_id'],$res->fields['practice_id']);
					break;
				case $tlist['Secondary']:
					$this->secondary_address->setup($res->fields['address_id'],$res->fields['practice_id']);
					break;
			}
			$res->MoveNext();
		}

		$res = $this->_execute("select * from practice_number where practice_id = ".(int)$this->id);
		while($res && !$res->EOF) {
			$this->phone_numbers[] = ORDataObject::factory('PracticeNumber',$res->fields['number_id'],$res->fields['practice_id']);
			$res->MoveNext();
		}
	}

	function persist() {
		parent::persist();
		if ($this->main_address->get('id') == 0) {
			$this->main_address->setup(0,$this->get('id'));
		}
		if ($this->secondary_address->get('id') == 0) {
			$this->secondary_address->setup(0,$this->get('id'));
		}
		$this->main_address->persist();
		$this->secondary_address->persist();

		foreach(array_keys($this->phone_numbers) as $key) {
			if ($this->phone_numbers[$key]->get('id') == 0) {
				$this->phone_numbers[$key]->setup(0,$this->get('id'));
			}
			$this->phone_numbers[$key]->persist();
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
	
	/**
	 * Alias to set_id() to avoid JS reserved word conflict
	 */
	function set_practice_id($id) {
		$this->set_id($id);
	}
	
	/**
	 * Alias to get_id() to avoid JS reserved word conflict
	 */
	function get_practice_id() {
		return $this->get_id;
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
			if ($phone->get('number_type') == $this->phone_types["Work"]) {
				return $phone->get('number');
			}
		}
		return "";
	}
	function set_phone1($phone) {
		$this->_set_number($phone, $this->phone_types["Work"]);
	}
	function get_phone2() {
		foreach($this->phone_numbers as $phone) {
			if ($phone->get('number_type') == $this->phone_types["Home"]) {
				return $phone->get('number');
			}
		}
		return "";
	}
	function set_phone2($phone) {
		$this->_set_number($phone, $this->phone_types["Home"]);
	}
	function get_fax() {
		foreach($this->phone_numbers as $phone) {
			if ($phone->get('number_type') == $this->phone_types["Fax"]) {
				return $phone->get('number');
			}
		}
		return "";
	}
	function set_fax($phone) {
		$this->_set_number($phone, $this->phone_types["Fax"]);
	}

	function _set_number($num, $type) {
		$found = false;
		for ($i=0;$i<count($this->phone_numbers);$i++) {
			if ($this->phone_numbers[$i]->get('number_type') == $type) {
				$found = true;
				$this->phone_numbers[$i]->set('number',$num);
			}
		}
		if ($found == false) {
			$p = ORDataObject::Factory('PracticeNumber',0,$this->get('id'));
			$p->set('number_type',$type);
			$p->set('number',$num);
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
		$this->main_address->set_postal_code($zip);
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
		$this->secondary_address->set_postal_code($zip);
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
	
	function get_addresses() {
		$a = new PracticeAddress();
		return $a->addressList($this->id);
	}

	function get_numbers() {
		$p = new PersonNumber();
		return $p->numberList($this->id);
	}

	function get_billingAddress() {
		$order = array("Billing","Main");
		$addrs = $this->get('addresses');

		$id = false;
		foreach($order as $type) {
			foreach($addrs as $addr) {
				if ($addr['type'] == $type) {
					$id = $addr['address_id'];
					break;
				}
			}
		}

		if (!$id) {
			if (count($addrs) > 0) {
				$tmp = array_shift($addrs);
				$id = $tmp['address_id'];
			}
		}
		return Celini::newOrdo('Address',$id);
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Employee with this
	 */
	function setup($id = 0) {
		$this->set('id',$id);
		if ($id > 0) {
			$this->populate();
		}
	}

	function toArray() {
		$ret = array();
		$ret['name'] = $this->get('name');
		$ret['address'] = $this->main_address->toArray();
		$ret['phone_number'] = $this->get_phone1();
		$ret['identifier'] = $this->get('identifier');
		return $ret;
	}

	function &get_config(){
		require_once APP_ROOT.'/local/includes/PracticeConfig.class.php';
		$config =& Celini::configInstance('practice');
		$config->loadPractice($this->get('id'));
		return $config;
	}

} // end of Class

?>
