<?php
// USAGE: php makeAlterTables.php <db schema xml> <live db to compare against> <flag to delete diff column>

if (!isset($argv[1])) {
	echo "You must supply a db schema xml file to compare against";
	exit;
}
if (!isset($argv[2])) {
	echo "You must supply a the live database to compare against.";
	exit;
}
$xml = simplexml_load_file($argv[1]);
mysql_connect('localhost','root');
mysql_select_db($argv[2]);
$f = fopen("sqlchanges.sql",'w');
$tableRes = mysql_query('show tables;');
$tables = array();
while($table = mysql_fetch_array($tableRes,MYSQL_NUM)) {
	$tables[$table[0]] = true;
}
foreach ($xml as $table) {
	foreach($table as $structure) {
		$tableName = (string)$structure->attributes()->name;
		if (isset($tables[$tableName])) {
			echo "Table: " . $tableName . " found, checking fields...\n";
			$retval = checkFields($tableName,$structure);
			if ($retval > 0) {
				echo $retval . " alter statements generated.\n";
			}
			else {
				echo "Confirmed fields match.\n";
			}
		}
		else {
			echo "Table: " . $tableName . " was not found, generating create sql\n";
			$sql = "CREATE TABLE `{$structure->attributes()->name}` (\n";
			$keys = array();
			foreach($structure as $objType => $field) {
				if ($objType == "field") {
					$sql .= "\t`{$field->attributes()->Field}` {$field->attributes()->Type} " . (($field->attributes()->Null == "NO") ? " NOT NULL " : " NULL ") . ",\n";
				}
				elseif ($objType == 'key') {
					$xmlKeyName = (string)$field->attributes()->Key_name;
					if ($field->attributes()->Key_name == "PRIMARY") {
						$xmlKeyName = "PRIMARY KEY";
					}
					elseif($field->attributes()->Non_unique == 0) {
						$xmlKeyName = "UNIQUE KEY `" . $xmlKeyName . "`";
					}
					elseif($field->attributes()->Non_unique == 1) {
						$xmlKeyName = "KEY `" . $xmlKeyName . "`";
					}

					if (!isset($keys[$xmlKeyName])) $keys[$xmlKeyName] = array(); 
					$keys[$xmlKeyName][] = (string)$field->attributes()->Column_name;
				}
				//var_dump($field);
			}
			foreach ($keys as $keyName => $keyData) {
				$sql .= "\t$keyName (`" . implode('`,`',$keyData) . "`),\n";
			}
			$sql = substr($sql,0,-2) . "\n";
			$sql .= ") ENGINE=INNODB DEFAULT CHARSET=utf8;\n\n";
			fwrite($f, $sql);
		}
	}
}
fclose($f);

function checkFields($tableName,$structure) {
	$changes = 0;
	global $f, $argv;
	$sql = 'SHOW COLUMNS FROM '.$tableName;
	$res = mysql_query($sql);
	$tableData = array();
	$primaryKeyExists = false;
	while ($row = mysql_fetch_array($res)) {
		$tableData[$row['Field']] = $row;
		if ($row['Key'] == 'PRI') $primaryKeyExists = true;
	}
	//var_dump($tableData);
	$columns = array();
	foreach ($structure as $objType => $fieldData) {
		if ($objType == 'field') {
			$xmlFieldName = (string)$fieldData->attributes()->Field;
			if (!isset($tableData[$xmlFieldName])) {
				echo "\tField: " . $xmlFieldName . " was not found, generating alter table.\n";
				$sql = "ALTER TABLE `$tableName` ADD `$xmlFieldName` " . (string)$fieldData->attributes()->Type . (($fieldData->attributes()->Null == "NO") ? " NOT NULL " : " NULL ") . ";\n";
				fwrite($f, $sql);
				$changes++;
			}
			else if (isset($argv[3]) && $argv[3] == '1') {
				$columns[$xmlFieldName] = $xmlFieldName;
				$type = strtolower((string)$fieldData->attributes()->Type);
				if ($type != $tableData[$xmlFieldName]['Type']) { // check if Type is changed
					echo "\tField: " . $xmlFieldName . " type was changed, generating alter column.\n";
					$sql = "ALTER TABLE `$tableName` CHANGE `$xmlFieldName` `$xmlFieldName` " . $type . (($fieldData->attributes()->Null == "NO") ? " NOT NULL " : " NULL ") . ";\n";
					fwrite($f, $sql);
					$changes++;
				}
			}
		}
		elseif ($objType == 'key') {
			$xmlFieldName = (string)$fieldData->attributes()->Column_name;
			$xmlKey = null;
			$xmlKeyName = (string)$fieldData->attributes()->Key_name;
			if ($xmlKeyName == 'PRIMARY' && (!isset($tableData[$xmlFieldName]) || $tableData[$xmlFieldName]['Key'] != 'PRI')) {
				$xmlKey = 'PRIMARY KEY';
			}
			elseif($fieldData->attributes()->Non_unique == 0 && (!isset($tableData[$xmlFieldName]) || $tableData[$xmlFieldName]['Key'] != 'UNI')) {
				//$xmlKey = 'UNIQUE KEY';
			}
			elseif($fieldData->attributes()->Non_unique == 1 && (!isset($tableData[$xmlFieldName]) || $tableData[$xmlFieldName]['Key'] != 'MUL')) {
				//$xmlKey = 'KEY';
			}
			if ($xmlKey !== null) {
				echo "\tField: " . $xmlFieldName . " key was changed, generating alter column key.\n";
				$sql = "ALTER TABLE `$tableName`";
				// check if primary key exists
				if ($xmlKey == 'PRIMARY KEY' && $primaryKeyExists) {
					$sql .= ' DROP PRIMARY KEY,';
				}
				$sql .= " ADD $xmlKey(`$xmlFieldName`);\n";
				fwrite($f, $sql);
				$changes++;
			}
		}
		//var_dump($objType);
		//var_dump($field);
	}

	if (isset($argv[3]) && $argv[3] == '1') {
		// removed un-used fields/columns
		foreach ($tableData as $key=>$value) {
			if (isset($columns[$key])) continue;
			echo "\tField: " . $key. " was deleted, generating drop column.\n";
			$sql = "ALTER TABLE `$tableName` DROP `$key`;\n";
			fwrite($f, $sql);
			$changes++;
		}
	}
	return $changes;
}
