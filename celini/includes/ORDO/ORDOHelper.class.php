<?php
/**
 * Miscellaneous methods relating to various ORDO functions.
 *
 * Covers polulating them from a db or and array
 * Covers persisting them to a db
 *
 * @author  Travis Swicegood <tswicegood@uversainc.com>
 * @author Joshua Eichorn <jeichorn@mail.com>
 * @package com.uversainc.celini
 */
class ORDOHelper
{
	/**
	 * Instance of clniDb
	 */
	var $db;

	function ORDOHelper() {
		$this->db = new clniDB();
	}

	/**
	 * Used by the factory* methods to determine the real name of what has
	 * been requested of them.
	 *
	 * @param  string
	 * @return string
	 */
	function getName($object) {
		//$realName = ucfirst($object);
		$realName = $object;
		if (isset($GLOBALS['configObj'])) {
			if (!is_null($classNameMap = $GLOBALS['configObj']->get('classNameMap'))) {
				$realName = isset($classNameMap[$realName]) ?
					$classNameMap[$realName] : $realName;
			}
		}
		return $realName;
	}

	/**
	 * Populate an ordo from an array of data
	 *
	 * @param ORDataObject	$ordo
	 * @param array		$input
	 */
	function populateFromArray(&$ordo,$input) {
		if (is_array($input)) {
			foreach ($input as $fieldName => $field) {
				
				// if were a storage array handle specifically else use the unified setter
				if ($fieldName === "int" && is_array($field) && isset($ordo->_int_storage)) {
					foreach($field as $key => $val) {
						$ordo->_int_storage->set($key,(int)$val);
					}
				}
				else if ($fieldName === "date" && is_array($field) && isset($ordo->_date_storage)) {
					foreach($field as $key => $val) {
						$ordo->_date_storage->set($key,$val);
					}
				}
				else if ($fieldName === "string" && is_array($field) && isset($ordo->_string_storage)) {
					foreach($field as $key => $val) {
						$ordo->_string_storage->set($key,$val);
					}
				}
				else if ($fieldName === "text" && is_array($field) && isset($ordo->_text_storage)) {
					foreach($field as $key => $val) {
						$ordo->_text_storage->set($key,$val);
					}
				}
				else {
					$ordo->set($fieldName,$field);
				}
			}
		}
	}

	/**
	 * Populate an ordo from the database
	 *
	 * @param ORDataObject	$ordo
	 * @param string|true	$keyField
	 */
	function populateFromDB(&$ordo,$keyField = true) {
		$sql = "SELECT * from " .$ordo->tableName();

		if ($keyField === true) {
			$sql .= " WHERE ".$this->db->genSqlPrimaryKeyWhere($ordo);
		} 
		else {
			$keyField = $this->db->escape($keyField);
			$sql .= " WHERE `$keyField` = " . $this->db->quote($ordo->get($keyField));
		}

		$this->populateFromQuery($ordo, $sql);
	}
	
	
	/**
	 * Populate an ordo from a database query
	 *
	 * Unlike {@link populateFromResults()}, this populates all storage values.
	 *
	 * @param  ORDataObject
	 * @param  string
	 */
	function populateFromQuery(&$ordo, $sql) {
		$result = $ordo->dbHelper->execute($sql);
		$this->populateFromResults($ordo, $result,true);
	}
	
	
	/**
	 * Populate an ordo from a database result object
	 *
	 * @param ORDataObject The ORDO we're working on. 
	 * @param ADORecordSet The results to populate with
	 */
	function populateFromResults(&$ordo, &$results,$populateStorage = false) {
		if (!$results || $results->EOF) {
			return;
		}
		
		$this->populateFromArray($ordo, $results->fields);

		if ($populateStorage) {
			$this->populateStorageValues($ordo);
		}
		$ordo->_populateMetaData($results);
		$ordo->metadata->populate($results);
		$ordo->_populated = true;
	}

	
	/**
	 * Populates all of the storage values for a given ORDO
	 *
	 * @param  ORDataObject
	 */
	function populateStorageValues(&$ordo) {
		if ($ordo->get('id') <= 0) {
			// can't do anything
			return;
		}
		
		// todo: refractor this into a smaller foreach() loop.
		// todo: consider move to _init/populateStorage()
		if (is_a($ordo->_int_storage,'Storage')) {
			$ordo->_int_storage->foreign_key = $ordo->get('id');
			$ordo->_int_storage->populate();
		}

		if (is_a($ordo->_date_storage,'Storage')) {
			$ordo->_date_storage->foreign_key = $ordo->get('id');
			$ordo->_date_storage->populate();
		}
		
		if (is_a($ordo->_string_storage,'Storage')) {
			$ordo->_string_storage->foreign_key = $ordo->get('id');
			$ordo->_string_storage->populate();
		}
		if (is_a($ordo->_text_storage,'Storage')) {
			$ordo->_text_storage->foreign_key = $ordo->get('id');
			$ordo->_text_storage->populate();
		}
	}
	
	/**
	 * Create an array of data from an ordo
	 *
	 * @param ORDataObject	$ordo
	 */
	function persistToArray(&$ordo) {
		$ret = array();
		$fields = $ordo->metadata->listFields();
		foreach($fields as $field) {
			$ret[$field] = $ordo->get($field);
		}
		return $ret;
	}
	
	
	/**
	 * Returns the column_def for a given ORDO's storage values
	 *
	 * @param  ORDataObject
	 * @return string
	 */
	function storageColumnAliases(&$ordo) {
		$ret = array();
		foreach ($ordo->storage_metadata as $storageType => $storageNames) {
			if (count($storageNames) <= 0) {
				continue;
			}
			
			foreach ($storageNames as $storageFieldName => $defaultValue) {
				$ret[] = $storageType . '_' . $storageFieldName . '.value AS ' . $storageFieldName;
			}
		}
		
		return implode(', ', $ret);
	}
	
	/**
	 * Returns the table joins for a given ORDO's storage values
	 *
	 * {@internal This currently will not work with ORDO's that have multiple primary keys unless 
	 *    the first key is what the storage data is tied to.}
	 *
	 * @param  ORDataObject
	 * @return string
	 */
	function storageTableJoins(&$ordo) {
		$ordoKey = array_shift($this->db->primaryKeys($ordo->tableName()));
		
		$ret = array();
		foreach ($ordo->storage_metadata as $storageType => $storageNames) {
			if (count($storageNames) <= 0) {
				continue;
			}
			
			foreach ($storageNames as $storageFieldName => $defaultValue) {
				$tableAlias = $storageType . '_' . $storageFieldName;
				$joinSql = 'LEFT JOIN storage_' . $storageType . ' AS ' . $tableAlias;
				$joinSql .= ' ON(' . $tableAlias . '.foreign_key = ordo.' . $ordoKey . ' AND 
					' . $tableAlias . '.value_key = ' . $this->db->quote($storageFieldName) . ')';
				$ret[] = $joinSql;
			}
		}
		
		return implode("\n", $ret);
	}
	 

	/**
	 * Persist an ordo to the database
	 *
	 * @param ORDataObject	$ordo
	 */
	function persistToDB(&$ordo) {
		$ordo->_inPersist = true;

		$fields = $this->db->listFields($ordo->tableName());
		$pkeys  = $this->db->primaryKeys($ordo->tableName());
		if (count($pkeys) > 1 ) {
			// multiple primary keys shouldn't use sequences
			$pkeys = array();
		}
		$pwhere = $this->db->genSqlPrimaryKeyWhere($ordo);

		if ($this->db->getOne('select count(*) from '.$ordo->tableName() .' where '.$pwhere)) {
			$sql = "UPDATE ";
			$update = true;
		}
		else {
			$sql = "REPLACE INTO "; 
			$update = false;
		}
		$sql .= $ordo->tableName() . " SET ";
		

		$runUpdate = false;
		$populateAfter = false;
		foreach ($fields as $field) {
			//echo "f: $field m: $func status: " .  (is_callable(array($this,$func))? "yes" : "no") . "<br>";
			if ($ordo->exists($field)) {
				$val = $ordo->get($field);
				if (in_array($field,$pkeys)  && empty($val)) {
					$last_id = $this->db->nextId("sequences");
					$ordo->set($field,$last_id);
					$val = $last_id;

					if ($ordo->_createOwnership) {
						// add an ownership entry
						$me =& Me::getInstance();
						$myid = $me->get_id();
						$this->db->execute("insert into ownership values ($last_id,$myid)");
					}
					if ($ordo->_createRegistry) {
						// add a ordo_registry entry
						$me =& Me::getInstance();
						$myid = $me->get_id();
						$this->db->execute("insert into ordo_registry values ($last_id,$myid,$myid)");
					}
				}
				else if (in_array($field,$pkeys) && $update) {
					unset($val);
				}

				if (isset($val) ) {
					//echo "s: $field to: $val <br>";
					if (is_object($val) && is_a($val,'clniValue')) {
						$sql .= " `$field` = ".$val->toSql().',';
						$populateAfter = true;
					}
					else {
						$sql .= " `$field` = " . $this->db->quote($val) .",";
					}
					$runUpdate = true;
				}
			}
		}

		if (strrpos($sql,",") == (strlen($sql) -1)) {
				$sql = substr($sql,0,(strlen($sql) -1));
		}

		if ($update) {
			$sql .= " WHERE $pwhere";
		}

		//echo "<br>sql is: " . $sql . "<br /><br>";
		$sqlStatus = false;
		if ($runUpdate) {
			$sqlStatus = $this->db->execute($sql);
		}
		if ($runUpdate === false  || $sqlStatus) {
			if ($ordo->get('id') > 0) {
				if (is_a($ordo->_int_storage,'Storage')) {
					$ordo->_int_storage->foreign_key = $ordo->get('id');
					$ordo->_int_storage->persist();
				}

				if (is_a($ordo->_date_storage,'Storage')) {
					$ordo->_date_storage->foreign_key = $ordo->get('id');
					$ordo->_date_storage->persist();
				}
				
				if (is_a($ordo->_string_storage,'Storage')) {
					$ordo->_string_storage->foreign_key = $ordo->get('id');
					$ordo->_string_storage->persist();
				}
				if (is_a($ordo->_text_storage,'Storage')) {
					$ordo->_text_storage->foreign_key = $ordo->get('id');
					$ordo->_text_storage->persist();
				}
			}

			$ordo->_inPersist = false;
			if ($populateAfter) {
				$this->populateFromDb($ordo);
			}
			return true;
		}
		$ordo->_inPersist = false;
		return false;
	}

	/** 
	 * Store changes to the audit log
	 */
	function audit(&$ordo,$meta,$auditFields,$drop = false) {
		$GLOBALS['loader']->requireOnce('includes/clni/clniAudit.class.php');

		$audit = new clniAudit();
		if ($drop) {
			$audit->logOrdoDrop($ordo,$meta,$auditFields);
		}
		else {
			$audit->logOrdoChanges($ordo,$meta,$auditFields);
		}
	}
}
