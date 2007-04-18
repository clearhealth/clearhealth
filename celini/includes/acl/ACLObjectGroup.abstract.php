<?php
/**
 * @package com.uversainc.celini
 */

/**#@+
 * Required library
 */
$loader->requireOnce('includes/PropertyContainer.class.php');
/**#@-*/

/**
 * Provides an abstract interface for introspection of group objects in the CeliniACL system
 *
 * @see CeliniACL
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @abstract
 */
class ACLObjectGroup
{
	var $_container = null;
	var $_gaclType = '';
	var $_celiniType = '';
	var $_gaclSectionValue = '';
	
	function ACLObjectGroup() {
		$this->_container =& new PropertyContainer();
	}
	
	function _setup($aclData) {
		if ($aclData[0] === false || count($aclData) <= 0) {
			return;
		}
		$this->_container->set('id', $aclData[0]);
		$this->_container->set('parent_id', $aclData[1]);
		$this->_container->set('name', $aclData[2]);;
	}
	
	function setupByName($name) {
		$id = $GLOBALS['security']->get_group_id($name, null, $this->_gaclType);
		$this->setupById($id);
	}
	
	function setupById($id) {
		$data = $GLOBALS['security']->get_group_data($id, $this->_gaclType);
		$this->_setup($data);
	}
	
	function change($new) {
		$this->_container->set('name', $new);
	}
	
	function genericList() {
		$returnArray = $GLOBALS['security']->sort_groups($this->_gaclType);
		return $returnArray; 
	}
	
	function childObjectList() {
		$childrenArray = $GLOBALS['security']->get_group_objects($this->_container->get('id'), $this->_gaclType, 'RECURSE');
		return $childrenArray[$this->_gaclSectionValue];
	}
	
	function addChildGroup($name) {
		$newGroupId = $GLOBALS['security']->add_group($name, $name, $this->_container->get('id'), $this->_gaclType);
		return $newGroupId;
	}
	
	function addChildObject($name) {
		return $GLOBALS['security']->add_group_object($this->_container->get('id'), $this->_gaclSectionValue, $name, $this->_gaclType);
	}
	
	function dropChildObject($name) {
		return $GLOBALS['security']->del_group_object($this->_container->get('id'), $this->_gaclSectionValue, $name, $this->_gaclType);
	}
	
	function persist() {
		return $GLOBALS['security']->edit_group(
			$this->_container->get('id'),
			$this->_container->get('name'),
			$this->_container->get('name'),
			$this->_container->get('parent_id'),
			$this->_container->get($this->_gaclType)
		);
	}
	
	function drop() {
		$dropSuccess = $GLOBALS['security']->del_group($this->_container->get('id'), true, $this->_gaclType);
		if ($dropSuccess) {
			$this->_container =& new PropertyContainer();
		}
		return $dropSuccess;
	}
}

