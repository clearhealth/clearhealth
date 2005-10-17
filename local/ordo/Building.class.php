<?php

require_once CELINI_ROOT . "/ordo/ORDataObject.class.php";
ORdataObject::factory_include('Address');
ORdataObject::factory_include('BuildingAddress');
/**
 * 
 */
 
class Building extends ORDataObject{
	
	/**
	 *	
	 *	@var $id
	 */
	 var $id;
	
	/**
	 *
	 *	@var description
	 */
	var $description;
	
	/**
	 *	
	 *	@var name
	 */
	var $name;
	
	/**
	 *	
	 *	@var practice_id
	 */
	var $practice_id;
	
	/**
	 *
	 *	@var address
	 */
	var $address;

	var $identifier = '';
	
	/**
	 * The ID of the facility code of this
	 *
	 * @var int
	 */
	var $facility_code_id = '';


    /**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function Building($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
		
		//shore up the most basic ORDataObject bits
		$this->id = $id;

		$this->description = "";
		$this->name = "";
		$this->practice_id = "";
		$this->address = new BuildingAddress();
		$this->address->set_type("main");

		$this->_table = "buildings";
		
		if ($id != "") {
			$this->populate();
		}
	}

	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}
	
	function populate() {
		parent::populate();
		settype($this->id,'int');
		$res = $this->_execute("select address_id from building_address where building_id = $this->id");
		$address_id = 0;
		if (isset($res->fields['address_id'])) {
			$address_id = $res->fields['address_id'];
		}
		$this->address =& ORDataObject::factory('BuildingAddress',$address_id,$this->id);
        //$ba = new BuildingAddress();
        //$this->address = $ba->addressList($this->id);
	}

	function persist() {
		parent::persist();
		//reinitialize address with this buildings id on add case 
		if ($this->address->id == 0) {
			$this->address->setup(0,$this->id,"building");
		}
		$this->address->persist();
	}
	
	/**
	 * Convenience function to get an array of many objects
	 * 
	 * @param int $foreign_id optional id use to limit array on to a specific relation, otherwise every document object is returned 
	 */
	function buildings_factory($foreign_id = "") {
		$buildings = array();
		
		if (empty($foreign_id)) {
			 $foreign_id= "like '%'";
		}
		else {
			$foreign_id= " = '" . mysql_real_escape_string(strval($foreign_id)) . "'";
		}
		
		$d = new Building();
		$sql = "SELECT id FROM  " . $d->_prefix . $d->_table . " WHERE practice_id " .$foreign_id ;
		$result = $d->_Execute($sql);

		while ($result && !$result->EOF) {
			$buildings[] = new Building($result->fields['id']);
			$result->MoveNext();
		}

		return $buildings;
	}
	
	/**
	 * Convenience function to generate string debug data about the object
	 */
	function toString($html = false) {
		$string .= "\n"
		. "ID: " . $this->id."\n"
		."description:" . $this->description."\n"
		."name:" . $this->name."\n"
		."practice_id:" . $this->practice_id."\n"
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
	
	
	function set_description($value) {
		$this->description = $value;
	}
	function get_description() {
		return $this->description;
	}

	function set_name($value) {
		$this->name = $value;
	}
	function get_name() {
		return $this->name;
	}

	function set_practice_id($value) {
		$this->practice_id = $value;
	}
	function get_practice_id() {
		return $this->practice_id;
	}
	
	function set_address_line1($line) {
		$this->address->set_line1($line);
	}
	function set_address_line2($line) {
		$this->address->set_line2($line);
	}
	function set_city($city) {
		$this->address->set_city($city);
	}
	function set_state($state) {
		$this->address->set_state($state);
	}
	function set_zip($zip) {
		$this->address->set_zip($zip);
	}
	
	function get_delete_message() {
		$string = "Building Name: " . $this->get_name() . "\n";
		$rooms = $this->get_rooms();
		foreach ($rooms as $room) 	{
			$string .= $room->get_delete_message();
			
		}	
		return $string;
	}
	
	function get_rooms() {
		$rooms = array();
		$sql = "SELECT * from " . $this->_prefix . "rooms where building_id =" . $this->_db->qstr($this->id);
		$result = $this->_Execute($sql);
		while ($result && !$result->EOF) {
			$rooms[] = new Room($result->fields['id']);	
			$result->MoveNext();
		}	
		return $rooms;
	}
	
	function delete() {
		$sql = "DELETE from " . $this->_prefix . $this->_table . " where id=" . $this->_db->qstr($this->id);
		$result = $this->_db->Execute($sql);
		$result = $this->_db->ErrorMsg();
		$rooms = $this->get_rooms();
		$retval = true;
		foreach ($rooms as $room) {
			$val = $room->delete();
			($val && $retval) ? $retval=true: $retval = false;	
		}
		if (empty($result) && $retval) {
			return true;
		}
		return false;
	}

	function getBuildingList() {
		$res = $this->_execute("select id, name from $this->_prefix$this->_table order by name");
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['id']] = $res->fields['name'];
			$res->moveNext();
		}
		return $ret;
	}

	function toArray() {
		$ret = array();
		$ret['name'] = $this->get('name');
		$ret['address'] = $this->address->toArray();
		$ret['identifier'] = $this->get('identifier');

		return $ret;
	}

	function getRoomList() {
		$res = $this->_execute("select r.id, concat_ws('->',b.name,r.name) name from $this->_table b inner join rooms r on building_id = b.id order by b.name, r.name");
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['id']] = $res->fields['name'];
			$res->moveNext();
		}
		return $ret;
	}

} // end of Class

?>
