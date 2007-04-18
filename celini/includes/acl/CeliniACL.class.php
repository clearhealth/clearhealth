<?php
/**
 * @package com.uversainc.celini
 */

/**#@+
 * Require library
 */
$loader->requireOnce('includes/acl/ACLWho.class.php');
$loader->requireOnce('includes/acl/ACLWhat.class.php');
$loader->requireOnce('includes/acl/ACLWhere.class.php');
$loader->requireOnce('includes/acl/ACLWhoGroup.class.php');
$loader->requireOnce('includes/acl/ACLWhereGroup.class.php');
/**#@-*/

/**
 * Provides an API for working with Celini's API system.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class CeliniACL
{
	/**
	 * Add a new ACL rule for a specific user
	 *
	 * @param  string  Who is executing this ACL
	 * @param  string  What are they doing
	 * @param  string  Where are they doing it
	 * @param  boolean Allow or deny
	 * @param  boolean Is a group
	 */
	function addACL($who, $what, $where, $allow = true, $isGroup = false) {
		settype($who, 'array');
		settype($what, 'array');
		settype($where, 'array');
		
		global $security;
		$aclWho = $isGroup ? array() : array('users' => $who);
		$aclWhoGroup = $isGroup ? $who : array();
		$security->add_acl(
			array('actions' => $what),
			$aclWho,
			$aclWhoGroup,
			array('resources', $where),
			array(),
			(int)$allow
		);
	}
	
	
	/**
	 * Add a new "who" to the ACL system
	 *
	 * @param  string
	 */
	function addWho($who) {
		return CeliniACL::_addObject($who, 'ARO');
	}
	
	
	/**
	 * Add a new "what"  to the ACL system
	 *
	 * @param string
	 */
	function addWhat($what) {
		return CeliniACL::_addObject($what, 'ACO');
	}
	
	
	/**
	 * Add a new "where" to the ACL system.
	 *
	 * @param string
	 */
	function addWhere($where) {
		return CeliniACL::_addObject($where, 'AXO');
	}
	
	
	/**
	 * Adds a value of <i>$type</i>'s value to the ACL system
	 *
	 * @access private
	 */
	function _addObject($value, $type) {
		global $security;
		switch ($type) {
			case 'ARO' :
				$section = 'users';
				break;
			case 'ACO' :
				$section = 'actions';
				break;
			case 'AXO' :
				$section = 'resources';
				break;
			default:
				Celini::raiseError('Unknown type');
		}
		
		$englishValue = ucwords(str_replace('_', ' ', $value));
		return $security->add_object($section, $englishValue, $value, 10, 0, $type);
	}
	
	
	/**
	 * Drop a 'who' from the system
	 *
	 * @param  string
	 * @return boolean
	 */
	function dropWho($who) {
		return CeliniACL::_dropObject($who, 'Who');
	}
	
	
	/**
	 * Drop a 'what' from the system
	 *
	 * @param  string
	 * @return boolean
	 */
	function dropWhat($what) {
		return CeliniACL::_dropObject($what, 'What');
	}
	
	
	/**
	 * Drop a 'where' from the system
	 *
	 * @param  string
	 * @return boolean
	 */
	function dropWhere($where) {
		return CeliniACL::_dropObject($where, 'Where');
	}
	
	
	/**
	 * Drop an object from the system
	 *
	 * @param  string
	 * @param  string
	 * @return boolean
	 * @access private
	 */
	function _dropObject($name, $type) {
		$objectName = "ACL{$type}";
		$object =& new $objectName();
		$object->setupByName($name);
		return $object->drop();
	}
	
	
	/**
	 * Change a 'who' value from the original to the new.
	 *
	 * @param  string
	 * @param  string
	 */
	function changeWho($original, $new) {
		CeliniACL::_changeObject('Who', $original, $new);
	}
	
	
	/**
	 * Change a 'what' value from the original to the new.
	 *
	 * @param  string
	 * @param  string
	 */
	function changeWhat($original, $new) {
		CeliniACL::_changeObject('What', $original, $new);
	}
	
	
	/**
	 * Change a 'where' value from the original to the new.
	 *
	 * @param  string
	 * @param  string
	 */
	function changeWhere($original, $new) {
		CeliniACL::_changeObject('Where', $original, $new);
	}
	
	
	/**
	 * Change an ACL object of a given <i>$type</i> from its original valueto its new value
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 * @access private
	 */
	function _changeObject($type, $original, $new) {
		$objectName = "ACL{$type}";
		$object =& new $objectName();
		$object->setupByName($original);
		$object->change($new);
		$object->persist();
	}
	
	
	/**
	 * Returns an array of 'Who' objects
	 *
	 * @return array
	 */
	function listWho() {
		return CeliniACL::_listObject('who');
	}
	
	
	/**
	 * Returns an array of 'Who' objects
	 *
	 * @return array
	 */
	function listWhat() {
		return CeliniACL::_listObject('what');
	}
	
	
	/**
	 * Returns an array of 'Who' objects
	 *
	 * @return array
	 */
	function listWhere() {
		return CeliniACL::_listObject('where');
	}
	
	
	/**
	 * Returns an array of ACL objects
	 *
	 * @return array
	 * @access private
	 */
	function _listObject($type) {
		$objectName = "ACL{$type}";
		$object =& new $objectName();
		return $object->genericList();
	}
	
	
	/**
	 * Add a new 'who' group.
	 *
	 * Returns the ID of the newly created group if successful, false otherwise.
	 *
	 * @param  string
	 * @param  string
	 * @return int|false
	 */
	function addWhoGroup($child, $parent) {
		CeliniACL::_addObjectGroup($child, $parent, 'Who');
	}
	
	
	/**
	 * Add a new 'where' group.
	 *
	 * Returns the ID of the newly created group if successful, false otherwise.
	 *
	 * @param  string
	 * @param  string
	 * @return int|false
	 */
	function addWhereGroup($child, $parent) {
		CeliniACL::_addObjectGroup($child, $parent, 'Where');
	}
	
	
	/**
	 * Handles adding the group.
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return int|false
	 * @access private
	 */
	function _addObjectGroup($child, $parent, $type) {
		$objectName = "ACL{$type}Group";
		$object =& new $objectName();
		$object->setupByName($parent);
		return $object->addChildGroup($child);
	}
	
	
	/**
	 * Returns an array of 'Who' groups
	 *
	 * @return array
	 */
	function listWhoGroups() {
		return CeliniACL::_listObjectGroups('Who');
	}
	
	
	/**
	 * Returns an array of 'Where' groups
	 *
	 * @return array
	 */
	function listWhereGroups() {
		return Celini::_listObjectGroups('Where');
	}
	
	
	/**
	 * Returns an array of ACL groups
	 *
	 * @return array
	 * @access private
	 */
	function _listObjectGroups($type) {
		$objectName = "ACL{$type}Group";
		$object =& new $objectName();
		return $object->genericList();
	}
	
	
	/**
	 * Add a "who" to a particular group
	 *
	 * @param  string
	 * @param  string
	 * @return boolean
	 */
	function addWhoToGroup($who, $group) {
		return CeliniACL::_addObjectToGroup($who, $group, 'Who');
	}
	
	
	/**
	 * Add a 'where' to a particular group
	 *
	 * @param  string
	 * @param  string
	 * @return boolean
	 */
	function addWhereToGroup($where, $group) {
		return CeliniACL::_addObjectToGroup($where, $group, 'Where');
	}
	
	
	/**
	 * Add an object to a particular group
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return boolean
	 * @access private
	 */
	function _addObjectToGroup($child, $group, $type) {
		$objectName = "ACL{$type}Group";
		$object =& new $objectName();
		$object->setupByName($group);
		return $object->addChildObject($child);
	}
	
	
	/**
	 * Drop a "who" from a particular group
	 *
	 * @param  string
	 * @param  string
	 * @return boolean
	 */
	function dropWhoFromGroup($who, $group) {
		return CeliniACL::_dropObjectFromGroup($who, $group, 'Who');
	}
	
	
	/**
	 * Drop a 'where' from a particular group
	 *
	 * @param  string
	 * @param  string
	 * @return boolean
	 */
	function dropWhereFromGroup($where, $group) {
		return CeliniACL::_dropObjectFromGroup($where, $group, 'Where');
	}
	
	
	/**
	 * Drop an object from a particular group
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return boolean
	 * @access private
	 */
	function _dropObjectFromGroup($child, $group, $type) {
		$objectName = "ACL{$type}Group";
		$object =& new $objectName();
		$object->setupByName($group);
		return $object->dropChildObject($child);
	}
	
	
	/**
	 * Returns an array of 'who' object that are a member of the given group
	 *
	 * @param  string
	 * @return array
	 */
	function getWhoGroupList($group) {
		return CeliniACL::_getObjectGroupList($group, 'Who');
	}
	
	
	/**
	 * Returns an array of 'where' object that are a member of the given group
	 *
	 * @param  string
	 * @return array
	 */
	function getWhereGroupList($group) {
		return CeliniACL::_getObjectGroupList($group, 'Where');
	}
	
	
	/**
	 * Return an array of the object that are a member of the given group
	 *
	 * @param  string
	 * @param  string
	 * @return array
	 * @access private
	 */
	function _getObjectGroupList($group, $type) {
		$objectName = "ACL{$type}Group";
		$object =& new $objectName();
		$object->setupByName($group);
		return $object->childObjectList();
	}
	
	
	/**
	 * Changes a 'who' group's name
	 *
	 * @param  string
	 * @param  string
	 * @return boolean
	 */
	function changeWhoGroup($old, $new) {
		return CeliniACL::_changeObjectGroup($old, $new, 'Who');
	}
	
	
	/**
	 * Changes a 'where' group's name
	 *
	 * @param  string
	 * @param  string
	 * @return boolean
	 */
	function changeWhereGroup($old, $new) {
		return CeliniACL::_changeObjectGroup($old, $new, 'Where');
	}
	
	
	/**
	 * Changes an object group's name
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return boolean
	 */
	function _changeObjectGroup($old, $new, $type) {
		$objectName = "ACL{$type}Group";
		$object =& new $objectName();
		$object->setupByName($old);
		$object->change($new);
		return $object->persist();
	}
	
	
	function dropWhoGroup($who) {
		return CeliniACL::_dropObjectGroup($who, 'Who');
	}
	
	function dropWhereGroup($where) {
		return CeliniACL::_dropObjectGroup($where, 'Where');
	}
	
	function _dropObjectGroup($name, $type) {
		$objectName = "ACL{$type}Group";
		$object =& new $objectName();
		$object->setupByName($name);
		return $object->drop();
	}
}

