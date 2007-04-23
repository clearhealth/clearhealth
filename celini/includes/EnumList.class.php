<?php
$GLOBALS['loader']->requireOnce("/includes/EnumValue.class.php");



/**
 * Provides an iterator and random access api to the values of an Enumeration
 *
 * This is just the standardized api, actual work is done in an EnumerationType class
 *
 * @author	Joshua Eichorn <jeichorn@mail.com>
 * @package	com.uversainc.celini
 */
class EnumList {
	
	/**
	 * EnumerationType class
	 */
	var $type;

	/**
	 * Id of the numeration
	 *
	 * Id is the numeric database id of this enum
	 */
	var $id = 0;

	/**
	 * Name of this Enumeration
	 *
	 * Name is the unique string used by the api
	 */
	var $name = "";

	/**
	 * Title of this Enumeration
	 *
	 * Title is a descriptive user readable 
	 */
	var $title = "";

	/**
	 * Enumeration Ordo
	 * @access private
	 */
	var $_enum;

	/**
	 * Enumeration values
	 * @access private
	 */
	var $_values = array();

	/**
	 * Stores if the current row in the iteration is valid
	 * @access private
	 */
	var $_valid = true;
	
	var $_keyMap = array();


	/**
	 * Pass in a Enumeration Name to populate the object with values
	 *
	 * @param	string|false	$enumName
	 */
	function EnumList($enumName = false,$editing = false,$flags = array()) {
		if ($enumName) {
			$this->name = $enumName;
			$this->_populate($editing,$flags);
		}
	}

	/**
	 * Helper method for populating an enum, loads the enum definition and then the correct type class
	 *
	 * @access private
	 */
	function _populate($editing = false,$flags) {
		$this->_enum =  Celini::newOrdo('EnumerationDefinition',$this->name,'ByName');
		
		if (!$this->_enum->isPopulated()) {
			trigger_error("Unknown Enumeration: {$this->name}");
		}
		
		$this->id = $this->_enum->get('id');
		$this->title = $this->_enum->get('title');
		$typeClass = "EnumType_".$this->_enum->get('type');

		if (!class_exists($typeClass)) {
			if ($GLOBALS['loader']->requireOnce('/includes/EnumType/'.$this->_enum->get('type').'.class.php') === false) {
				Celini::raiseError('Unable to find ' . $this->_enum->get('type') . ' enum type file');
			}
		}
		
		$type = new $typeClass();
		$type->editing = $editing;
		$type->flags = $flags;
		
		$this->setType($type);
		$this->rewind();
	}
	
	
	/**
	 * Used to set this EnumList to a new EnumType
	 *
	 * @param EnumType_Default
	 */
	function setType(&$type) {
		assert('is_a($type, "EnumType_Default")');
		
		$this->type =& $type;
		if (is_a($this->type,'EnumType_default')) {
			$this->_values = $this->type->enumData($this->id,true);
		}
		/* 
		 * make a map of the stored key values in from the DB to their real key
		 * inside the $_values array.
		 */
		foreach ($this->_values as $realKey => $value) {
			$this->_keyMap[$value['key']] = $realKey;
		}

	}
	

	/**
	 * Get an sql92 join clause
	 *
	 * @param	string	$joinField
	 * @param	string	$tableAlias
	 */
	function joinSql($joinField,$tableAlias) {
		return " inner join {$this->type->table} as $tableAlias on $tableAlias.`key` = $joinField and $tableAlias.enumeration_id = {$this->id} ";
	}

	/**
	 * Get the value of a specific key
	 *
	 * returns false if the key doesn't exist
	 *
	 * @see EnumManager::lookup()
	 * @param  int     The key of the enum to lookup
	 * @param  string  The name of the value to return (optional)
	 * @return string|false
	 */
	function lookupValue($key, $valueName = 'value') {
		if (isset($this->_keyMap[$key])) {
			return $this->_values[$this->_keyMap[$key]][$valueName];
		}
		return false;
	}

	/**
	 * Get the key for a specific value
	 *
	 * returns false if the key doesn't exist, and an array if multiple keys exist
	 *
	 * @param	string	$value
	 * @return	int|array|false
	 *
	 * @todo Make this work...  $_values isn't a multi-level array, and this
	 *  doesn't take that into consideration.
	 */
	function lookupKey($value) {
		$keys = array_keys($this->_values,$value);
		if (count($keys) === 1) {
			return $keys[0];
		}
		return $keys;
	}

	/**
	 * Get the values of this enumeration in an array
	 *
	 * @param	boolean	$assoc	If set to true an assoc array with key => value is returned, otherwise each item in the array contains a subarray with all the data from that row
	 * @return	array
	 */
	function toArray($assoc = true) {
		if ($assoc) {
			$ret = array();
			foreach($this->_values as $row) {
				if ($row['status'] == 1) {
					$ret[$row[$this->type->assocKey]] = $row['value'];
				}
			}
			return $ret;
		}
		return $this->_values;
	}

	/**
	 * Call to update the properties of the enumeration (not its values)
	 *
	 * @param	array	$properties
	 */
	function updateEnumeration($properties) {
		$this->_enum->populate_array($properties);
		$this->_enum->persist();
	}

	/**
	 * Call to update the values of the enumeration
	 *
	 * @param	array	$rows
	 */
	function updateValues($rows) {
		foreach($rows as $key => $row) {
			$row['enumeration_id'] = $this->id;
			$this->type->update($row);
			$this->_keyMap[$row['key']] = $row['key'] - 1;
		}
		$this->_values = $this->type->enumData($this->id);
	}

	// Iterator api
	/**
	 * Move the internal pointer to the start
	 */
	function rewind() {
		reset($this->_values);
	}

	/**
	 * Return an associative array containing all the data for the current item in this enumeration
	 *
	 * @return	EnumValue|false
	 */
	function &current() {
		$values = current($this->_values);
		if (is_array($values)) {
			$return =& new EnumValue(current($this->_values));
			return $return;
		}
		else {
			$return = false;
			return $return;
		}
	}

	/**
	 * Return the key of the current row
	 *
	 * @return	int
	 */
	function key() {
		return key($this->_values);
	}

	/**
	 * Move to the next entry
	 */
	function next() {
		next($this->_values);
	}

	/**
	 * Move to the last entry
	 */
	function end() {
		end($this->_values);
	}

	/**
	 * Check if the current item is valid
	 */
	function valid() {
		return $this->current();
	}
	
}
?>
