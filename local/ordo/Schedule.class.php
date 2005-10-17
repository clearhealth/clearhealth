<?php

require_once CELINI_ROOT . "/ordo/ORDataObject.class.php";
require_once APP_ROOT . "/local/ordo/Event.class.php";

/**
 * 
 */
 
class Schedule extends ORDataObject{
	
	/**
	 *	
	 *	@var $id
	 */
	 var $id;

	/**
	 *	
	 *	@var schedule_code
	 */
	var $schedule_code;
	
	/**
	 *	
	 *	@var name
	 */
	var $name;
	
	/**
	 *	
	 *	@var description_long
	 */
	var $description_long;
	
	/**
	 *	
	 *	@var description_short
	 */
	var $description_short;
	
	/**
	 *	
	 *	@var events
	 */
	var $events;
	
	/**
	 *	
	 *	@var practice_id
	 */
	var $practice_id;
	
	/**
	 *	
	 *	@var user_id
	 */
	var $user_id;
	
	/**
	 *	
	 *	@var user_id
	 */
	var $room_id;
	
	
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function Schedule($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
		
		//shore up the most basic ORDataObject bits
		$this->id = $id;

		$this->schedule_code = "";
		$this->name = "";
		$this->description_long = "";
		$this->description_short = "";
		$this->events = array();
	
		$this->_table = "schedules";
		
		if ($id != "") {
			$this->populate();
		}
	}
	

	function setup($id = "") {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
		
	}
	function populate() {
		parent::populate();
		$this->events = Event::events_factory($this->id);
	}

	function fromUserId($user_id) {
		settype($user_id,'int');

		$s =& ORDataObject::factory('Schedule');

		$ret = array();

		$res = $s->_execute("select id from $s->_prefix$s->_table where user_id = $user_id");
		while($res && !$res->EOF) {
			$ret[] = ORDataObject::factory('Schedule',$res->fields['id']);
			$res->MoveNext();
		}
		return $ret;
	}

	function persist() {
		parent::persist();
		foreach ($this->events as $event) {
			$event->persist($this->id);
		}
	}
	
	
	/**
	 * Convenience function to get an array of many objects
	 * 
	 * @param int $foreign_id optional id use to limit array on to a specific relation, otherwise every document object is returned 
	 */
	function schedules_factory($foreign_id = "") {
		$schedules = array();
		
		if (empty($foreign_id)) {
			 $foreign_id= "like '%'";
		}
		else {
			$foreign_id= " = '" . mysql_real_escape_string(strval($foreign_id)) . "'";
		}
		
		$c = new Schedule();
		$sql = "SELECT id FROM  " . $c->_prefix . $c->_table;
		$result = $c->_Execute($sql);
		
		while ($result && !$result->EOF) {
			$schedules[] = new Schedule($result->fields['id']);
			$result->MoveNext();
		}

		return $schedules;
	}
	
	/**
	 * Convenience function to generate string debug data about the object
	 */
	function toString($html = false) {
		$string .= "\n"
		. "ID: " . $this->id."\n"
		."schedule_code:" . $this->schedule_code."\n"
		."name:" . $this->name."\n"
		."description_long:" . $this->description_long."\n"
		."description_short:" . $this->description_short."\n"
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
	
	function set_schedule_code($value) {
		$this->schedule_code = $value;
	}
	function get_schedule_code() {
		return $this->schedule_code;
	}

	function set_name($value) {
		$this->name = $value;
	}
	function get_name() {
		return $this->name;
	}

	function set_description_long($value) {
		$this->description_long = $value;
	}
	function get_description_long() {
		return $this->description_long;
	}

	function set_description_short($value) {
		$this->description_short = $value;
	}
	function get_description_short() {
		return $this->description_short;
	}
	
	function set_practice_id($value) {
		$this->practice_id = $value;
	}
	function get_practice_id() {
		return $this->practice_id;
	}
	
	function set_user_id($value) {
		$this->user_id = $value;
	}
	function get_user_id() {
		return $this->user_id;
	}
	
	function set_room_id($value) {
		$this->room_id = $value;
	}
	function get_room_id() {
		return $this->room_id;
	}
	
	function get_events() {
		return $this->events;	
	}
	
	function get_delete_message() {
		$string = "Schedule Name: " . $this->get_schedule_code() . "-" . $this->get_name() . "\n";
		$evs = $this->get_events();
		foreach ($evs as $ev) 	{
			$string .= $ev->get_delete_message();
		}	
		return $string;
	}
	
	function delete() {
		$sql = "DELETE from " . $this->_prefix . $this->_table . " where id=" . $this->_db->qstr($this->id);
		$result = $this->_db->Execute($sql);
		$result = $this->_db->ErrorMsg();
		$evs = $this->get_events();
		$retval = true;
		foreach ($evs as $ev) {
			$val = $ev->delete();
			($val && $retval) ? $retval=true: $retval = false;	
		}
		if (empty($result) && $retval) {
			return true;
		}
		return false;
	}


} // end of Class

?>
