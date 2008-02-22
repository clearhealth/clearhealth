<?php
/**
 * Handles mapping action method names
 *
 * Syntax is: actionActionName or processActionName
 *
 * If ActionName matches a gacl role permissions are applied using that: possible roles are (Usage,View,Edit,Add,Delete)
 *
 * If you need to specify a none matching name you can a postFix like _edit
 *
 * some preferred examples are
 *
 * actionAdd
 * actionEdit
 * processAdd
 *
 * With a postfix
 *
 * actionAddUser_edit
 * processAddUser_edit
 *
 * Note that only the new style syntax is supported in this class.  Only syntax variation support is done in the calling class.
 *
 * @package com.clear-health.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 * @todo	pull role list form gacl
 * @todo	is it worth adding a file cache for these, if so should we just use serialize and a factory?
 */
class clniMapper {

	var $_methodMap = array();
	var $_securityMap = array();

	var $_roles = array('usage','view','edit','add','delete','list');

	/**
	 * Setup the mapper
	 */
	function clniMapper($class) {
		$this->setup($class);
	}

	/**
	 * Build method map and security cache
	 */
	function setup($instance) {
		$this->_methodMap = array();
		$methods = get_class_methods($instance);
		foreach($methods as $method) {
			$security = false;
			$key = false;
			if (preg_match('/^(action|process)/', $method)) {
				if (strstr($method,'_')) {
					$security = 'view';
					$key = substr($method,0,strrpos($method,'_'));
					$possible = substr($method,strrpos($method,'_')+1);
					if ($possible && in_array($possible,$this->_roles)) {
						$security = $possible;
					}
					else {
						$key = $method;
					}
				}
				else {
					$key = $method;
					$possible = false;
					$security = false;
					if (substr($method,0,7) === 'process') {
						$possible = strtolower(substr($method,7));
					}
					else if (substr($method,0,6) === 'action') {
						$possible = strtolower(substr($method,6));
					}
					if ($possible && in_array($possible,$this->_roles)) {
						$security = $possible;
					}
				}
			}

			if ($key) {
				//var_dump($key,$method,$security,"\n");
				$this->_methodMap[strtolower($key)] = $method;
				$this->_securityMap[strtolower($key)] = $security;
			}
		}
	}

	/**
	 * Get a method for an action name
	 *
	 * @param	process|action	$type
	 * @param	string		$action
	 * @return	string|false	false if there is no matching method
	 */
	function getMethod($type,$action) {
		$name = strtolower($type.$action);
		if (isset($this->_methodMap[$name])) {
			return $this->_methodMap[$name];
		}
		return false;
	}

	/**
	 * Get a security role for an action name
	 *
	 * @param	process|action	$type
	 * @param	string		$action
	 * @return	string|false	false if there is no matching method
	 */
	function getRole($type,$action) {
		$realAction = 'action' . strtolower($action);
		if (isset($this->_securityMap[$realAction])) {
			return $this->_securityMap[$realAction];
		}
		return false;
	}
}
?>
