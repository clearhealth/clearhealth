<?php
/*****************************************************************************
*       AlterTable.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class AlterTable extends XMLParserAbstract {

	protected $_dbName = '';
	protected $_newTables = array(); // list of all new tables
	protected $_tables = array(); // list of all existing tables
	protected $_changes = array(); // diff results container
	protected $_sqlFile = ''; // sqlchanges.sql location
	protected $_sequenceIds = array('-1'=>'');

	protected $_fd = null;
	protected $_tsStart = false;
	protected $_tsData = array();
	protected $_tdStart = false;
	protected $_tdData = array();
	protected $_tdRowStart = false;
	protected $_tdRowData = array();
	protected $_currentTagName = '';
	protected $_currentAttribs = array();
	protected $_withSql = true;
	protected $_tableColumnsCtr = array();
	protected $_existingEnums = array();
	protected $_sqlInsert = '';

	public function __construct() {
		$config = Zend_Registry::get('config');
		$this->_dbName = $config->database->params->dbname;
		parent::__construct();
	}

	public function startElement($parser,$name,array $attribs) {
		$this->_depth++;
		$this->_currentTagName = $name;
		$this->_currentAttribs = $attribs;

		// depth: 1 = mysqldump, 2 = database, 3 = table_structure/table_data, 4 = field/key/options/row, 5 = field
		if ($this->_depth == 2 && $name == 'database' && $attribs['name'] == 'chmed') { // other database names are ignored
			$this->_dbName = $attribs['name'];
			$this->_populateDbTables();
			if ($this->_withSql) {
				$tmp = array("CREATE DATABASE IF NOT EXISTS `{$this->_dbName}`;");
				$tmp[] = "USE `{$this->_dbName}`;";
				fwrite($this->_fd,implode("\n",$tmp)."\n");
			}
		}
		else if ($this->_tdStart && $this->_depth == 4 && $name == 'row') {
			$this->_tdRowStart = true;
			$this->_tdRowData['attribs'] = $attribs;
			$this->_tdRowData['data'] = array();
		}
		else if ($this->_tsStart && $this->_depth == 4) {
			$this->_tsData['structure'][$name][] = $this->_currentAttribs;
		}
		else if ($this->_depth == 3 && $name == 'table_structure') {
			$this->_tsStart = true;
			$this->_tsData['attribs'] = $attribs;
			$this->_tsData['structure'] = array();
			$this->_checkTableName($attribs['name']);
		}
		else if ($this->_depth == 3 && $name == 'table_data' && isset($this->_tables[$attribs['name']])) { // structure for table data must exist
			$this->_tdStart = true;
			$this->_tdData['attribs'] = $attribs;
			$this->_tdData['data'] = array();
			$this->_tdFirstRow = true;
			if (!$this->_withSql) return;
			$tableName = $attribs['name'];

			$db = Zend_Registry::get('dbAdapter');
			$columnNames = array();
			foreach ($this->_tables[$tableName] as $fieldName=>$col) {
				$columnNames[] = $db->quoteIdentifier($fieldName);
			}
			$this->_sqlInsert = "\nINSERT INTO ".$db->quoteTableAs($tableName)." (".implode(',',$columnNames).") VALUES";
		}
	}

	public function characterData($parser,$data) {
		// depth: 1 = mysqldump, 2 = database, 3 = table_structure/table_data, 4 = field/key/options/row, 5 = field
		if (!isset($this->_currentAttribs['name']) || trim($data,"\t\n\r") == '') return;
		//$data = trim($data,"\t\n\r");
		$tmp = array();
		$tmp['name'] = $this->_currentTagName;
		$tmp['attribs'] = $this->_currentAttribs;
		$tmp['value'] = $data;
		if ($this->_tdRowStart) {
			if (!isset($this->_tdRowData['data'][$this->_currentAttribs['name']])) {
				$this->_tdRowData['data'][$this->_currentAttribs['name']] = $tmp;
			}
			else {
				$this->_tdRowData['data'][$this->_currentAttribs['name']]['value'] .= $data;
			}
		}
		else if ($this->_tsStart) {
			if (!isset($this->_tsData['data'][$this->_currentAttribs['name']])) {
				$this->_tsData['data'][$this->_currentAttribs['name']] = $tmp;
			}
			else {
				$this->_tsData['data'][$this->_currentAttribs['name']]['value'] .= $data;
			}
		}
		else if ($this->_tdStart) {
			if (!isset($this->_tdData['data'][$this->_currentAttribs['name']])) {
				$this->_tdData['data'][$this->_currentAttribs['name']] = $tmp;
			}
			else {
				$this->_tdData['data'][$this->_currentAttribs['name']]['value'] .= $data;
			}
		}
	}

	public function endElement($parser,$name) {
		if ($this->_tdStart && $this->_depth == 4 && $name == 'row') {
			$this->_tdRowStart = false;
			$ret = $this->_checkTableDataRow();
			$this->_tdRowData = array();
		}
		else if ($this->_depth == 3 && $name == 'table_structure') {
			$this->_tsStart = false;
			$ret = $this->_checkTableStructure();
			if ($this->_withSql) {
				fwrite($this->_fd,$ret);
			}
			$this->_tsData = array();
		}
		else if ($this->_depth == 3 && $name == 'table_data') {
			$this->_tdStart = false;
			$this->_checkTableData();
			$this->_tdData = array();
			if ($this->_withSql) {
				fwrite($this->_fd,";\n");
			}
		}
		$this->_depth--;
	}

	protected function _checkTableFields($tableName,$data) {
		$sql = array();
		if (isset($this->_newTables[$tableName])) { // new table
			return $sql;
		}
		$db = Zend_Registry::get('dbAdapter');
		foreach ($data['structure'] as $objType=>$values) {
			if ($objType == 'field') {
				foreach ($values as $attribs) {
					$xmlFieldName = isset($attribs['Field'])?$attribs['Field']:'';
					$type = isset($attribs['Type'])?$attribs['Type']:'';
					$null = isset($attribs['Null'])?$attribs['Null']:'';
					if (!isset($this->_tables[$tableName][$xmlFieldName])) {
						$sql[] = "ALTER TABLE `$tableName` ADD `$xmlFieldName` " . $type . (($null == "NO") ? " NOT NULL " : " NULL ") . ";\n";
					}
				}
			}
		}
		return $sql;
	}

	protected function _checkTableStructure() {
		$data = $this->_tsData;
		$ret = false;
		$sql = '';
		$tableName = isset($data['attribs']['name'])?$data['attribs']['name']:'';
		trigger_error("Checking structure for table: $tableName",E_USER_NOTICE);
		if (isset($this->_tables[$tableName]) && !isset($this->_newTables[$tableName])) {
			trigger_error("Table $tableName exists",E_USER_NOTICE);
			$retval = $this->_checkTableFields($tableName,$data);
			$ctr = count($retval);
			if ($ctr > 0) {
				$sql .= implode("\n",$retval);
				$this->_changes[] = $ctr.' alter statements for table '.$tableName;
			}
		}
		else {
			$msg = 'New table '.$tableName;
			$this->_changes[] = $msg;
			trigger_error($msg,E_USER_NOTICE);
			$sql = "CREATE TABLE `{$tableName}` (\n";
			$keys = array();
			$hasStructure = false;

			foreach ($data['structure'] as $objType=>$values) {
				$hasStructure = true;
				if ($objType == 'field') {
					foreach ($values as $attribs) {
						$fieldName = isset($attribs['Field'])?$attribs['Field']:'';
						$type = isset($attribs['Type'])?$attribs['Type']:'';
						$null = isset($attribs['Null'])?$attribs['Null']:'';
						$key = isset($attribs['Key'])?$attribs['Key']:'';
						$default = isset($attribs['Default'])?$attribs['Default']:'';
						$extra = isset($attribs['Extra'])?$attribs['Extra']:'';
						$sql .= "\t`{$fieldName}` {$type} " . (($null == 'NO') ? ' NOT NULL ' : ' NULL ') . ",\n";
						// table structure in global _tables container variable
						$row = array();
						$row['Field'] = $fieldName;
						$row['Type'] = $type;
						$row['Null'] = $null;
						$row['Key'] = $key;
						$row['Default'] = $default;
						$row['Extra'] = $extra;
						if ($this->_tables[$tableName] === true) {
							$this->_tables[$tableName] = array();
						}
						$this->_tables[$tableName][$row['Field']] = $row;
					}
				}
				elseif ($objType == 'key') {
					foreach ($values as $attribs) {
						$xmlKeyName = isset($attribs['Key_name'])?$attribs['Key_name']:'';
						$nonUnique = isset($attribs['Non_unique'])?$attribs['Non_unique']:'';
						if ($xmlKeyName == 'PRIMARY') {
							$xmlKeyName = 'PRIMARY KEY';
						}
						else if ($nonUnique == 0) {
							$xmlKeyName = "UNIQUE KEY `" . $xmlKeyName . "`";
						}
						else if ($nonUnique == 1) {
							$xmlKeyName = "KEY `" . $xmlKeyName . "`";
						}

						if (!isset($keys[$xmlKeyName])) {
							$keys[$xmlKeyName] = array();
						}
						$keys[$xmlKeyName][] = isset($attribs['Column_name'])?$attribs['Column_name']:'';
					}
				}
			}
			if (!$this->_withSql || !$hasStructure) {
				return '';
			}

			foreach ($keys as $keyName => $keyData) {
				$sql .= "\t$keyName (`" . implode('`,`',$keyData) . "`),\n";
			}
			$sql = substr($sql,0,-2) . "\n";
			$sql .= ") ENGINE=INNODB DEFAULT CHARSET=utf8;\n\n";
		}
		return $sql;
	}

	protected function _checkTableName($tableName) {
		if (!isset($this->_tables[$tableName])) {
			$this->_tables[$tableName] = true;
			$this->_newTables[$tableName] = true;
		}
	}

	protected function _checkTableDataRow() {
		$data = $this->_tdRowData;
		$db = Zend_Registry::get('dbAdapter');
		$tableName = isset($this->_tdData['attribs']['name'])?$this->_tdData['attribs']['name']:'';
		//trigger_error("Checking data for table: $tableName",E_USER_NOTICE);

		$rows = array();
		$ret = true;
		$tableColumns = isset($this->_tables[$tableName])?$this->_tables[$tableName]:array();
		if ($tableColumns === true) {
			$tableColumns = array();
		}
		$primaryKey = null;
		$guidRow = null;
		foreach ($data['data'] as $val) {
			$fieldName = isset($val['attribs']['name'])?$val['attribs']['name']:'';
			if (isset($this->_tables[$tableName]) && isset($this->_tables[$tableName][$fieldName]) && $this->_tables[$tableName][$fieldName]['Key'] == 'PRI') {
				$primaryKey = $fieldName;
			}
			$objType = isset($val['name'])?$val['name']:'';
			if ($objType != 'field' || !array_key_exists($fieldName,$tableColumns)) {
				continue;
			}
			$fieldValue = isset($val['value'])?$val['value']:'';
			if ($fieldName == 'guid') {
				$sqlSelect = $db->select()
						->from($this->_dbName.'.'.$tableName)
						->where('guid != ?','')
						->where('guid = ?',$fieldValue);
				if ($guidRow = $db->fetchRow($sqlSelect)) { // data already exists
					if ($this->_withSql && $primaryKey !== null) {
						if (preg_match('/\[@nextSequenceId=(\d+)\]/',$tableColumns[$primaryKey],$matches)) {
							$tableColumns[$primaryKey] = $guidRow['enumerationId'];
							$key = -1;
							if (strlen($matches[1]) > 0) {
								$key = $matches[1];
							}
							$this->_sequenceIds[$key] = $tableColumns[$primaryKey];
							$this->_existingEnums[$tableColumns[$primaryKey]] = $tableColumns[$primaryKey];
						}
					}
				}
			}
			if (preg_match_all('/\[@lastSequenceId=(\d+)\]/',$fieldValue,$matches)) {
				$key = -1;
				if (strlen($matches[1][0]) > 0) {
					$index = $matches[1][0];
					if (isset($this->_sequenceIds[$index])) {
						$key = $index;
					}
				}
				if (count($matches[1]) > 1) {
					foreach ($matches[1] as $match) {
						$index = -1;
						if (isset($this->_sequenceIds[$match])) {
							$index = $match;
						}
						$fieldValue = preg_replace('/\[@lastSequenceId='.$match.'\]/mi',$this->_sequenceIds[$index],$fieldValue);
					}
				}
				else {
					$fieldValue = $this->_sequenceIds[$key];
				}
			}
			$tableColumns[$fieldName] = $fieldValue;
		}
		if ($this->_withSql && $primaryKey !== null) {
			if (preg_match('/\[@nextSequenceId=(\d+)\]/',$tableColumns[$primaryKey],$matches)) {
				$tableColumns[$primaryKey] = WebVista_Model_ORM::nextSequenceId();
				$key = -1;
				if (strlen($matches[1]) > 0) {
					$key = $matches[1];
				}
				$this->_sequenceIds[$key] = $tableColumns[$primaryKey];
				trigger_error('nextSequenceId generated for: '.$tableName.'.'.$primaryKey,E_USER_NOTICE);
			}
		}
		if (!isset($this->_tableColumnsCtr[$tableName])) {
			$this->_tableColumnsCtr[$tableName] = 0;
		}
		if (!($guidRow || ($tableName == 'enumerationsClosure' && isset($tableColumns['descendant']) && isset($this->_existingEnums[$tableColumns['descendant']])))) {
			$this->_tableColumnsCtr[$tableName]++;
			if (!$this->_withSql) return '';
			//trigger_error(print_r($tableColumns,true));
			$row = array();
			foreach ($this->_tables[$tableName] as $columnName=>$col) { // making sure row columns are in order
				$row[$columnName] = (isset($tableColumns[$columnName]) && is_string($tableColumns[$columnName]))?$tableColumns[$columnName]:'';
			}
			if ($this->_tdFirstRow) {
				$this->_tdFirstRow = false;
			}
			fwrite($this->_fd,$this->_sqlInsert."(".$db->quote($row).');');
		}
	}

	protected function _checkTableData() {
		$db = Zend_Registry::get('dbAdapter');
		$tableName = isset($this->_tdData['attribs']['name'])?$this->_tdData['attribs']['name']:'';
		trigger_error("Checking data for table: $tableName",E_USER_NOTICE);
		if ($this->_tableColumnsCtr[$tableName] > 0) {
			$this->_changes[] = $this->_tableColumnsCtr[$tableName].' insert statements to table '.$tableName;
		}
		return true;
	}

	protected function _populateTableDefinitions($filename) {
		$this->_populateDbTables();
		$this->parse($filename);
	}

	protected function _populateDbTables($dbName=null) {
		if ($dbName === null) $dbName = $this->_dbName;
		$this->_tables = array();
		$db = Zend_Registry::get('dbAdapter');
		$dbRes = $db->query('SHOW DATABASES LIKE '.$db->quote($dbName));
		if (!$dbRes->fetch()) return;
		$tableRes = $db->query('SHOW TABLES FROM `'.$dbName.'`');
		$tableRes->setFetchMode(Zend_Db::FETCH_NUM);
		foreach ($tableRes->fetchAll() as $table) {
			$this->_tables[$table[0]] = true;
		}
		foreach ($this->_tables as $tableName=>$value) {
			$res = $db->query("SHOW COLUMNS FROM `$dbName`.`$tableName`");
			$res->setFetchMode(Zend_Db::FETCH_ASSOC);
			$this->_tables[$tableName] = array();
			foreach ($res->fetchAll() as $row) {
				$this->_tables[$tableName][$row['Field']] = $row;
			}
		}
	}

	public function getChanges($data) {
		return $this->_changes;
	}

	public function generateChanges($filename) {
		$this->_changes = array();
		$this->_withSql = false;
		$this->_generateSql($filename);
		return $this->_changes;
	}

	public function executeSqlChanges() {
		$config = Zend_Registry::get('config');
		$dbParams = $config->database->params;
		$mysqlClient = exec('which mysql');
		if ($config->bin && $config->bin->mysqlClient) $mysqlClient = (string)$config->bin->mysqlClient; // overrides autodetected mysql client directory
		$cmd = $mysqlClient.' -f --user="'.$dbParams->username.'"';
		if (strlen($dbParams->password) > 0) {
			$cmd .= ' --password="'.$dbParams->password.'"';
		}
		$cmd .= ' --max_allowed_packet=100M --database='.$dbParams->dbname.' < '.$this->_sqlFile;
		trigger_error('Executing command: '.$cmd,E_USER_NOTICE);
		$ret = exec($cmd);
		unlink($this->_sqlFile);
		return $ret;
	}

	public function generateSqlChanges($filename) {
		$this->_withSql = true;
		return $this->_generateSql($filename);
	}

	protected function _generateSql($filename) {
		$this->_existingEnums = array();
		$ts = self::calcTS();
		$msg = 'generating update file';
		if ($this->_withSql) {
			$msg .= ' with SQL';
		}
		else {
			$msg .= ' without SQL';
		}
		$msg .= ': ';
		trigger_error('before '.$msg.$ts,E_USER_NOTICE);
		if ($this->_withSql) {
			$this->_sqlFile = tempnam(Zend_Registry::get('basePath').'tmp/','sqlchanges_');
			if (!$this->_fd = fopen($this->_sqlFile,'a+')) {
				$msg = __('Could not write to temporary file: '.$this->_sqlFile);
				trigger_error($msg,E_USER_NOTICE);
				return $msg;
			}
		}
		$this->_populateTableDefinitions($filename);
		if ($this->_fd) fclose($this->_fd);
		$te = self::calcTS();
		trigger_error('after '.$msg.$te,E_USER_NOTICE);
		$elapse = $te - $ts;
		trigger_error('time elapsed: '.$elapse,E_USER_NOTICE);
		return true;
	}

	public static function calcTS() {
		list($usec, $sec) = explode(" ", microtime());
		$ts = ((float)$usec + (float)$sec);
		if (!isset($GLOBALS['gts'])) $GLOBALS['gts'] = $ts;
		return $ts-$GLOBALS['gts'];
	}

}
