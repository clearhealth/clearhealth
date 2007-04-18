<?php
$loader->requireOnce('includes/EnforceType.class.php');
/**
 * Filter class for _GET or _POST
 *
 * If this were php5 it would implement arrayAccess and be able to easily replace _GET or _POST
 */
class clniFilter {

	/**
	 * @access private
	 */
	var $_data;

	/**
	 * @access private
	 */
	var $_typeEnforcer = false;

	/**
	 * Filter type
	 */
	var $_type;

	function clniFilter($type) {
		$var = strtoupper($type);
		$this->_type = $type;

		switch($var) {
			case 'GET':
				$this->_data = $_GET;
				break;
			case 'POST':
				$this->_data = $_POST;
				break;
			case 'SERVER':
				$this->_data = $_SERVER;
				break;
			default:
				$this->_data = @$GLOBALS['HTTP_'.$var.'_VARS'];
		}
		$this->_typeEnforcer = new EnforceType();
	}

	/**
	 * check if a key is set
	 */
	function exists($key) {
		return isset($this->_data[$key]);
	}

	/**
	 * Getting a value with basic security, htmlentities and magic_quotes
	 *
	 * Will return an empty string with no errors thrown if the key doesn't exist
	 */
	function get($key) {
		$value = $this->getRaw($key);
		$untainted = $this->escape($value);
	    
		return $untainted;
	}
	
	
	/**
	 * Escapes and returns a given <i>$value</i>
	 *
	 * @param  mixed
	 * @return mixed
	 *
	 * @todo Should this be deprecated in favor of using {@link getTyped()} with
	 *    htmlsafe <i>$type</i>, or do we want to make it an alias?
	 */
	function escape($value) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = $this->escape($v);
			}
		}
		elseif (!is_object($value)) {
			$value = htmlentities($value);
		}
		
		return $value;	
	}

	/**
	 * Get a raw value
	 *
	 * This will return a raw value, while filtering away any PHP do-gooding 
	 * such as magic_quotes_gpc.
	 *
	 * @return mixed
	 */
	function getRaw($key) {
		$value = isset($this->_data[$key]) ? $this->_data[$key] : null;
		return $this->_rawFilter($value);
	}
	
	/**
	 * Handles any filtering that should happen to "raw" values.
	 *
	 * This will generally only be used to filter out magic_quotes_gpc slashes,
	 * but could be extended to handle additional types of noise that PHP 
	 * generates.
	 * 
	 * @access private
	 */
	function _rawFilter($value) {
		if (is_array($value)) {
			$array = array();
			foreach ($value as $k => $v) {
				$array[$k] = $this->_rawFilter($v);
			}
			return $array;
		} elseif (!is_object($value)) {
			return get_magic_quotes_gpc() ? stripslashes($value) : $value;
		}
		
		return $value;
	}

	/**
	 * Get a value forced to a specified type
	 */
	function getTyped($key,$type) {
		$value = $this->getRaw($key);
	
		return $this->_typeEnforcer->$type($value);
	}
	
	/**
	 * Used to set a value.
	 *
	 * {@internal The only use I see for this is when you need to mimic a full
	 *     request, by manually setting values in _GET or _POST}
	 *
	 * @param  string
	 * @param  mixed
	 */
	function set($key, $value) {
		$this->_data[$key] = $value;
		switch($this->_type) {
			case 'GET':
				$_GET[$key] = $value;
				break;
			case 'POST':
				$_POST[$key] = $value;
				break;
			case 'SERVER':
				$_SERVER[$key] = $value;
				break;
		}
	}
	
	
	/**
	 * Returns an array of all of the keys of the various values this contains
	 *
	 * @return array
	 */
	function keys() {
		return array_keys($this->_data);
	}
}
?>
