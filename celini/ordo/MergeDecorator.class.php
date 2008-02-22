<?php
/**
 * ORDO class that has decoration code for merging objects
 *
 * @package	com.clear-health.celini
 */

/**
 * Object Relational Persistence Mapping Class that merges multiple ORs into 1 base object
 *
 * @package	com.clear-health.celini
 */
class MergeDecorator extends ORDataObject {

	/**
	 * The list of merge keys
	 */
	var $_merge = array();

	/**
	 * Merge an object in
	 */
	function merge($key,&$object) {
		$this->_merge[$key] = $key;
		$this->$key =& $object;
		
		$this->_foreignKeyList = array_merge($object->_foreignKeyList, $this->_foreignKeyList);
		$this->_enumList = array_merge($object->_enumList, $this->_enumList);
	}

	/**
	 * main get method checks the current class first then the merge classes in order
	 */
	function get($key) {
		
		if ($this->exists($key)) {
			return parent::get($key);
		}
		else {
			foreach($this->_merge as $k) {
				if ($this->$k->exists($key)) {
					return $this->$k->get($key);
				}
			}
		}
	}

	/**
	 * main value method checks the current class first then the merge classes in order
	 */
	function value($key) {
		$accessor = 'value_' . $key;	
		if (method_exists($this,$accessor)) {
			return parent::value($key);
		}
		else {
			foreach($this->_merge as $k) {
				if (method_exists($this->$k,$accessor)) {
					return $this->$k->value($key);
				}
			}
		}
		return $this->get($key);
	}
	
	
	/**
	 * {@inheritdoc}
	 *
	 * @internal Look for a custom valueList method on this object, then go through all of the
	 *    merged objects looking for custom values.  If nothing is found, return whatever the 
	 *    default valueList would have returned anyhow.
	 */
	function valueList($key = null) {
		$accessor = 'valueList_' . $key;
		if (method_exists($this, $accessor)) {
			return parent::valueList($key);
		}
		else {
			foreach ($this->_merge as $k) {
				if (method_exists($this->$k, $accessor)) {
					return $this->$k->value($key);
				}
			}
		}
		return parent::valueList($key);
	}

	/**
	 * main set method that hits person as well
	 */
	function set($key,$value) {
		if ($this->exists($key)) {
			return parent::set($key,$value);
		}
		else {
			foreach($this->_merge as $k) {
				if ($this->$k->exists($key)) {
					return $this->$k->set($key,$value);
				}
			}
		}
	}

	/**
	 * Load the data from the db
	 */
	function mergePopulate($value = false) {
		foreach($this->_merge as $key) {
			if ($value) {
				$this->$key->set($value,$this->get($value));
			}
			$this->$key->populate();
		}
		$this->_populated = true;
	}

	/**
	 * Store the data to the db
	 */
	function mergePersist($value = false) {
		$ret = false;
		foreach($this->_merge as $key) {
			if ($value) {
				$this->$key->set($value,$this->get($value));
			}
			if ($this->$key->persist()) {
				$ret = true;
			}
		}
		return $ret;
	}
}
?>
