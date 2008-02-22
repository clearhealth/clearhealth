<?php
/**
 * @package com.clear-health.celini
 */

/**#@+
 * Required library
 */
$loader->requireOnce('includes/PropertyContainer.class.php');
/**#@-*/

/**
 * Provides an interface for introspection of a Who object in the CeliniACL system
 *
 * @see CeliniACL
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @abstract
 */
class ACLObject
{
	var $_container = null;
	var $_celiniType = '';
	var $_gaclType = '';
	var $_gaclSectionValue = '';
	
	function ACLObject() {
		$this->_container =& new PropertyContainer();
	}
	
	function _setup($aclData) {
		if ($aclData['id'] === false || count($aclData) <= 0) {
			return;
		}
		$this->_container->set('id', $aclData['id']);
		$this->_container->set('section_value', $aclData[0]);
		$this->_container->set($this->_celiniType, $aclData[1]);
		$this->_container->set('order', $aclData[2]);
		$this->_container->set('hidden', $aclData[4]);
	}
	
	function setupByName($who) {
		$id = $GLOBALS['security']->get_object_id($this->_gaclSectionValue, $who, $this->_gaclType);
		$this->setupById($id);
	}
	
	function setupById($id) {
		$data = $GLOBALS['security']->get_object_data($id, $this->_gaclType);
		$data[0]['id'] = $id;
		$this->_setup($data[0]);
	}
	
	function isHidden() {
		return $this->_container->value('hidden') == 0 ? false : true;
	}
	
	function change($newWho) {
		$this->_container->set($this->_celiniType, $newWho);
	}
	
	function drop() {
		$dropSuccess = $GLOBALS['security']->del_object($this->_container->get('id'), $this->_gaclType);
		if ($dropSuccess) {
			$this->_container =& new PropertyContainer();
		}
		return $dropSuccess;
	}
	
	function genericList() {
		$idArray   = $GLOBALS['security']->get_object($this->_gaclSectionValue, 1, $this->_gaclType);
		$nameArray = $GLOBALS['security']->get_objects($this->_gaclSectionValue, 1, $this->_gaclType);
		
		$returnArray = array();
		foreach ($idArray as $key => $id) {
			$returnArray[$id] = $nameArray[$this->_gaclSectionValue][$key];
		}
		return $returnArray; 
	}
	
	
	function persist() {
		$GLOBALS['security']->edit_object(
			$this->_container->get('id'),
			$this->_container->get('section_value'),
			$this->_container->get($this->_celiniType),
			$this->_container->get($this->_celiniType),
			$this->_container->get('order'),
			$this->_container->get('hidden'),
			$this->_gaclType);
	}
}

