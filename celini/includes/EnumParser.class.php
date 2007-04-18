<?php

// Required for mock testing
require_once CELINI_ROOT . '/lib/adodb/adodb.inc.php';

class EnumParser
{
	var $_db = null;
	var $_column = null;
	var $_table = null;
	
	function EnumParser(&$db, $column, $table = 'enumeration') {
		$this->_db =& $db;
		$this->_column = $column;
		$this->_table = $table;
	}
	
	function parse() {
		$sql = 'SHOW COLUMNS FROM ' . $this->_table . ' LIKE "' . $this->_column . '"';
		$result = $this->_db->getAssoc($sql);
		$row = $result['role'];
		$enumValues = explode("','", preg_replace('/(enum|set)\(\'(.+?)\'\)/', '\\2', $row['Type']));	
		
		return $enumValues;	
	}
}

