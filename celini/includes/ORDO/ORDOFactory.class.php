<?php

$loader->requireOnce('/includes/ORDO/ORDOFileLoader.class.php');

class ORDOFactory
{
	var $_loader = null;
	var $_helper = null;
	
	var $name = '';
	var $setupSuffix = '';
	
	function ORDOFactory($name = '', $setupSuffix = '') {
		$this->name = $name;
		$this->setupSuffix = $setupSuffix;
		
		// init
		$this->_loader =& new ORDOFileLoader();
		$this->_helper =& new ORDOHelper();
	}
	
	function &newORDO($arguments) {
		assert('!empty($this->name)');
		
		if (!$this->_loader->loadORDO($this->name)) {
			trigger_error("Unable to find class for {$this->name}\n", E_USER_ERROR);
			exit;
		}
		
		$realName = $this->_helper->getName($this->name);
		$ordoObject =& new $realName();
		
		call_user_func_array(array(&$ordoObject, 'setup' . $this->setupSuffix), (array)$arguments);
		return $ordoObject;
	}
}

