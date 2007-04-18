<?php
$loader->requireOnce('/includes/Messages.class.php');
$loader->requireOnce('/includes/clniMapper.class.php');

/**
 * Base Manager class, used to process results from actions
 *
 * {@link clniMapper} for the syntax to use for action and process methods
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class Manager {

	var $messages;
	var $controller;

	var $_mapper;

	/**
	 * Lookup array for security prefix method names
	 */
	var $_methodTable = array();

	/**
	 * Grab a messages instance so managers to give feedback
	 */
	function Manager() {
		$this->messages =& Messages::getInstance();
	}

	/**
	 * Set a reference to the controller
	 */
	function setController(&$con) {
		$this->controller =& $con;
		$this->_mapper = new clniMapper($this);
	}

	/**
	 * Setup method that is ran on every page after the controller is setup but before any process method is called
	 */
	function preProcess() {
	}

	/**
	 * Setup method that is ran on every page after the child controllers have been run but before current controller is fetched
	 */
	function postProcess() {
	}

	/**
	 * Static method that returns an instance of the manager or false if it doesn't exists
	 *
	 * @static
	 */
	function &factory($manager) {
		$manager = ucfirst($manager);
		$class = "M_$manager";
		if (class_exists($class)) {
			$return = new $class;
			return $return;
		}
		else {
			global $loader;
			if (!$loader->includeOnce('controllers/' . $class . '.class.php')) {
				$return = new Manager();
				return $return;
			}
			
			$return = new $class;
			return $return;
		}
		
		$return = new Manager();
		return $return;
	}
	
	function isValid() {
		return (strtolower(get_class($this)) != 'manager'); 
	}

	/**
	 * Helper function used build a method name
	 */
	function _methodName($mode,$action) {
		$method = $this->_mapper->getMethod($mode,$action);

		if ($method !== false) {
			return $method;
		}

		// compat syntax
		if ($mode === 'process') {
			$method = "process_".strtolower($action);
		}
		else if ($mode === 'action') {
			$method = strtolower($action).'_action';
			if (!method_exists($this,$method)) {
				$method = 'action_'.strtolower($action);
			}
		}

		return $method;
	}
	

	/**
	 * Method that tells if a process exists
	 */
	function exists($process,$mode ='process') {
		$method = $this->_mapper->getMethod($mode,$process);
		if ($method !== false) {
			return true;
		}
		$method = $this->_methodName($mode,$process);
		if (method_exists($this,$method)) {
			return true;
		}
		return false;
	}

	/**
	 * Process and return its results
	 */
	function dispatch($action,$args,$mode = 'process') {
		if ($this->exists($action,$mode)) {
			$method = $this->_mapper->getMethod($mode,$action);
			if ($method === false) {
				$method = $this->_methodName($mode,$action);
			}
			return call_user_func_array(array(&$this,$method),$args);
		}
	}

	/**
	 * Get the acl role for an action
	 */
	function aclRole($action,$mode='action') {
		return $this->_mapper->getRole($mode,$action);
	}

	
}
?>
