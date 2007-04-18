<?php
/**
* Generic storage mechanism for use with ORDataObjects
*/
class Storage {
	var $foreign_key;


	var $_table;
	var $_prefix;
	var $_type;

	var $_updates = array();
	var $_values = array();

	var $_defaultReturn = false;
	
	
	/**
	 * Stores whether this is internally in a persist() operation
	 *
	 * @var boolean
	 * @see persist()
	 * @access private
	 */
	var $_inPersist = false;
	

	/**
	* Set the table name based on type
	*
	* @param	string	$type	int|date
	* @param	int	$foreign_key
	*/
	function Storage($type,$foreign_key = 0,$db = null, $prefix = null) {
		$this->_table = "storage_$type";
		$this->_type = $type;

		settype($foreign_key,'int');

		if ($prefix != NULL) {
		  $this->_prefix = $prefix;
		}
		else {
		    if (isset($GLOBALS['frame']['config']['db_prefix']) && !empty($GLOBALS['frame']['config']['db_prefix'])) {
		      $this->_prefix = $GLOBALS['frame']['config']['db_prefix'];
		    }
		}

		if ($foreign_key != 0) {
			$this->foreign_key = $foreign_key;
			$this->populate();
		}
	}

	/**
	* Set a key to be updated
	*/
	function set($key,$value) {
		$setFilter = "_set_{$this->_type}";
		if (method_exists($this, $setFilter)) {
			$value = $this->$setFilter($value);
		}
		
		$this->_updates[$key] = $value;
		$this->_values[$key] = $value;
	}
	
	
	/**
	* Get a single value
	*/
	function get($key) {
		if (isset($this->_values[$key])) {
			$getFilter = "_get_{$this->_type}";
			if (method_exists($this, $getFilter)) {
				return $this->$getFilter($this->_values[$key]);
			}
			else {
				return $this->_values[$key];
			}
		}
		else if ($this->_defaultReturn !== false) {
			return $this->_defaultReturn;
		}
	}
	
	
	/**
	 * Converts a date value into an ISO date
	 *
	 * Note that this assumes that only dates are being stored, not timestamps.
	 * That assumption is being based off of what I've seen in my storage_date
	 * table.
	 *
	 * @return string
	 * @access private
	 */
	function _set_date($dateString) {
		if (empty($dateString)) { 
			return $dateString;
		}
		
		$date = DateObject::create($dateString);
		return $date->toISO();
	}
	
	
	/**
	 * Converts a stored date value into USA format
	 *
	 * See {@link _set_date()} for assumptions on date storage
	 *
	 * @return string
	 * @access private
	 */
	function _get_date($dateString) {
		if (empty($dateString)) {
			return '';
		}
		
		$date = DateObject::create($dateString);
		if ($this->_inPersist) {
			return $date->toISO();
		}
		else {
			return $date->toUSA();
		}
	}


	/**
	* Read values from the database
	*/
	function populate() {
		$db = new clniDb();
		if ($this->foreign_key) {
			$sql = "select value_key, value from $this->_prefix$this->_table where foreign_key = ".(int)$this->foreign_key;
			$this->_values = $db->getAssoc($sql);

			if ($this->_values === false) {
				// we have an error
				echo "Storage populate query failed: $sql\n";
				echo $db->errorMsg();
				die();
			}
			else {
				$this->_updates = $this->_values;
			}
		}
	}

	/**
	* Store updates into the db
	*/
	function persist() {
		$db = new clniDb();
		$this->_inPersist = true;
		foreach($this->_updates as $key => $val) {
			$sql = "replace into $this->_prefix$this->_table SET value_key = ".$db->quote($key).", value = ".$db->quote($val).", foreign_key = ".(int)$this->foreign_key;
			$res = $db->execute($sql);
			if ($res === false) {
				echo "Storage persist failed: $sql";
			}
		}
		$this->_inPersist = false;
	}

	/**
	* Generate sql to bring in the rows as columns
	*
	* @static
	*/
	function generateCaseSql($table,$fields,$alias_prefix="") {
		$ret = "";
		foreach($fields as $field) {
			$ret .= "max(case $table.value_key when '$field' then $table.value else null end) as `$alias_prefix$field`,\n";
		}
		return $ret;
	}
}
