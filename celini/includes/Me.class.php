<?php

$loader->requireOnce("ordo/ORDataObject.class.php");
ORDataObject::factory_include('User');
/**
*	Me class serves as repository for runtime persistent across requests information about the requester
*	Houses information used to lookup roles, username, id, interfacable objects
*	@package com.uversainc.celini
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
		
		// should this initialization happen here?
		$this->_objects['user'] = Celini::newORDO('User');
	}

	/**
	* Get an instance of the me object, handles session storage
	*
	* @access static
	*/
	function &getInstance()
	{
		if (!(isset($_SESSION['frame']['me']) && is_a($_SESSION['frame']['me'],'me')))
		{
			$_SESSION['frame']['me'] = new Me();
		}
		return $_SESSION['frame']['me'];
	}
	
	/**#@+
	*	Getter/Setter for id variable
	*/
	function get_id() {
		return $this->_id;	
	}
	
	/**
	*	@todo error handling for set id = 0
	*/		
	function set_id($id) {
		
		if($id == 0) {
			//	There is a problem here, id 0 is reserved as the default it would not normally be supplied to set_id
			//	except in error.
		}
		elseif (is_numeric($id)) {
			$this->_id = $id;	
		}
	}
	
	function &get_user() {
		if (!isset($this->_objects['user']) || !is_a($this->_objects['user'],'User')) {
			if ($this->get_id() > 0) {
				$this->_objects['user'] = Celini::newOrdo('User',$this->get_id(),'ById');
			}
			else {
				$this->_objects['user'] = Celini::newOrdo('User');
			}
		}
		return $this->_objects['user'];
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
		$user = $this->get_user();
		return $user->get('username'); 
	}
	
	function get_user_id() {
		$user = $this->get_user();
		return $user->get('id');
	}

	function get_person_id() {
		$user = $this->get_user();
		return $user->get('person_id'); 
	}
		
	function set_user($u) {
		if (is_a($u,"user")) {
			unset ($this->_objects['user']);
			$this->_objects['user'] = $u;
			$this->_id = $u->get_id();
		}	
	}

	function isLoggedIn() {
		if ($this->get_id() > 0) {
			if (isset($this->_objects['user']) && is_a($this->_objects['user'],'User')) {
				return true;
			}
		}
		return false;
	}
}
?>
