<?php
/**
 * 
 */
class Room extends ORDataObject{
	
	var $id			= '';
	var $description	= '';
	var $number_seats	= '';
	var $building_id	= '';
	var $building		= '';
	var $name		= '';
	
	var $_table 		= 'rooms';
	var $_internalName	= 'Room';
	var $_key		= 'id';
	
	function Room($id = 0)	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();
		
		if ($id > 0) {
			$this->setup($id);
		}
	}

	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	function genericList() {
		$sql = "select r.id, concat(b.name,' -> ',r.name) name from ".$this->tableName()
			." r inner join buildings b on r.building_id = b.id order by b.name, r.name";
		return $this->dbHelper->getAssoc($sql);
	}
	
	function value_fullname() {
		if (!$this->isPopulated()) {
			return 'Not Set';
		}
		$b =& Celini::newOrdo('Building',$this->get('building_id'));
		return $b->get('name').' -> '.$this->get('name');
	}

	/**
	 * Convenience function to get an array of many objects
	 * 
	 * @param int $foreign_id optional id use to limit array on to a specific relation, otherwise every document object is returned 
	 */
	function rooms_factory($foreign_id = "") {
		$rooms = array();
		
		if (empty($foreign_id)) {
			 $foreign_id= "like '%'";
		}
		else {
			$foreign_id= " = '" . mysql_real_escape_string(strval($foreign_id)) . "'";
		}
		
		$d = new Room();
		$sql = "SELECT id FROM  " . $d->_prefix . $d->_table . " WHERE building_id " .$foreign_id ;
		$result = $d->_Execute($sql);
		
		while ($result && !$result->EOF) {
			$rooms[] = new Room($result->fields['id']);
			$result->MoveNext();
		}

		return $rooms;
	}
	
	/**
	 * Convenience function to get an array of many objects
	 * 
	 * @param mixed $foreign_id optional id use to limit array on to a specific relation, otherwise every document object is returned 
	 */
	function rooms_practice_factory($foreign_id = "",$blank = true) {
		$rooms = array();
		
		if ($blank)
			$rooms[0] = " ";
			
		if(is_array($foreign_id)){
			$practices=$foreign_id;
		} elseif(is_a($foreign_id,'Practice')) {
			$practices = array($foreign_id);
		} else {
			$practices=array(ORDataObject::factory('Practice',$foreign_id));
		}
		foreach($practices as $practice){
			$foreign_id=$practice->get('id');
			if (empty($foreign_id)) {
				 $foreign_id= "like '%'";
			}
			else {
				$foreign_id= " = '" . mysql_real_escape_string(strval($foreign_id)) . "'";
			}
		
			$d = new Room();
			$sql = "SELECT r.id, r.name as room_name, b.name as building_name FROM  " . $d->_prefix . $d->_table . " as r "
			."LEFT JOIN buildings as b on b.id=r.building_id "
			."LEFT JOIN practices as s on  s.id=b.practice_id WHERE s.id " .$foreign_id ;
			$result = $d->_Execute($sql);
		
			while ($result && !$result->EOF) {
				$rooms[$result->fields['id']] = $result->fields['building_name'] . "->" . $result->fields['room_name'];
				$result->MoveNext();
			}
		}
		return $rooms;
	}
	
	/**#@+
	*	Getter/Setter methods used by reflection to affect object in persist/poulate operations
	*	@param mixed new value for given attribute
	*/
	
	function get_building() {
		$b = new Building($this->building_id);
		return $this->building = $b;
	}

	function get_building_name() {
		$b = $this->get_building();
		return $b->get('name');
	}

	function get_delete_message() {
		$string = "Room Name: " . $this->get_name() . "\n";
		$ocs = $this->get_occurences();
		foreach ($ocs as $oc) 	{
			$name = "unspecified event";
			$schedule = "unspecified schedule";
			$e = new Event($oc->get_event_id());
			$c = new Schedule($e->get_foreign_id());
			$ename = $e->get_title();
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
	
	function get_occurences() {
		$occurences = array();
		$sql = "SELECT * from ".$this->_prefix."occurences where location_id =" . $this->_db->qstr($this->id);
		$result = $this->_Execute($sql);
		while ($result && !$result->EOF) {
			$occurences[] = new Occurence($result->fields['id']);	
			$result->MoveNext();
		}	
		return $occurences;
	}
	
	function delete() {
		$sql = "DELETE from " . $this->_prefix . $this->_table . " where id=" . $this->_db->qstr($this->id);
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
	
	
	/** 
	 * Returns whether there are any rooms so we can determine if a room is
	 * the first one to be added.
	 *
	 * @return boolean
	 */
	function roomsExist() {
		$sql = "SELECT COUNT(*) AS total FROM {$this->_prefix}{$this->_table}";
		$result = $this->_db->Execute($sql);
		
		return ($result->fields['total'] > 0);
	}

} // end of Class

?>
