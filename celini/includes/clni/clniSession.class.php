<?php
/**
 * Wrapper for the PHP session
 *
 * Writes directly to the session, so a singleton isn't needed
 *
 * Session data is stored in its own namespace so data stored with this
 * won't be accessible by the same property name in _SESSION
 */
class clniSession {

	var $_mainNamespace = '_clniSession';

	var $namespace = 'default';

	/**
	 * Set the namespace you want to work in, this is usually set to the module name
	 *
	 * @param string $namespace The namespace to set
	 * @return old namespace
	 */
	function setNamespace($namespace) {
		$old = $this->namespace;
		$this->namespace = $namespace;
		return $old;
	}

	/**
	 * Get a value from the session
	 *
	 * @param string $property Name of the key, can also specify the namespace namespace:property
	 * @param string $default The value to return if the key isn't set
	 */
	function get($property,$default = null) {
		$p = $this->_setNamespaceFromProperty($property);
		if ($p) {
			$ret = $this->_get($p[1],$default);
			$this->setNamespace($p[0]);
			return $ret;
		}
		return $this->_get($property,$default);
	}

	/**
	 * set a value in the session
	 *
	 * @param string $property Name of the key, can also specify the namespace namespace:property
	 * @param mixed $value The value to set
	 */
	function set($property,$value) {
		$p = $this->_setNamespaceFromProperty($property);
		if ($p) {
			$this->_set($p[1],$value);
			$this->setNamespace($p[0]);
		}
		else {
			$this->_set($property,$value);
		}
	}

	/**
	 * Merge a set of values into an array that is in the session
	 *
	 * @param string $property Name of the key, can also specify the namespace namespace:property
	 * @param array|string  $value
	 */
	function merge($property,$value) {
		$oldVal = $this->get($property);
		settype($oldVal, 'array');
		$this->set($property,array_merge($oldVal,$value));
	}

	function _get($property,$default,$print = false) {
		if (isset($_SESSION[$this->_mainNamespace][$this->namespace][$property])) {
			$ret = $_SESSION[$this->_mainNamespace][$this->namespace][$property];
			return $ret;
		}
		//if ($print) {echo $this->_mainNamespace .'//'.$this->namespace.'//'.$property; var_dump($_SESSION['_clniSession']);}
		return $default;
	}

	function _set($property,$value) {
		$_SESSION[$this->_mainNamespace][$this->namespace][$property] = $value;
	}
	function clear() {
		$_SESSION[$this->_mainNamespace][$this->namespace] = array();
	}

	/**
	 * @return mixed, false if no namespace, an array of ($oldnamespace,$property) if it contains it
	 */
	function _setNamespaceFromProperty($property) {
		if (preg_match('/(.+):(.+)/', $property, $matches)) { 
			$old = $this->setNamespace($matches[1]);
			return array($old,$matches[2]);
		}
		return false;
	}
}
?>
