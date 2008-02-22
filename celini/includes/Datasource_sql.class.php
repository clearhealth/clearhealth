<?php
/**
 * @package	com.clear-health.celini
 */
 
/**
 * Include the datasource class because were an implementation of it
 */
$loader->requireOnce("includes/Datasource.class.php");

/**
 * Sql version of a datasource
 * @package	com.clear-health.celini
 */
class Datasource_sql extends Datasource{
	var $_db;
	var $_query = array( 'cols' => '', 'from' => '', 'where' => '', 'groupby' => '', 'orderby' => '', 'limit' => '');
	var $union = array();
	var $orderHints = array();


	var $_res = false;
	var $_numRows = false;
	var $currentSql = false;

	/**
	 * @param	adodbconnection $db
	 * @param	array	$query
	 * @param	array	$labels	array('colname' => 'label')
	 */
	function setup(&$db,$query,$labels=array()) {
		if (!is_array($query)) {
			trigger_error('query must be an array',E_USER_ERROR);
		}
		$this->_db =& $db;
		$this->_query = $query;
		$this->_labels = $labels;

		// load in default orderby rules
		if (isset($this->_query['orderby']) && !isset($this->_query['union'])) {
			// strip out any comments
			$orderby = preg_replace('/\/\*.+\*\\//','',$this->_query['orderby']);
			$rules = explode(',',$orderby);
			foreach($rules as $key => $val) {
				$rules[$key] = trim($val);
			}
			if (count($rules) > 0) {
				$tmp = preg_split('/([,().]|as)/',$this->_query['cols'],0,PREG_SPLIT_DELIM_CAPTURE);
				$fields = array();
				$pos = 0;
				$field = '';
				foreach($tmp as $token) {
					switch($token) {
						case 'as':
							$field = '';
							break;
						case '(':
							$pos++;
							break;
						case ')':
							$pos--;
							$field = '';
							break;
						case '.':
							$field = '';
							break;
						case ',':
							if ($pos == 0) {
								$fields[] = trim($field);
								$field = '';
							}
							break;
						default:
							$field .= $token;
							break;
					}

				}
				$fields = array_flip($fields);
			}
			foreach($rules as $rule) {
				if (stristr($rule,'desc')) {
					$col = trim(substr(trim($rule),0,-4));
					$dir = 'DESC';
				}
				else if (stristr($rule,'asc')) {
					$col = trim(substr(trim($rule),0,-3));
					$dir = 'ASC';
				}
				else {
					$col = trim($rule);
					$dir = 'ASC';
				}
				$col = str_replace('`','',$col);
				if (!empty($col)) {
					if (isset($fields[$col])) {
						$this->addOrderRule($col,$dir,$fields[$col]);
					}
					else {
						$this->addOrderRule($col,$dir,'');

					}
				}
			}
		}
	}

	function setLimit($start,$rows) {
		$this->_query['limit'] = "$start,$rows";
	}
	
	function setQuery($section, $content) {
		if (isset($this->_query[$section])) {
			$this->_query[$section] = $content;
			return true;
		}
		return false;
	}

	function numRows() {
		if ($this->_numRows === false) {
			if (isset($this->_query['union'])) {
				$sql = "";
				foreach($this->_query['union'] as $part) {
					$part['limit'] = "";
					$part['orderby'] = "";
					if (!empty($sql)) {
						$sql .= " UNION \n";
					}
					$sql .= $this->_buildQuery($part);
				}
				if (strlen($this->_query['limit']) > 0) {
                                	$sql .= " LIMIT " . $this->_query["limit"];
                        	}
				//echo $sql;	
				$res = $this->_query($sql);
				$this->_numRows = $res->numRows();
			} 
			else if (isset($this->_query['groupby'])) {
				$part = $this->_query;
				$part['limit'] = "";
				$part['orderby'] = "";
				$sql = $this->_buildQuery($part);
				$res = $this->_query($sql);
				$this->_numRows = $res->numRows();
			}
			else {
				$sql = "SELECT count(*) FROM ".$this->_query['from'];
				if (!empty($this->_query['where'])) {
					$sql .= " WHERE ".$this->_query['where'];
				}
			
				$this->_numRows = $this->_getOne($sql);
			}
		}
		return $this->_numRows;
	}

	function _updateOrderBy($source) {
		uasort($this->_orderRules,array(&$this,'orderSort'));
		$orderby = "";
		foreach($this->_orderRules as $order => $data) {
			if ($data[1] === "ASC" || $data[1] === "DESC") {
				if (isset($this->orderHints[$data[0]])) {
					$data[0] = $this->orderHints[$data[0]];
				}
				$orderby .= " $data[0] $data[1], ";	
			}
		}
		$source['orderby'] = substr($orderby,0,strlen($orderby)-2);
		return $source;
	}

	/**
	 * if column labels aren't already set grab them from the query, right now were doing this in an expensive way
	 */
	function getColumnLabels() {
		if ($this->_labels === false) {
			if (!$this->valid()) {
				$this->rewind();
			}
			$row = $this->get();
			if (is_array($row)) {
				foreach(array_keys($row) as $label) {
					$this->_labels[$label] = ucfirst(str_replace('_',' ',$label));
				}
			}
			else {
				$this->_labels = array();
			}
		}
		return parent::getColumnLabels();
	}

	/**
	* Run filters on the current row
	*/
	function _filter() {
		parent::_filter($this->_res->fields);
	}

	/**
	* Query the database with basic error handling
	*/
	function _query($sql,$mode = ADODB_FETCH_ASSOC ) {
		$this->_db->SetFetchMode($mode);
		$res = $this->_db->Execute($sql) or $this->_error($sql);
		return $res;
	}

	function _error($sql) {
		var_dump($sql);
		unset($_SESSION['grid']);
		trigger_error("Error in query: " . $this->_db->ErrorMsg(). "$sql");
	}

	/**
	* Get one value form the db with basic error handling
	*/
	function _getOne($sql) {
		$res = $this->_query($sql,ADODB_FETCH_NUM);
		if (isset($res->fields[0])) {
			return $res->fields[0];
		}
	}

	/**
	* Prepare to Iterate
	*/
	function rewind() {
		$sql = $this->buildSql();
		$this->_res = $this->_query($sql);
		$this->currentSql = $sql;
		$this->_filter();
	}	

	/**
	 * Build a query from an array
	 */
	function _buildQuery($parts) {
		$sql = "SELECT ".$parts['cols'];
		if (!empty($parts['from'])) {
			$sql .= " FROM ".$parts['from'];
		}
		if (!empty($parts['where'])) {
			$sql .= " WHERE ".$parts['where'];
		}
		if (!empty($parts['groupby'])) {
			$sql .= " GROUP BY ".$parts['groupby'];
		}
		if (!empty($parts['orderby'])) {
			$sql .= " ORDER BY ".$parts['orderby'];
		}
		if (!empty($parts['limit'])) {
			$sql .= " LIMIT ".$parts['limit'];
		}
		return $sql;
	}
	
	/**
	* Is the current row valid
	*/
	function valid() {
		if (isset($this->_res->fields)) {
			return !$this->_res->EOF;
		}
		return false;
	}	
	
	/**
	* Move to the next row
	*/
	function next() { 
		$this->_res->moveNext();
		$this->_filter();
	}

	/**
	* Get all the fields for this row
	*/
	function get() {
		return $this->_res->fields;
	}
	
	/**
	 * Use {@link buildSql()} instead
	 *
	 * @see buildSql()
	 * @deprecated
	 */
	function preview() {
		return $this->buildSql();
	}
	
	/**
	 * Returns the SQL that this datasource generates
	 *
	 * @return  string
	 */
	function buildSql() {
		if (isset($this->_query['union'])) {
			$sql = "";
			foreach($this->_query['union'] as $part) {
				$orderby = $this->_updateOrderBy($part);
				$part['orderby'] = false;
				if (!empty($sql)) {
					$sql .= "\nUNION\n";
				}
				$sql .= $this->_buildQuery($part);
			}
			if (!empty($orderby['orderby'])) {
				$sql .= " ORDER BY ".$orderby['orderby'];
			}
			if (isset($this->_query['limit']) && strlen($this->_query['limit']) > 0) {
				$sql .= " LIMIT " . $this->_query["limit"];
			}
		}
		else {
			$this->_query = $this->_updateOrderBy($this->_query);
			$sql = $this->_buildQuery($this->_query);
		}
		return $sql;
	}
}
?>
