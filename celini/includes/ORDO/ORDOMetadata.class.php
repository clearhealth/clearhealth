<?php

/**#@+
 * Loads required file
 */
$loader->requireOnce('includes/EnforceType.class.php');
/**#@-*/

/**
 * Provides metadata about a ordo
 *
 * Mainly type information about fields
 *
 * @package com.uversainc.celini
 * @author Joshua Eichorn <jeichorn@mail.com>
 */
class ORDOMetadata {

	/**
	 * A reference to the parent ordo
	 */
	var $_ordo;

	/**
	 * Fields in this ordo
	 */
	var $_fields = array();

	/**
	 * Primary keys
	 */
	var $_pkeys = array();

	/**
	 * A type enforcer
	 */
	var $_typeEnforcer;

	/**
	 * Has the metadata been populated
	 */
	var $_populated = false;
	
	/**
	 * An array of fields that have been changed
	 *
	 * @var array
	 */
	var $_changedFields = array();

	/**
	 * Place to store new field values before the object has been populated
	 */
	var $_newFieldValues = array();

	/**
	 * Set the parent ordo
	 */
	function setORDO(&$ordo) {
		$this->_ordo =& $ordo;
		$this->_typeEnforcer = new EnforceType();
	}

	/**
	 * Get a list of all the fields in this ordo
	 */
	function listFields() {
		if ($this->_populated === false) {
			$this->populate();
		}
		return array_keys($this->_fields);
	}

	/**
	 * Get a type for a field (this can be a real field or a storage field
	 *
	 * These types map to the types supported by the {@link EnforceType} class
	 *
	 * @param string	$fieldName
	 */
	function getType($fieldName) {
		if (isset($this->_fields[$fieldName])) {
			return $this->_fields[$fieldName]->type;
		}
		return false;
	}

	/**
	 * Get an object containing all the fields details, see {@link ORDOMetadataField}
	 *
	 * @param string	$fieldName
	 */
	function getTypeDetails($fieldName) {
		if (isset($this->_fields[$fieldName])) {
			return $this->_fields[$fieldName];
		}
		return false;

	}

	/**
	 * Get the primary key(s) of the table
	 *
	 * @return string|array
	 */
	function getPrimaryKey() {
		if ($this->_populated === false) {
			$this->populate();
		}
		if (count($this->_pkeys) == 1) {
			return $this->_pkeys[0];
		}
		return $this->_pkeys;
	}

	/**
	 * Run a test to see if the updated field has changed, if so update the fields metadata
	 */
	function updateChanged($fieldName,$newValue) {
		if (isset($this->_fields[$fieldName])) {
			if (!is_string($newValue)) {
				$newValue = serialize($newValue);
			}
			if (strcmp($newValue,$this->_fields[$fieldName]->dbValue) != 0) {
				$this->_fields[$fieldName]->isChanged = false;
				$this->_changedFields[] = $fieldName;
				return true;
			}
		}
		else {
			$this->_newFieldValues[$fieldName] = $newValue;
		}
		return false;
	}

	/**
	 * Enforce the type specified by a given field
	 */
	function enforceType($key,$value) {
		if (is_object($value) && is_a($value,'clniValue')) {
			$type = $value->type();
		}
		else {
			$type = $this->getType($key);
		}
		if ($type && method_exists($this->_typeEnforcer,$type)) {
			$this->_typeEnforcer->$type($value);
		}
		return $value;
	}

	/**
	 * Populate meta data
	 *
	 * If the object has not already been populated, type data will be populated but not database values
	 * If using this data to build an update map it must be populate with the same key before updates start
	 */
	function populate($result = false) {
		if ($result === false) {
			$this->_populateFromTableDef();
		}
		else {
			$this->_populateFromResult($result);
		}
		$this->_populateFromStorage();

		$this->_populated = true;
		if (count($this->_newFieldValues) > 0) {
			foreach($this->_newFieldValues as $key => $val) {
				$this->updateChanged($key,$val);
			}
			$this->_newFieldValues = array();
		}
	}

	/**
	 * Populate type information from a result
	 */
	function _populateFromResult($result) {
		$pkeys = array();
		for($i =0; $i < $result->fieldCount(); $i++) {
			$f = $result->fetchField($i);

			$f->mtype = $result->MetaType($f);
			$fields[$f->name] = new ORDOMetadataField($f);
			if (isset($fields[$f->name])) {
				$fields[$f->name]->dbValue = $result->fields[$f->name];
			}
			if ($fields[$f->name]->isPrimaryKey) {
				$pkeys[] = $f->name;
			}
		}

		$this->_fields = $fields;
		$this->_pkeys = $pkeys;
		return array($fields,$pkeys);
	}

	/**
	 * Set a cache entry
	 */
	function _setCache($ordoName,$data) {
		$GLOBALS['ORDOMetadata'][$ordoName] = $data;
	}

	/**
	 * Get a cache entry
	 */
	function _getCache($ordoName) {
		if (isset($GLOBALS['ORDOMetadata'][$ordoName])) {
			return $GLOBALS['ORDOMetadata'][$ordoName];
		}
		return false;
	}

	/**
	 * Populate type information from the table definition
	 *
	 * @access private
	 */
	function _populateFromTableDef() {
		$data = $this->_getCache($this->_ordo->name());
		if ($data !== false) {
			$this->_fields = $data[0];
			$this->_pkeys = $data[1];
			return true;
		}
		$sql = 'SELECT * FROM '.$this->_ordo->tableName().' LIMIT 1';
		$data = $this->_populateFromResult($this->_ordo->dbHelper->execute($sql));
		$this->_setCache($this->_ordo->name(),$data);
	}

	/**
	 * Populate type information from storage
	 *
	 * @todo make a max_size lookup table
	 * @access private
	 */
	function _populateFromStorage() {
		//var_dump('_populateFromStorage'.$this->_ordo->tableName());
		$map = array('int' => 'I','string'=> 'C','date'=>'D', 'text' => 'X');
		foreach($this->_ordo->storage_metadata as $t => $fields) {
			foreach($fields as $fieldName => $field) {
				$c = new stdClass();
				$c->type = $t;
				$c->mtype = $map[$t];
				$c->table = "storage_" . $t; // <- is this right?
				$c->name = $fieldName;
				$c->isStorage = true;
				$c->max_length = -1;
				$c->not_null = 1;
				$c->primary_key = 0;
				$c->multiple_key = 0;
				$c->unique_key = 0;
				$this->_fields[$fieldName] = new ORDOMetadataField($c);

				$type = '_'.$t.'_storage';
				$this->_fields[$fieldName]->dbValue = $this->_ordo->$type->get($fieldName);
			}
		}
	}
	
	/**
	 * Returns an array of fields that have been modified.
	 *
	 * @return array
	 */
	function modifiedFields() {
		foreach($this->_newFieldValues as $field => $val) {
			$this->_changedFields[] = $field;
		}
		$this->_changedFields = array_unique($this->_changedFields);
		return $this->_changedFields;
	}
	
	
	/**
	 * Returns true if any of the fields have been updated
	 *
	 * @return boolean
	 */
	function isModified() {
		return (count($this->modifiedFields()) > 0);
	}

	/**
	 * Returns true if this insert will be creating a new entry
	 * The check for this is, has the primaryKey(s) been modified
	 *
	 * @return boolean
	 */
	function isNew() {
		$modified = $this->modifiedFields();

		$pkey = $this->getPrimaryKey();
		if (in_array($pkey,$modified)) {
			return true;
		}
		return false;
	}

	/**
	 * Get the db value of a field
	 */
	function dbValue($fieldName) {
		if (isset($this->_fields[$fieldName]->dbValue)) {
			return $this->_fields[$fieldName]->dbValue;
		}
	}
	
	
	/**
	 * Returns true if <i>$fieldName</i> represents a value that is a known enum.
	 *
	 * @return boolean
	 */
	function isEnum($fieldName) {
		return isset($this->_ordo->_enumList[$fieldName]);
	}
	
	
	/**
	 * Returns the name of the enum used to generate values for <i>$fieldName</i> or <i>false</i>
	 * if that value is not a known enum.
	 *
	 * @see isEnum()
	 * @return string|false
	 */
	function enumName($fieldName) {
		if (!$this->isEnum($fieldName)) {
			return false;
		}
		
		return $this->_ordo->_enumList[$fieldName];
	}
}

/**
 * Provides information about a specific field, mainly exists for documentation purposes, but utililty methods may be added if needed
 *
 * @package com.uversainc.celini
 * @author Joshua Eichorn <jeichorn@mail.com>
 */
class ORDOMetadataField {
	/**
	 * Table the field is in
	 */
	var $table;
	
	/**
	 * Name of the field
	 */
	var $name;

	/**
	 * Type of the field
	 */
	var $type;

	/**
	 * Size of the field (either string len or number len, false if it doesn't apply
	 */
	var $size = false;

	/**
	 * Is this field a storage var
	 */
	var $isStorage = false;

	/**
	 * Is this field a unique key
	 */
	var $isUnique = false;

	/**
	 * Has this field been changes since the last time it was persisted
	 */
	var $isChanged = false;

	/**
	 * can this field be null
	 */
	var $canBeNull = false;

	/**
	 * Value of the field when it was read from the db
	 */
	var $dbValue;

	/**
	 * Adodb MetaType
	 */
	var $adodbType;

	/**
	 * Is this field a primary key
	 */
	var $isPrimaryKey = false;
	
	/**
	 * Translate data from adodb into usable fields
	 */
	function ORDOMetadataField($adodbField) {
		//var_dump($adodbField);
		$this->table = $adodbField->table;
		$this->name = $adodbField->name;
		$this->adodbType = $adodbField->mtype;
		$this->size = $adodbField->max_length;
		$this->type = $adodbField->type; // this may need some mapping
		$this->canBeNull = (bool)$adodbField->not_null-1;
		if (isset($adodbField->isStorage) && $adodbField->isStorage) {
			$this->isStorage = true;
		}
		$this->isUnique = (bool)$adodbField->unique_key;
		if ($adodbField->primary_key && !$adodbField->multiple_key) {
			$this->isUnique = true;
		}
		$this->isPrimaryKey = $adodbField->primary_key;
	}
}
?>
