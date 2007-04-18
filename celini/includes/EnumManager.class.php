<?php
$loader->requireOnce("/includes/EnumList.class.php");
$loader->requireOnce("/includes/EnumType/Default.class.php");
/**
 * Access point to all Enumeration data within Celini
 * 
 * Usage:
 * <code>
 * $manager =& EnumManager::GetInstance();
 *
 * var_dump($manager->enumArray('test'));
 * </code>
 * @author	Joshua Eichorn <josh@mail.com>
 * @package	com.uversainc.celini
 */
class EnumManager {

	/**
	 * EnumerationValue class cache
	 *
	 * @access private
	 */
	var $_elCache = array();

	/**
	 * Editing flag to pass to the enum type object
	 */
	var $editing = false;

	/**
	 * Singleton method, use this instead of new to get an instance
	 *
	 * @return	Enumeration
	 */
	function &getInstance() {
		if (!isset($GLOBALS['_CACHE']['ENUM_MANAGER'])) {
			$GLOBALS['_CACHE']['ENUM_MANAGER'] = new EnumManager();
		}
		return $GLOBALS['_CACHE']['ENUM_MANAGER'];
	}

	/**
	 * Get the values of a given enumeration back in an associative array (key => $value)
	 * 
	 * This is the most basic way to use an enumeration
	 * @param	string	$enumName
	 * @param	false|string	$key if not false use this column for the key
	 * @param	false|string	$val if not false use this column for the value
	 * @return	array()
	 */
	function enumArray($enumName,$key = false, $val = false) {
		$enum =& $this->enumList($enumName);

		if ($key === false) {
			return $enum->toArray();
		}
		else {
			$ret = array();
			for($enum->rewind(); $enum->valid(); $enum->next()) {
				$row = $enum->current();
				if ($row->status == 1) {
					$ret[$row->$key] = $row->$val;
				}
			}
			return $ret;
		}
	}

	/**
	 * Check if an enum exists
	 *
	 * @param	string	$enumName
	 * @return	boolean
	 */
	function enumExists($enumName) {
		$enum = Celini::newOrdo('EnumerationDefinition',$enumName,'ByName');
		return $enum->isPopulated();
	}


	/**
	 * Get the String value of an enum from its key
	 *
	 * @param  string  System name of the enum to lookup
	 * @param  int     The key of a given enu,
	 * @param  string  The name of the value to return (optional)
	 * @return string|false
	 */
	function lookup($enumName, $key, $valueName = 'value') {
		$enum =& $this->enumList($enumName);
		return $enum->lookupValue($key, $valueName);
	}
	
	
	/**
	 * Get the Key for an enum based on it's value
	 *
	 * @param  string 
	 * @param  string
	 * @return string
	 *
	 * @todo make this actually work nicely instead of ripping off the array
	 */
	function lookupKey($enumName, $value) {
		$array = $this->enumArray($enumName);
		return array_search($value, $array);
	}

	/**
	 * Get an sql92 join clause for a given enum
	 *
	 * @param	string	$enumName
	 * @param	string	$joinField
	 */
	function joinSql($enumName,$joinField) {
		$enum =& $this->enumList($enumName);
		return $enum->joinSql($joinField,$enumName);
	}

	/**
	 * Get an EnumerationList object for an enum, this allows you to access secondary information and/or make changes to the enum
	 *
	 * @param	string	$enumName
	 * @return	EnumerationValue
	 */
	function &enumList($enumName,$flags = array()) {
		if (count($flags) > 0) {
			$return =& new EnumList($enumName,$this->editing,$flags);
			return $return;
		}
		else {
			if (!isset($this->_elCache[$enumName])) {
				$this->_elCache[$enumName] =& new EnumList($enumName,$this->editing);
			}
			return $this->_elCache[$enumName];
		}
	}
}
?>
