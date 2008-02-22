<?php
/**
 * @package com.clear-health.celini
 */

/**
 * A container of properties with various methods for retrieving and manipulating them.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class PropertyContainer
{
	/**#@+
	 * @access private
	 */
	var $_properties = array();
	var $_unknownCallback = '_unknownMessage';
	/**#@-*/
	
	function PropertyContainer() {
		
	}
	
	/**
	 * Populate this container from an associative array
	 *
	 * @param  array
	 */
	function populateByArray($array) {
		foreach ($array as $key => $value) {
			$this->set($key,  $value);
		}
	}
	
	
	/**
	 * Returns a property with a given <i>$key</i>.
	 *
	 * @param  string
	 * @return mixed
	 */
	function get($key) {
		$accessor = 'get_' . $key;
		if (method_exists($this, $accessor)) {
			return $this->$accessor();
		}
		
		if (isset($this->_properties[$key])) {
			return $this->_properties[$key];
		}
		
		$unknownCallback = $this->_unknownCallback;
		return $this->$unknownCallback($key);
	}
	
	
	/**
	 * Sets a property with a given <i>$key</i> to the specified <i>$value</i>.
	 *
	 * @param  string
	 * @param  mixed
	 */
	function set($key, $value) {
		$mutator = 'set_' . $key;
		if (method_exists($this, $mutator)) {
			$this->$mutator($value);
		}
		
		$this->_properties[$key] = $value;
	}
	
	
	/**
	 * Returns a human readable property with a given <i>$key</i>.
	 *
	 * @param  string
	 * @return mixed
	 */
	function value($key) {
		$accessor = 'value_' . $key;
		if (method_exists($this, $accessor)) {
			return $this->$accessor();
		}
		return $this->get($key);
	}
	
	
	/**
	 * Performs some sort of function when an unknown property is requested
	 *
	 * @access protected
	 */
	function _unknownMessage($key) {
		return 'Unknown property [' . $key . ']';
	}
	
	
}

