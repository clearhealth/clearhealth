<?php

require_once("User.class.php");
include_once(dirname(__FILE__) . "/adodb/adodb.inc.php");
/**
*	Me class serves as repository for runtime persistent across requests information about the requester
*	Houses information used to lookup roles, username, id, interfacable objects
*	@package com.pennfirm.openbiller.frame.env
*/

class Me {
	
	/**
	*	Holds array of objects that other objects can interface with at runtime using reflection, application specific
	*	@access private
	*	@var array
	*/
	
	var $_objects;
	
	/**
	*	Contains information that will be flushed to the db at session expiration or after specified threshold about audit points
	*	@access private
	*	@var array
	*/
	
	var $_audit_log;
	
	/**
	*	Contains the reference unique identifier for the current user
	*	@access private
	*	@var int
	*/
	
	var $_id;
	
	/**
	*	Sets user defaults prior to the object being populated from the database on a login. These represent the least amount of information
	*	available about a given user.
	*	 
	*/
	
	function Me() {
		
		$this->_objects = array();
		$this->_audit_log = array();
		$this->_id = 0;
		$this->_objects[] = new User(null,null);
	}
	
	/**#@+
	*	Getter/Setter for id variable
	*/
	
	function get_id() {
		return $this->_id;	
	}
	
	function set_id($id) {
		
		if($id == 0) {
			/**	There is a problem here, id 0 is reserved as the default it would not normally be supplied to set_id
			*	except in error.
			*	@todo error handling for set id = 0
			*/		
		}
		elseif (is_numeric($id)) {
			$this->_id = $id;	
		}
		
	}
	
	function get_user() {
		//print_r($this->_objects);
		foreach ($this->_objects as $key => $obj) {
			if (is_a($obj, "user")) {
				return $obj; 
			}	
		}
	}
	
	function get_groups() {
		$user = $this->get_user();
		return $user->get_groups();
	}
	
	function is_group_member($test_group = "usage") {
		$user = $this->get_user();
		return $user->is_group_member($test_group);
	}
	
	function get_username() {
		foreach ($this->_objects as $key => $obj) {
			if (is_a($obj, "user")) {
				return $obj->get_username(); 
			}	
		}
	}
	
	function get_user_id() {
		foreach ($this->_objects as $key => $obj) {
			if (is_a($obj, "user")) {
				return $obj->get_id(); 
			}	
		}
		return false;
	}
		
	function set_user($u) {
		if (is_a($u,"user")) {
			foreach ($this->_objects as $key => $obj) {
				if (is_a($obj, "user")) {
					unset ($this->_objects[$key]);
				}	
			}
			$this->_objects[] = $u;
			$this->_id = $u->get_id();
		}	
	}
}


?>