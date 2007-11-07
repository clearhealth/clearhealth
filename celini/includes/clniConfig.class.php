<?php

class clniConfig
{
	/**
	 * 
	 * Storage for config keys and values.
	 * 
	 * @access private
	 * 
	 * @see clniConfig::clniConfig()
	 * 
	 * @see clniConfig::get()
	 * 
	 * @see clniConfig::set()
	 * 
	 */
	var $_corral = array();
	
	
	/**
	 * Handle initialization
	 *
	 * This takes an array, how you populate the array should be left up to a 
	 * a clniConfigFactory object.
	 *
	 * @param array
	 */
	function clniConfig($configArray) {
		assert('is_array($configArray)');
		
		$this->_corral = $configArray;
	}
	
	
	/**
	 * Returns the value of <i>$key</i> if it exists
	 *
	 * @param  string $key
	 * @param  string $default returned if the setting isn't set, null being the default value to return
	 * @return mixed
	 */
	function get($key,$default = null) {
		if (isset($this->_corral[$key])) {
			return $this->_corral[$key];
		}
		return $default;
	}

	/**
	 * Set a property
	 *
	 * @param string	$key
	 * @param mixed		$value
	 */
	function set($key,$value) {
		$this->_corral[$key] = $value;
	}
	
	function cget($value,$default = false,$parent = NULL) {
		if (is_null($parent)) $parent = $GLOBALS['config'];
		$vars = split(':',$value);
		if (count($vars) > 0 && !empty($vars[1])) {
				$parent = $parent[$vars[0]];
				array_shift($vars);
				return clniConfig::cget(implode(":",$vars),$default,$parent);
		}
		elseif (isset($parent[$value])) {
			return $parent[$value];
		}
		return $default;
	}
}
?>
