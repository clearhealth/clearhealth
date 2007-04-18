<?php

$loader->requireOnce('includes/PropertyContainer.class.php');

class ChildPropertyContainer extends PropertyContainer
{
	var $_parent = null;
	
	function ChildPropertyContainer(&$parent) {
		parent::PropertyContainer();
		$this->_parent =& $parent;
	}
	
	function get($key) {
		$accessor = 'get_'. $key;
		if (method_exists($this->_parent, $accessor)) {
			return $this->_parent->$accessor();
		}
		return parent::get($key);
	}
	
	function set($key, $value) {
		$mutator = 'set_' . $key;
		if (method_exists($this->_parent, $mutator)) {
			$this->_parent->$mutator($value);
		}
		parent::set($key, $value);
	}
	
	function value($key) { 
		$accessor = 'value_'. $key;
		if (method_exists($this->_parent, $accessor)) {
			return $this->_parent->$accessor();
		}
		return parent::value($key);
	}
}
