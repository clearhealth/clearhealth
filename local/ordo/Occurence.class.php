<?php

require_once CELLINI_ROOT . "/ordo/ORDataObject.class.php";

/**
 * 
 */
 
class Occurence extends ORDataObject{
	
	/**
	 *	
	 *	@var $id
	 */
	 var $id;

	/**
	 *	
	 *	@var event_id
	 */
	var $event_id;
	
	/**
	 *	
	 *	@var start
	 */
	var $start;
	
	/**
	 *	
	 *	@var end
	 */
	var $end;
	
	/**
	 *	
	 *	@var notes
	 */
	var $notes;
	
	/**
	 *	
	 *	@var location_id
	 */
	var $location_id;
	
	/**
	 *	Used to track a related external enitity, usually a person
	 *	@var external_id
	 */
	var $external_id;
	
	/**
	 *	
	 *	@var location
	 */
	var $location;
	
	/**
	 *	Temporary holder for the date part of what will become the start and end times. This is because ususally the UI only
	 *	collects the date once even though we use it for the start and end
	 *	@var string location_id
	 */
	var $date;
	
	/**
	 *	Holds the id of a user which can be associated with this occurence
	 *	@var int user_id
	 */
	var $user_id;
	
	/**
	 *	Holds the user_object for the user_id
	 *	the getter caches the object until persist is called
	 *	@var object user
	 */
	var $user;
	
	/**
	 *	Last change id contains the user id of the last user to persist the object
	 *	@var int last_change_id
	 */
	var $last_change_id;
	
	
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function Occurence($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();

		//shore up the most basic ORDataObject bits
		$this->id = $id;

		$this->event_id = "";
		$this->start = "";
		$this->end = "";
		$this->notes = "";
		$this->location_id = "";
		$this->date = "0000-00-00";
	
		$this->_table = "occurences";
		
		if ($id != "") {
			$this->populate();
		}
	}
	
	/**
	 * Overloaded method to set last_change id
	 */
	function persist() {
		$this->user = null;
		$me =& Me::getInstance();
		if ($me->get_user_id() > 0) {
			$this->last_change_id = $me->get_user_id();
		}
		parent::persist();
	}
	
	/**
	 * Convenience function to get an array of many objects
	 * 
	 * @param int $foreign_id optional id use to limit array on to a specific relation, otherwise every document object is returned 
	 */
	function occurences_factory($foreign_id = "") {
		
		$occurences = array();
		
		if (empty($foreign_id)) {
			 $foreign_id= "like '%'";
		}
		else {
			$foreign_id= " = '" . mysql_real_escape_string(strval($foreign_id)) . "'";
		}
		
		$o = new Occurence();
		$sql = "SELECT * FROM  " . $o->_prefix . $o->_table . " WHERE event_id " .$foreign_id . " ORDER BY " . $o->_prefix . $o->_table . ".start";
		$result = $o->_Execute($sql);
		
		$i =0;
		while ($result && !$result->EOF) {
			$occurences[$i] = new Occurence();
			$occurences[$i]->populate_array($result->fields);
			$i++;
			$result->MoveNext();
		}

		return $occurences;
	}
	
	/**
	 * Convenience function to generate string debug data about the object
	 */
	function toString($html = false) {
		$string .= "\n"
		. "ID: " . $this->id."\n"
		."event_id:" . $this->event_id."\n"
		."start:" . $this->start."\n"
		."end:" . $this->end."\n"
		."notes:" . $this->notes."\n"
		."location_id:" . $this->location_id."\n"
		."user_id:" . $this->user_id."\n"
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
		
	function set_event_id($value) {
		$this->event_id = $value;
	}
	function get_event_id() {
		return $this->event_id;
	}

	function set_start($value) {
		$this->start = $value;
	}
	function get_start() {
		return $this->start;
	}
	
	function get_start_timestamp() {
		return strtotime($this->start);
	}

	function set_end($value) {
		$this->end = $value;
	}
	function get_end() {
		return $this->end;
	}
	
	function get_end_timestamp() {
		return strtotime($this->start);
	}
	
	function get_duration() {
		return (strtotime($this->end) - strtotime($this->start))/60;
	}

	function set_notes($value) {
		$this->notes = $value;
	}
	function get_notes() {
		return $this->notes;
	}

	function set_location_id($value) {
		$this->location_id = $value;
	}
	
	function get_location_id() {
		return $this->location_id;
	}
	
	function set_external_id($value) {
		$this->external_id = $value;
	}
	
	function get_external_id() {
		return $this->external_id;
	}
	
	function set_last_change_id($value) {
		$this->last_change_id = $value;
	}
	
	function get_last_change_id() {
		return $this->last_change_id;
	}
	
	function set_user_id($value) {
		$this->user_id = $value;
	}
	
	function get_user_id() {
		return $this->user_id;
	}
	
	function get_user() {
		if (is_object($this->user)) {
			return $this->user;	
		}
		$u = new User(null,null);
		$u->id = $this->user_id;
		$u->populate();
		$this->user = $u;
		return $this->user;
	}
	
	function get_location_name() {
		if (!is_object($this->location)) $this->location = new Room($this->location_id);
		return $this->location->get_name();
	}
	
	function set_date($value) {
		if (strpos($value,"/") == 2 || strpos($value,"/") == 1) {
			preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/",$value,$matches);
			$this->date = $matches[3] . "-" . $matches[1] . "-" . $matches[2];	
		}
		elseif (strpos($value,"-") == 2 && 	preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})/",$value)) {
			preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})/",$value,$matches);
			if (count($matches) == 4) {
				$this->date = $matches[3] . "-" . $matches[1] . "-" . $matches[2];
			}	
		}
		elseif (strpos($value,"-") == 2 && 	preg_match("/([0-9]{4})-([0-9]{1,2})\/([0-9]{1,2})/",$value,$matches)) {
			preg_match("/([0-9]{4})-([0-9]{1,2})\/([0-9]{1,2})/",$value,$matches);
			if (count($matches) == 4) {
				$this->date = $matches[3] . "-" . $matches[1] . "-" . $matches[2];	
			}
		}
	}
	function get_date() {
		if (!empty($this->start)) {
			return date("m/d/Y",strtotime($this->start));
		}
		return "";	
	}
	function set_start_time($value) {	
		$this->start = $this->date . " " . $value;
	}
	function get_start_time() {
		if (!empty($this->start)) {
			return date("H:i",strtotime($this->start));
		}
		return "";	
	}
	function set_end_time($value) {
		$this->end = $this->date . " " . $value; 
	}
	function get_end_time() {
		if (!empty($this->end)) {
			return date("H:i",strtotime($this->end));
		}
		return "";	
	}
	
	function delete() {
		$sql = "DELETE from " . $this->_prefix . $this->_table . " where id=" . $this->_db->qstr($this->id);
		$this->_db->Execute($sql);
		$result = $this->_db->ErrorMsg();
		if (empty($result)) {
			return true;
		}
		return false;
	}


} // end of Class

?>
