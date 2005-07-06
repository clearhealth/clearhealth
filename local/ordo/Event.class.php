<?php

require_once CELLINI_ROOT . "/ordo/ORDataObject.class.php";
require_once APP_ROOT . "/local/ordo/Occurence.class.php";

/**
 * 
 */
 
class Event extends ORDataObject{
	
	/**
	 *	
	 *	@var $id
	 */
	 var $id;

	/**
	 *	
	 *	@var title
	 */
	var $title;
	
	/**
	 *	
	 *	@var description
	 */
	var $description;
	
	/**
	 *	
	 *	@var website
	 */
	var $website;
	
	/**
	 *	
	 *	@var contact_person
	 */
	var $contact_person;
	
	/**
	 *	
	 *	@var email
	 */
	var $email;
	
	/**
	 *	
	 *	@var foreign_id
	 */
	var $foreign_id;
	
	/**
	 *	
	 *	@var occurences
	 */
	var $occurences;

	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function Event($id = "",$load_occurences = true)	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
		
		//shore up the most basic ORDataObject bits
		$this->id = $id;

		$this->title = "";
		$this->description = "";
		$this->website = "";
		$this->contact_person = "";
		$this->email = "";
		$this->foreign_id = "";
		$this->load_occurences = $load_occurences;
		$this->occurences = array();
		
		$this->_table = "events";
		
		if ($id != "") {
			$this->populate();
		}
	}
	
	function populate() {
		parent::populate();
		if ($this->load_occurences) {
			$this->occurences = Occurence::occurences_factory($this->id);
		}
	}

	function persist() {
		parent::persist();
		foreach ($this->occurences as $occurence) {
			$occurence->persist($this->id);
		}
	}
	
	/**
	 * Convenience function to get an array of many objects
	 * 
	 * @param int $foreign_id optional id use to limit array on to a specific relation, otherwise every document object is returned 
	 */
	function events_factory($foreign_id = "") {
		$events = array();
		
		if (empty($foreign_id)) {
			 $foreign_id= "like '%'";
		}
		else {
			$foreign_id= " = '" . mysql_real_escape_string(strval($foreign_id)) . "'";
		}
		
		$e = new Event();
		$sql = "SELECT * FROM  " . $e->_prefix . $e->_table . " WHERE foreign_id " .$foreign_id ;
		$result = $e->_Execute($sql);
		
		$i = 0;
		while ($result && !$result->EOF) {
			$events[$i] = new Event();
			$events[$i]->populate_array($result->fields);
			$i++;
			$result->MoveNext();
		}

		return $events;
	}
	
	/**
	 * Convenience function to get an array of many objects
	 * 
	 * @param int $foreign_id optional id use to limit array on to a specific relation, otherwise every document object is returned 
	 */
	function get_events_between($start,$end,$key_type = "day",$foreign_id = "",$code_filter = "",$code_state=true) {
		$events = array();
		$e = new Event();
		
		if (!empty($foreign_id)) {
			$foreign_id= " AND e.foreign_id = '" . mysql_real_escape_string(strval($foreign_id)) . "'";
		}
		
		if (!empty($code_filter)) {
			if ($code_state) {
					$code_state = " IN ";	
				}
				else {
					$code_state = " NOT IN";
				}
			$in_str = mysql_real_escape_string(strval($code_filter));
			$in_ta = split(",",$in_str);
			$in_str = "'" . implode("','",$in_ta) . "'";
			$code_filter = " AND (c.schedule_code $code_state (" . $in_str . ") ";
			if ($code_state == " NOT IN") {
				$code_filter .= " OR c.schedule_code IS NULL) ";
			}
			else {
				$code_filter .= " ) ";
			}
		}
		
		
		//set default facility
		
		$filters = $_SESSION['calendar']['filters'];
		$filter_sql = "";
		if (is_array($filters)) {
			foreach ($filters as $type => $filter) {
				if (!empty($filter)) {
					switch($type) {
						case 'user': 
							$filter_sql .= " AND (u.user_id = " . $e->_db->qstr($filter) . "  OR c.schedule_code = 'ADM')";
							break;
						case 'location':
							$filter_sql .= " AND o.location_id = " . $e->_db->qstr($filter) . " ";
							break;	
					}
				}
			}
		}
		
		//echo $filter_sql;
		$sql = "SELECT o.*,e.*,c.*, o.id as occurence_id, c.id as schedule_id, UNIX_TIMESTAMP(o.start) as start_ts,UNIX_TIMESTAMP(o.end) as end_ts, "
		." UNIX_TIMESTAMP(DATE_FORMAT(o.start,'%Y-%m-%d')) as start_day, b.name as building, r.name as room, IF(schedule_code = 'PS',1,0) as schedule_sort,"
		." u.color as color, u.nickname as nickname, u2.nickname as last_change_nickname, psn.last_name as p_lastname, psn.person_id as person_id, psn.first_name as p_firstname, DATE_FORMAT(psn.date_of_birth,'%m/%d/%Y') as dob, " 
		." pt.record_number as p_record_number, pt.record_number as p_patient_number, n.number as p_phone, n.active as dnc, rm.name as room_name, "
		." DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(psn.date_of_birth, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(psn.date_of_birth, '00-%m-%d')) AS age, "
		." o.`timestamp` last_change "
		." FROM `".$GLOBALS['frame']['config']['db_name']."`.".$e->_prefix."occurences as o LEFT JOIN `".$e->_prefix."events` as e on o.event_id = e.id LEFT JOIN ".$e->_prefix."schedules as c on c.id = e.foreign_id "
		." LEFT JOIN rooms as rm on c.room_id=rm.id "
		." LEFT JOIN ".$e->_prefix."rooms as r on r.id = o.location_id LEFT JOIN ".$e->_prefix."buildings as b on b.id = r.building_id LEFT JOIN ".$e->_prefix."user as u on u.user_id= o.user_id"
		." LEFT JOIN ".$e->_prefix."user as u2 on u2.user_id = o.last_change_id "
		." LEFT JOIN patient as pt on pt.person_id=o.external_id "
		." LEFT JOIN person as psn on psn.person_id=pt.person_id "
		." LEFT JOIN person_number as p2n on psn.person_id=p2n.person_id "
		." LEFT JOIN number as n on n.number_id=p2n.number_id and n.number_type =" .  "1" //this will be the first value in the number_types enum
		." WHERE o.start BETWEEN '$start'  AND '$end' AND (c.schedule_code != 'NS' OR c.schedule_code IS NULL) $foreign_id $code_filter $filter_sql group by o.id ORDER BY schedule_sort DESC, o.start, o.end";
		//echo $sql . "<br>";

		$result = $e->_Execute($sql);
		
		return Event::event_array_builder($result,$key_type);
	}
	
	/**
	 * Convenience function to get an array of many objects
	 * 
	 * @param string $where_sql 
	 */
	function get_events($where_sql,$key_type = "day") {
		$events = array();
		$e = new Event();
		
		
		$sql = "SELECT o.*,e.*,c.*, o.id as occurence_id, c.id as schedule_id, UNIX_TIMESTAMP(o.start) as start_ts,UNIX_TIMESTAMP(o.end) as end_ts, "
		." UNIX_TIMESTAMP(DATE_FORMAT(o.start,'%Y-%m-%d')) as start_day, b.name as building, r.name as room, IF(schedule_code = 'PS',1,0) as schedule_sort,"
		." u.color as color, u.nickname as nickname, u2.nickname as last_change_nickname, psn.last_name as p_lastname, psn.person_id as person_id, psn.first_name as p_firstname, DATE_FORMAT(psn.date_of_birth,'%m/%d/%Y') as dob, " 
		." pt.record_number as p_record_number, pt.record_number as p_patient_number, n.number as p_phone, n.active as dnc, rm.name as room_name, "
		." DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(psn.date_of_birth, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(psn.date_of_birth, '00-%m-%d')) AS age"
		.", o.`timestamp` last_change "
		." FROM `".$GLOBALS['frame']['config']['db_name']."`.".$e->_prefix."occurences as o LEFT JOIN `".$e->_prefix."events` as e on o.event_id = e.id LEFT JOIN ".$e->_prefix."schedules as c on c.id = e.foreign_id "
		." LEFT JOIN rooms as rm on c.room_id=rm.id "
		." LEFT JOIN ".$e->_prefix."rooms as r on r.id = o.location_id LEFT JOIN ".$e->_prefix."buildings as b on b.id = r.building_id LEFT JOIN ".$e->_prefix."user as u on u.user_id= o.user_id"
		." LEFT JOIN ".$e->_prefix."user as u2 on u2.user_id = o.last_change_id "
		." LEFT JOIN patient as pt on pt.person_id=o.external_id "
		." LEFT JOIN person as psn on psn.person_id=pt.person_id "
		." LEFT JOIN person_number as p2n on psn.person_id=p2n.person_id "
		." LEFT JOIN number as n on n.number_id=p2n.number_id and n.number_type =" .  "1" //this will be the first value in the number_types enum
 		." WHERE $where_sql group by o.id ORDER BY o.start";
		//echo $sql;
	
		$result = $e->_Execute($sql);
		
		return Event::event_array_builder($result,$key_type);
	}
	
	function event_array_builder($result,$key_type) {
		$events = array();
		while ($result && !$result->EOF) {
			$key = null;
			switch($key_type) {
				case "month":
				case "week":
				
					$key = $result->fields['start_day'];
					$key2 = ($result->fields['start_ts'] - ($result->fields['start_ts']%900));
					//echo "end ts " . date("Y-m-d H:i",$result->fields['end_ts']) . " start ts " .date("Y-m-d H:i",$result->fields['start_ts']) . " di " . ceil(($result->fields['end_ts'] - $result->fields['start_ts'])/900) . "<br>";
					$result->fields['duration_increments'] = ceil(($result->fields['end_ts'] - $result->fields['start_ts'])/900);
					$events[$key][$key2][] = $result->fields;
					break;
				case "week_schedule":
					$key = $result->fields['start_day'];
					$key2 = ($result->fields['start_ts'] - ($result->fields['start_ts']%900));
					//echo "end ts " . date("Y-m-d H:i",$result->fields['end_ts']) . " start ts " .date("Y-m-d H:i",$result->fields['start_ts']) . " di " . ceil(($result->fields['end_ts'] - $result->fields['start_ts'])/900) . "<br>";
					$di=ceil(($result->fields['end_ts'] - $result->fields['start_ts'])/900);
					$result->fields['duration_increments'] = 1;
					$result->fields['first_inc'] = true;
					$events[$key][$key2][] = $result->fields;
					$result->fields['first_inc'] = false;
					$result->fields['last_inc'] = false;
					for($i=1;$i<$di;$i++) {	
						if ($i+1 == $di) {
							$result->fields['last_inc'] = true;		
						}
						$events[$key][$key2+($i*900)][] = $result->fields;
					}
					break;
				case "day":
					//floor to the correct 15 minute increment in seconds
					$key = ($result->fields['start_ts'] - ($result->fields['start_ts']%900));
					$di=ceil(($result->fields['end_ts'] - $result->fields['start_ts'])/900);
					$result->fields['duration_increments'] = ceil(($result->fields['end_ts'] - $result->fields['start_ts'])/900);
					$events[$key][] = $result->fields;
					break;
				case "day_schedule":
					//floor to the correct 15 minute increment in seconds
					$key = ($result->fields['start_ts'] - ($result->fields['start_ts']%900));
					$di=ceil(($result->fields['end_ts'] - $result->fields['start_ts'])/900);
					$result->fields['duration_increments'] = 1;
					$result->fields['first_inc'] = true;
					$events[$key][] = $result->fields;
					$result->fields['first_inc'] = false;
					$result->fields['last_inc'] = false;
					for($i=1;$i<$di;$i++) {	
						if ($i+1 == $di) {
							$result->fields['last_inc'] = true;		
						}
						$events[$key+($i*900)][] = $result->fields;
					}
					break;	
				case "day_brief":
					//floor to the correct 15 minute increment in seconds
					$key = ($result->fields['start_ts'] - ($result->fields['start_ts']%900));
					$di=ceil(($result->fields['end_ts'] - $result->fields['start_ts'])/900);
					$result->fields['duration_increments'] = 1;
					$result->fields['first_inc'] = true;
					$events[$key][] = $result->fields;
					break;
				case "find_first":
					$key2 = ($result->fields['start_ts'] - ($result->fields['start_ts']%900));
					//echo "end ts " . date("Y-m-d H:i",$result->fields['end_ts']) . " start ts " .date("Y-m-d H:i",$result->fields['start_ts']) . " di " . ceil(($result->fields['end_ts'] - $result->fields['start_ts'])/900) . "<br>";
					$di=ceil(($result->fields['end_ts'] - $result->fields['start_ts'])/900);
					$events[] = $key2;
					for($i=1;$i<$di;$i++) {	
						$events[] = $key2+($i*900);
					}
					break;
			}
			
			$result->MoveNext();
		}

		return $events;
	}
	
	/**
	 * Convenience function to generate string debug data about the object
	 */
	function toString($html = false) {
		$string .= "\n"
		. "ID: " . $this->id."\n"
		."title:" . $this->title."\n"
		."description:" . $this->description."\n"
		."website:" . $this->website."\n"
		."contact_person:" . $this->contact_person."\n"
		."email:" . $this->email."\n"
		."foreign_id:" . $this->foreign_id."\n"
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
	
	function set_title($value) {
		$this->title = $value;
	}
	function get_title() {
		return $this->title;
	}

	function set_description($value) {
		$this->description = $value;
	}
	function get_description() {
		return $this->description;
	}

	function set_website($value) {
		$this->website = $value;
	}
	function get_website() {
		return $this->website;
	}

	function set_contact_person($value) {
		$this->contact_person = $value;
	}
	function get_contact_person() {
		return $this->contact_person;
	}

	function set_email($value) {
		$this->email = $value;
	}
	function get_email() {
		return $this->email;
	}

	function set_foreign_id($value) {
		$this->foreign_id = $value;
	}
	function get_foreign_id() {
		return $this->foreign_id;
	}
	
	function get_earliest_date() {
		if (isset($this->occurences[0]->start)) {
			return $this->occurences[0]->start;
		}
		return "";		
	}
	
	function get_latest_date() {
		if (isset($this->occurences[count($this->occurences)-1])) {
			return $this->occurences[count($this->occurences)-1]->end;	
		}
		return "";	
	}
	
	function get_delete_message() {
		$string = "Event Name: " . $this->get_title() . "\n";
		$ocs = $this->get_occurences();
		$c = new Schedule($this->get_foreign_id());
		foreach ($ocs as $oc) 	{
			$name = "unspecified event";
			$schedule = "unspecified schedule";
			$ename = $this->get_title();
			$cname = $c->get_name();
			if (!empty($ename)) {
				$name = $ename;
			}
			
			if (!empty($cname)) {
				$schedule = $cname;
			}
			
			$string .= "--Scheduled use: " . $oc->get_start() . " - " . $oc->get_end() . " for " . $name . " schedule " . $schedule . "\n";
		}	
		return $string;
	}
	
	function delete() {
		$sql = "DELETE from " . $this->_prefix .$this->_table . " where id=" . $this->_db->qstr($this->id);
		$result = $this->_db->Execute($sql);
		$result = $this->_db->ErrorMsg();
		$ocs = $this->get_occurences();
		$retval = true;
		foreach ($ocs as $oc) {
			$val = $oc->delete();
			($val && $retval) ? $retval=true: $retval = false;	
		}
		if (empty($result) && $retval) {
			return true;
		}
		return false;
		
	}
	
	function get_occurences($newest = -1) {
		if (empty($this->occurences)) {
			$this->occurences = Occurence::occurences_factory($this->id,$newest);	
		}
		return $this->occurences;	
	}
	
	
	

} // end of Class

?>
