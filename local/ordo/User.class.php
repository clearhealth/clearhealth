<?php

require_once CELLINI_ROOT . "/ordo/ORDataObject.class.php";
define("DEFAULT_USER_ID",0);

/**
*	This class is a data model object for representation of user information. Not application specific user information but only 
*	framework and web interaction information. Currently this is used in role lookup, login authentication and as one of the elements
*	in {@link Me} objects array.
*/

class User extends ORDataObject {

	var $_username;
	var $_password;
	var $groups;
	var $id;
	var $nickname;
	var $color;
	
	/**
	*	Constructor expects a reference to and adodb compliant db object.
	* 	When using frame this is in $GLOBALS['frame']['adodb']['db']
	*	It takes the reference and passes it to the parent class which set
	*	the private _db variable to be used when executing queries such as 
	*	$this->_db->Execute()
	*/
	
    function User($username = false,$password = false,$db = null) {
    	parent::ORDataObject($db);	
	$this->_table = "users";
	$this->_sequence_name = $this->_prefix."sequences";
    	$this->groups = array();
    	
	$this->setup($username,$password);
    }

    function setup($username=false,$password=false) {
    	/*
    	*	User id 0 is reserved for use as the default user, a named user should never have an id of 0
    	*/
    	$this->id = DEFAULT_USER_ID;
		
    	if (!empty($username) && !empty($password)) {
    		$this->id = $this->get_id_from_userpass($username,$password);
			
    		if (is_numeric($this->id) && $this->id != 0) {
    			$this->populate();
    		}	
    	}
    }
    
    function populate() {
    
    	parent::populate();
			$sql = "SELECT u.*,ug.*, g.* from " . $this->_prefix . $this->_table . " as u LEFT JOIN " . $this->_prefix . "users_groups as ug on ug.user_id = u.id LEFT JOIN " . $this->_prefix . "groups as g on g.id=ug.group_id where u.id = " . $this->_db->qstr($this->id);
    	//echo "sql: $sql<br>";
    	$res = $this->_Execute($sql);
    	while ($res && !$res->EOF) {
    		$this->_username = $res->fields['username'];
    		//$this->groups[$res->fields['group_id']] = array("name" => $res->fields['name'], "foreign_id" => $res->fields['foreign_id']);
    		$this->groups[$res->fields['group_id']] = $res->fields['name'];
    		$res->MoveNext();
    		//echo $this->_username;	
    	}    	
    }
    
    /**#@+
    *	Getter/Setter method used as part of object model for populate, persist, and form_poulate operations
    */
    
    function get_username() {
    	return $this->_username;	
    }
    
    function set_username($un) {
    	$this->_username = $un;	
    }
    
    function get_password() {
    	return $this->_password;	
    }
    
    function set_password($p) {
    	$this->_password = $p;	
    }
    
    function get_id() {
    	return $this->id;	
    }
    
    function set_id($id) {
    	
    	//id of 0 is reserved for default user
    	if (is_numeric($id) && $id != DEFAULT_USER_ID) {
    		$this->id = $id;
    	}	
    }
    
    function get_nickname() {
    	return $this->nickname;	
    }
    function set_nickname($value) {
   		$this->nickname = $value;
    }
    
    function get_color() {
    	return $this->color;	
    }
    function set_color($value) {
   		$this->color = $value;
    }
    
    function get_default_location_id() {
    	return $this->default_location_id;	
    }
    function set_default_location_id($value) {
   		$this->default_location_id = $value;
    }
    
    function get_groups() {
    	return $this->groups;	
    }
    function get_group_ids() {
    	return array_keys($this->groups);
    }
    
    function set_groups($g) {
    	if (is_array($g)) {
    	  $this->groups = $g;
    	}	
    }
    
    function groups_factory() {
    	
    	$groups = array();
			$sql = "SELECT * FROM ".$this->_prefix."groups";
    	$db = $this->_db;
    	$res = $db->Execute($sql);
    	
    	while ($res && !$res->EOF) {
    		$groups[$res->fields["id"]] = $res->fields["name"];
    		$res->MoveNext(); 
    	}	
    	return $groups;
    }
    
    /**
    *	User has a this specialty function because it sometimes needs to populate having only a username and password
    *	rather than an id. This case is usually login.
    *	
    */
		function get_id_from_userpass ($username, $password) {
			$sql = "SELECT u.id from " . $this->_prefix . $this->_table . " as u where  username = " . $this->_db->qstr($username) . " and password = " .  $this->_db->qstr($password);
    	//echo "sql: $sql<br>";
    	$results = $this->_execute($sql);
    	if (!$results->EOF) {
    		return $results->fields['id'];	
    	}
    	return NULL;	
		  	
    } 
    
    function users_factory($group="") {
		$users = array();
		$u = new User(null,null);
		$sql = "SELECT u.id from " . $u->_prefix . $u->_table . " as u ";
		
		if (!empty($group)) {
			$sql .= " LEFT JOIN ".$this->_prefix."users_groups as ug on ug.user_id=u.id LEFT JOIN ".$this->_prefix."groups as g on g.id = ug.group_id where g.name =" . $this->_db->qstr($group);
		}
		$sql .= " order by u.username";
		
		$results = $this->_db->Execute($sql) or die ("Database Error: " . $this->_db->ErrorMsg());
		while ($results && !$results->EOF) {
			$tu = new User("","");
			$tu->id = $results->fields['id'];
			$tu->populate();
			$users[] = $tu;
			$results->MoveNext();
		}
		return $users;
	}
	function is_group_member($test_group = "usage") {
		foreach($this->groups as $group) {
			if ($group == $test_group) {
				return true;	
			}	
		}
		return false;
	}

}
?>
