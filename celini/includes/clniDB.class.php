<?php
/**
 * Celini DB Helper
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class clniDB {
	/**
	 * DB Connection
	 */
	var $_db;

	/**
	 * Automatically sets up a db connection
	 */
	function clniDB() {
		$this->_db =& $this->dbInstance();
	}

	function checkConnection() {
		if (!$this->_db->isConnected()) {
			$this->_db->connect($this->_db->host,$this->_db->user,$this->_db->password,$this->_db->database);
		}
	}

	/**
	 * Get a db connection instance
	 */
	function &dbInstance() {
		return $GLOBALS['config']['adodb']['db'];
	}
	
	/**
	 * List the fields in a table
	 *
	 * @param	string	$table
	 */
	function listFields($table) {
		$sql = "SHOW COLUMNS FROM ". $this->escape($table);
		$res = $this->execute($sql);
		$field_list = array();
		while(!$res->EOF) {
			$field_list[] = $res->fields['Field'];
			$res->MoveNext();
		}
		return $field_list;
	}

	/**
	 * Returns an escaped string 
	 *
	 * You'll generally want to use {@link quote} instead as it will incapsulate
	 * the string in single quotes.
	 *
	 * @param  string
	 * @return string
	 * @see    quote()
	 *
	 * @todo Consider alternatives to allow for multi-database support instead
	 *    of relying on the PHP mysql extension. 
	 */
	function escape($input) {
		return mysql_real_escape_string($input);
	}

	/**
	 * Escapes <i>$input</i> and returns it encapsulated inside single-quotes.
	 *
	 * @param  string
	 * @return string
	 * @see    escape()
	 */
	function quote($input) {
		return "'" . clniDB::escape($input) . "'";
	}

	/**
	 * Execute a query and return result set, triggers and error on a failed query
	 *
	 * @param string	$sql
	 * @return resultset
	 */
	function execute($sql,$fetchMode = ADODB_FETCH_ASSOC) {
		if (!empty($sql)) {
			$this->_db->SetFetchMode($fetchMode);
			$res = $this->_db->Execute($sql);
			if ($res === false) {
				Celini::raiseError("Error in query- " . $this->_db->ErrorMsg()."<br>\n$sql");
			}
			return $res;
		}
	}

	/**
	 * Execute a query and get a single result back
	 *
	 * @param string	$sql
	 */
	function getOne($sql) {
		return $this->_db->getOne($sql);
	}	

	/**
	 * Execute a query and get an assoc array as the results
	 *
	 * @param string	$sql
	 */
	function getAssoc($sql) {
		return $this->_db->getAssoc($sql);
	}

	/**
	 * Execute a query and cache the results in memory
	 *
	 * This works exactly like {@link getAssoc()}, except it caches the results of the query and
	 * returns the cached version if the same query is called again during the same execution.
	 *
	 * @param  string $sql
	 * @return array
	 */
	function cachedGetAssoc($sql) {
		$sqlKey = md5($sql);
		if (!isset($GLOBALS['_CACHE']['clniDB']['cachedGetAssoc'][$sqlKey])) {
			$GLOBALS['_CACHE']['clniDB']['cachedGetAssoc'][$sqlKey] = $this->getAssoc($sql);
		}
		return $GLOBALS['_CACHE']['clniDB']['cachedGetAssoc'][$sqlKey];
	}
	
	/**
	 *  Execute a query and get a single column array back
	 *
	 * @param string	$sql
	 */
	function getCol($sql) {
		return $this->_db->getCol($sql);
	}

	/**
	 * Get the next id from a sequence
	 *
	 * @param string	$sequenceName
	 */
	function nextId($sequenceName = 'sequences') {
		return $this->_db->genId($sequenceName);
	}
	
	
	/**
	 * Get the next id from a sequence, using a custom one located in includes/sequences if 
	 * available.
	 *
	 * @see    nextId()
	 * @param  string   $sequenceName
	 * @param  array    $parameters    An optional series of parameters that are passed into a 
	 *                                 custom sequence function.
	 * @return int
	 */
	function customNextId($sequenceName = 'sequences', $parameters = array()) {
		if ($GLOBALS['loader']->includeOnce('includes/sequences/' . $sequenceName . '.function.php')) {
			$sequenceFunctionName = "Sequences_" . $sequenceName;
			return call_user_func_array($sequenceFunctionName, $parameters);
		}
		return $this->nextId($sequenceName);
	}
	

	/**
	 * Get the primary keys for a table
	 *
	 * @param string	$table
	 */
	function primaryKeys($table) {
		$this->checkConnection();
		return $this->_db->MetaPrimaryKeys($table);
	}

	/**
	 * Create an sql where clause for selecting a table by its primary key(s)
	 *
	 * @param string	$table
	 */
	function genSqlPrimaryKeyWhere(&$ordo) {
		$table = $ordo->tableName();
		$pkeys = $this->primaryKeys($table);

		if ($pkeys === false) {
			Celini::raiseError("Table: $table has no primary key specified and cannot be updated");
		}

		// see if were doing an insert or an update
		// can't use replace with foreign key constraints since it deletes and inserts not insert or update

		$pwhere = "";
		$first = true;
		foreach($pkeys as $pkey) {
			if (!$first) {
				$pwhere .= " and ";
			}
			$first = false;
			$pvalue = $this->quote($ordo->get($pkey));
			$pwhere .= " $pkey = $pvalue ";
		}
		return $pwhere;
	}

	function affectedRows() {
		return $this->_db->affected_rows();
	}
}

/**
 * Base sql type class, used for cases where you want to control custom escaping of values with out using a getter or setter method on the ordo
 */
class clniValue {
	var $value;
	function clniValue($value) {
		$this->value = $value;
	}

	function toString() {
		return $value;
	}

	function toSql(&$db) {
		return $db->escape($this->value);
	}
	function type() {
		return get_class($this);
	}
}

/**
 * Raw sql values, unescaped
 */
class clniValueRaw extends clniValue {
	function toSql() {
		return $this->value;
	}
}
?>
