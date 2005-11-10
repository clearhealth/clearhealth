<?php

require_once CELINI_ROOT . "/ordo/ORDataObject.class.php";

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
	 *	From appointment reasons enumeration
	 *	@var reason_code
	 */
	var $reason_code = '';
	
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

	var $walkin	= '';
	var $group_appointment	= '';
	
	/**
	 *	Creator_id contains the user id of the first user to persist the object
	 *	@var int created_by_id
	 */
	var $creator_id=0;
	
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function Occurence($id = 0)	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();

		//shore up the most basic ORDataObject bits

		$this->event_id = "";
		$this->start = "";
		$this->end = "";
		$this->notes = "";
		$this->location_id = "";
		$this->date = "0000-00-00";
	
		$this->_table = "occurences";

		$this->setup($id);
		
	}

	function setup($id = 0) {
		$this->set('id',$id);
		if ($id > 0) {
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
			if($this->id==0){
				$this->creator_id=$me->get_user_id();
			} else {
				$this->last_change_id = $me->get_user_id();
			}
		}
		parent::persist();
	}
	
	/**
	 * Convenience function to get an array of many objects
	 * 
	 * @param int $foreign_id optional id use to limit array on to a specific relation, otherwise every document object is returned 
	 * @param bool $newer	when newer is true only items from the last 7 days and into the future are shown
	 */
	function occurences_factory($foreign_id = "",$mode=false) {
		
		$occurences = array();
		
		if (empty($foreign_id)) {
			 $foreign_id= "like '%'";
		}
		else {
			$foreign_id= " = '" . mysql_real_escape_string(strval($foreign_id)) . "'";
		}
		
		$o = new Occurence();
		$nsql = "";
		if ($mode === 1) {
			$nsql = " and start > date_sub(now(), INTERVAL 7 DAY) ";
		}
		$sql = "SELECT * FROM  " . $o->_prefix . $o->_table . " WHERE event_id " .$foreign_id . $nsql .
			" ORDER BY " . $o->_prefix . $o->_table . ".start";
		if ($mode === 2) {
			$sql .= " DESC limit 100";
		}

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

	
	/**
	 * Sets the "start" timestamp of this object
	 *
	 * @param	string
	 * @access	protected
	 */
	function set_start($value) {
		$this->_setDate('start', $value);
	}
	
	/**
	 * Returns the "start" timestamp of this object
	 *
	 * @return	string
	 * @access	protected
	 */
	function get_start() {
		return $this->_getTimestamp('start');
	}
	
	
	/**
	 * Returns the "start" date of this object
	 *
	 * @return string
	 * @access protected
	 */
	function get_start_date() {
		return $this->_getDate('start');
	}
	
	/**
	 * An alias for {@link get_start()} left in to insure BC.
	 *
	 * @see		get_start()
	 * @access	protected
	 */
	function get_start_timestamp() {
		return $this->get_start();
	}

	
	/**
	 * Sets the "end" timestamp of this object
	 *
	 * @param	string
	 * @access	protected
	 */
	function set_end($value) {
		$this->_setDate('end', $value);
	}
	
	
	/**
	 * Returns the full timestamp this object's "end" column
	 *
	 * @return	string
	 * @access	protected
	 */
	function get_end() {
		return $this->_getTimestamp('end');
	}
	
	/**
	 * Returns the "end" date of this object
	 *
	 * @return string
	 * @access protected
	 */
	function get_end_date() {
		return $this->_getDate('end');
	}
	
	
	/**
	 * @internal This doesn't look right to me, the single-line comment was the
	 *	original code that I'm replacing.  I don't want to change the behavior
	 *	to what I believe is correct without first having the all clear from
	 *	whoever put it in here this way.
	 */
	function get_end_timestamp() {
		return $this->_getTimestamp('start');
		//return strtotime($this->start);
	}
	
	
	/**
	 * Returns the duration of this object from start to finish
	 *
	 * @return	int
	 * @access	protected
	 * @todo		Implement some sort of time difference system inside 
	 *			TimestampObject.
	 */
	function get_duration() {
		return (strtotime($this->end->toString()) - strtotime($this->start->toString())) / 60;
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
	
	function set_reason_id($value) {
		$this->reason_code = $value;
	}
	
	function get_reason_id() {
		return $this->reason_code;
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

	function get_creator() {
		if (is_object($this->creator)) {
			return $this->creator;	
		}
		$u = new User(null,null);
		if($this->creator_id==0){
			$u->id=$this->last_change_id;
		}else{
			$u->id = $this->creator_id;
		}
		$u->populate();
		$this->creator = $u;
		return $this->creator;
	}

	function get_user_display_name() {
		$user = $this->get_user();
		$person =& ORDataObject::factory('Person',$user->get('person_id'));
		return $person->get('last_name').', '.$person->get('first_name');
	}

	function get_creator_display_name() {
		$user = $this->get_creator();
		$person =& ORDataObject::factory('Person',$user->get('person_id'));
		return $person->get('last_name').', '.$person->get('first_name');
	}

	function get_external_display_name() {
		$person =& ORDataObject::factory('Person',$this->get('external_id'));
		return $person->get('last_name').', '.$person->get('first_name');
	}
	
	function get_location_name() {
		if (!is_object($this->location)) $this->location = ORDataObject::factory('Room',$this->location_id);
		return $this->location->get_name();
	}

	function get_building_name() {
		if (!is_object($this->location)) $this->location = ORDataObject::factory('Room',$this->location_id);
		return $this->location->get('building_name');
	}
	
	
	/**
	 * Sets the internal date of this object
	 *
	 * This field does not exist in storage.  Instead, it is shorthand for
	 * changing just the date portion of the "start" column.
	 *
	 * @param	string
	 * @access	protected
	 */
	function set_date($value) {
		$this->_setDate('date', $value);
	}
	
	
	/**
	 * Retrieves the internal date of this object
	 *
	 * This field does not exist in storage, instead, it is generated based off
	 * of the "start" field's contents.
	 *
	 * @return	string
	 * @access	protected
	 */
	function get_date() {
		if (is_a($this->date, 'TimestampObject')) {
			return $this->_getDate('date');
		}
		elseif (is_a($this->start, 'TimestampObject')) {
			return $this->_getDate('start');
		}
		
		return '';
	}
	
	/**
	 * Sets the start time of this object.
	 *
	 * This field does not exist in storage.  Instead, it is shorthand for
	 * changing just the time portion of the "start" column.
	 *
	 * @param	string
	 * @access	protected
	 */
	function set_start_time($value) {
		switch (gettype($this->date)) {
		case 'object' :
			$this->_setDate('start', $this->_getDate('date') . ' ' . $value);
			break;
		
		// This is here for legacy purposes - in case a date gets set as a 
		// string instead of an object
		case 'string' :
			$this->_setDate('start', $this->date . ' ' . $value);
			break;
		}
	}
	
	
	/**
	 * Returns the start time of this object.
	 *
	 * This field does not exist in storage, instead, it is generated based off
	 * of the "start" field's contents.
	 *
	 * @return	string
	 * @access	protected
	 */
	function get_start_time() {
		if (empty($this->start) || !is_object($this->start)) {
			return '';
		}
		
		$time =& $this->start->getTime();
		return $time->toString('%H:%i');
	}
	
	
	/**
	 * Sets the end time of this object.
	 *
	 * This field does not exist in storage.  Instead, it is shorthand for
	 * changing just the time portion of the "end" column.
	 *
	 * @param	string
	 * @access	protected
	 */
	function set_end_time($value) {
		switch (gettype($this->date)) {
		case 'object' :
			$this->_setDate('end', $this->_getDate('date') . ' ' . $value);
			break;
		
		// This is here for legacy purposes - in case a date gets set as a 
		// string instead of an object
		case 'string' :
			$this->_setDate('end', $this->date . ' ' . $value);
			break;
		}
	}
	
	
	/**
	 * Returns the end time of this object.
	 *
	 * This field does not exist in storage, instead, it is generated based off
	 * of the "end" field's contents.
	 *
	 * @return	string
	 * @access	protected
	 */
	function get_end_time() {
		if (empty($this->end)) {
			return '';
		}
		
		$time =& $this->end->getTime();
		return $time->toString('%H:%i');
	}

	function get_delete_message() {
		$message = "Deleting occurence #".$this->_db->qstr($this->id);
		return $message;
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
