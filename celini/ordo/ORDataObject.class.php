<?php
/**
 * ORDataObject
 * @package com.clear-health.celini
 */

/**
*/
$loader->requireOnce('includes/clniDB.class.php');
$loader->requireOnce("ordo/Storage.class.php");
$loader->requireOnce('includes/TimestampObject.class.php');
$loader->requireOnce('includes/DatasourceFileLoader.class.php');
$loader->requireOnce('includes/ORDO/ORDOFileLoader.class.php');
$loader->requireOnce('includes/ORDO/ORDOFactory.class.php');
$loader->requireOnce('includes/ORDO/ORDOMetadata.class.php');
$loader->requireOnce('includes/ORDO/ORDOFinder.class.php');
$loader->requireOnce('includes/ORDO/RelationshipFinder.class.php');
$loader->requireOnce('ordo/Document.class.php');

/**
 * class ORDataObject
 *
 */
class ORDataObject {
	var $_prefix = "";
	var $_table;
	var $_db;
	var $_sequence_name;
	var $_populated = false;
	var $enumTable = false;
	var $_createOwnership = true;
	var $_createRegistry = false;

	var $_key = false;

	/**
	 * Allows this to communicate internally whether or not it is being executed 
	 * via {@link persist()}
	 *
	 * @var boolean
	 * @see persist()
	 * @access protected
	 */
	var $_inPersist = false;

	/**
	 * Set to true when you want data in the db format
	 * Use this instead of _inPersist when you need db formated data
	 */
	var $_dbFormat = false;

	/**
	 * Metadata for storage variables
	 *
	 * format is [type][key] = key
	 *
	 * This should be be accessed directly its just for definition purposes
	 * @access protected
	 */
	 var $storage_metadata = array('int' => array(), 'date' => array(), 'string' => array(), 'text' => array());

	/**
	 * Metadata hints
	 *
	 * This should be be accessed directly its just for definition purposes
	 * @access protected
	 */
	var $metaHints = array('hide'=>array(),'ignore'=>array(),'labels'=>array());
	
	
	/**
	 * An associative-array of properties that are enums
	 *
	 * Should be formatted 'column_name' => 'enum_name'
	 *
	 * @see value()
	 * @var array
	 * @access protected
	 */
	var $_enumList = array();
	
	
	/**
	 * An array of properties that are actually foreign keys
	 *
	 * @see list()
	 * @var array
	 * @access protected
	 */
	var $_foreignKeyList = array();
	

	/**
	 * General type metadata for all object fields, populated at populate time
	 *
	 * mtype is:
	 *
	 * C: Character fields that should be shown in a <input type="text"> tag.
	 * X: Clob (character large objects), or large text fields that should be shown in a <textarea>
	 * D: Date field
	 * T: Timestamp field
	 * L: Logical field (boolean or bit-field)
	 * N: Numeric field. Includes decimal, numeric, floating point, and real.
	 * I: Integer field.
	 * R: Counter or Autoincrement field. Must be numeric.
	 * B: Blob, or binary large objects.
	 * 
	 * @deprecated use $this->metadata object instead
	 */
	var $meta = array();

	/**
	 * instance of the metadata object
	 */
	var $metadata;

	/**
	 * Database helper instance
	 */
	var $dbHelper;

	/**
	 * ORDO helper instance
	 */
	var $helper;
	
	/**
	 * This is the value returned when a member is unknown.
	 *
	 * Generally, this will be a string.  Include "%s" somewhere in the string
	 * if you want  
	 *
	 * @var mixed
	 * @access protected
	 */
	var $_unknownMessage = 'Unknown member: %s';
	
	/**
	 * Contains the properly capitalized name of this ORDO.
	 *
	 * Will be deprecated as of PHP 5.
	 *
	 * @var string
	 * @see name()
	 * @access protected
	 */
	var $_internalName = null;
	
	
	/**
	 * Storage variables
	 */
	var $_int_storage;
	var $_date_storage;
	var $_string_storage;
	var $_text_storage;

	/**
	 * Add changes to the Audit log
	 */
	var $auditChanges = false;

	/**
	 * Audit Field Changes
	 */
	var $auditFieldChanges = false;

	/**
	 * Audit message, can be used to store additional info in the audit log, or a formated version for latter display
	 */
	var $_auditMessage;

	function ORDataObject($db = NULL, $prefix = NULL) {
		if ($db != NULL) {
		  $this->_db = $db;
		}
		else {
		  $db =& $GLOBALS['frame']['adodb']['db'];
		  if (is_object($db) && is_a($db,"adoconnection")) {
		    $this->_db =& $db;
		  }
		}

		$this->dbHelper = new clniDB();
		$this->helper = new ORDOHelper();
		$this->metadata = new ORDOMetadata();
		$this->metadata->setORDO($this);
		
		if ($prefix != NULL) {
		  $this->_prefix = $prefix;
		}
		else {
		    if (isset($GLOBALS['frame']['config']['db_prefix']) && !empty($GLOBALS['frame']['config']['db_prefix'])) {
		      $this->_prefix = $GLOBALS['frame']['config']['db_prefix'];
		    }
		}
											
		$this->_sequence_name = $this->_prefix . $this->_sequence_name;                                                                      

		if ($GLOBALS['config']['use_storage']) {
			$this->_int_storage = new Storage("int");
			$this->_date_storage = new Storage("date");
			$this->_string_storage = new Storage("string");
			if ($GLOBALS['config']['use_text_storage']) {
				$this->_text_storage = new Storage("text");
			}
			$this->storage_defaults();
		}

		$config =& Celini::configInstance();
		if ($config->get('auditChanges')) {
			$this->auditChanges = true;
			if ($config->get('auditFieldChanges')) {
				$this->auditFieldChanges = true;
			}
		}
		$this->_createOwnership = $config->get('ownership',true);
		$this->_createRegistry = $config->get('ordo_registry',false);

	}

	/**
	 * Get the table name this ordo acts on including any prefixing
	 */
	function tableName() {
		return $this->_prefix.$this->_table;
	}
	
	
	/**
	 * Returns the primary key of this ORDO's database table.
	 *
	 * @return string
	 * @todo Determine some means of handling composite keys via this
	 */
	function primaryKey() {
		return $this->_key;
	}

	
	
	/**
	 * Returns the name of this ORDO
	 *
	 * @return string
	 * @todo Consider rebuilding from the $_key field
	 */
	function name() {
		if (!is_null($this->_internalName)) {
			return $this->_internalName;
		}
		else {
			return get_class($this);
		}
	}

	/**
	 * This method should be overridden in child classes, used to set ids and such, called by the factory method
	 */
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}
	
	
	/**
	 * Setup default values for storage vars, usually called by the constructor
	 */
	function storage_defaults() {
		foreach($this->storage_metadata as $type => $metadata) {
			$index = "_{$type}_storage";
			if (isset($this->$index)) {
				foreach($metadata as $field => $val) {
					$this->$index->set($field,$val);
				}
			}
		}
	}
	
	/**
	* Store instance data into the database
	*/
	function persist() {
		// this is pretty much a hack, will need to extend the metaData object for this to work correctly in php5 at least
		$meta = $this->metadata;
		$ret = $this->helper->persistToDB($this);
		if ($this->auditChanges) {
			$this->helper->audit($this,$meta,$this->auditFieldChanges);
		}
		return $meta->isModified();
	}

	/**
	 * @deprecated
	 */
	function _makePkeyWhere() {
		Celini::deprecatedWarning("ORDataObject::_makePkeyWhere");
		return $this->dbHelper->genSqlPrimaryKeyWhere($this->_table);
	}

	/**
	 * Populate the object from database, primary key ($this->id) must be set
	 *
	 * @param	string	$id	Name of the primary key in the database
	 */
	function populate($id = true) {
		$this->helper->populateFromDb($this,$id);
	}

	/**
	 * Populate metadata from a query
	 */
	function _populateMetaData($result,$storage = true) {
		$type = array();

		for($i =0; $i < $result->fieldCount(); $i++) {
			$f = $result->fetchField($i);

			$f->mtype = $result->MetaType($f);
			$type[$f->name] = $f;
		}

		$map = array('int' => 'I','string'=> 'C','date'=>'D', 'text' => 'X');
		if ($storage) {
			foreach($this->storage_metadata as $t => $fields) {
				foreach($fields as $field_name => $field) {
					
					$c = new stdClass();
					$c->type = $t;
					$c->mtype = $map[$t];
					$c->name = $field_name;
					$type[$field_name] = $c;
				}
			}
		}
		$this->meta = $type;
	}

	/**
	 * Populate meta data even though populate hasn't been called
	 */
	function populateMetaData() {
		$res = $this->_execute("select * from $this->_prefix$this->_table limit 1");
		$this->_populateMetaData($res,true);
	}
	
	/**
	 * Add hints to the metaHints array
	 * This function is needed so that multiple level in an object hierachy can
	 * add there own metahints without overwriting the parents defaults, should
	 * be called from constructor
	 */
	function addMetaHints($type, $array) {
		if (isset($this->metaHints[$type]) && is_array($array)) {
			$this->metaHints[$type] = array_merge($this->metaHints[$type],$array);		
		}
		return false;
	}

	/**
	 * Populate the object from an array
	 *
	 * Storage variables can either be specified in type (int,date,string) subarrays or be speciefied in class metadata
	 */
	function populate_array($results) {
		Celini::deprecatedWarning("ORDataObject::populate_array");
		$this->helper->populateFromArray($this,$results);
	}

	/**
	 * Populate the object from an array
	 *
	 * Storage variables can either be specified in type (int,date,string) subarrays or be speciefied in class metadata
	 */
	function populateArray($results) {
		$this->helper->populateFromArray($this,$results);
	}

	/**
	 * @deprecated
	 */
	function _list_fields() {
		Celini::deprecatedWarning("ORDataObject::_list_fields");
		return $this->dbHelper->listFields($this->_table);
	}
	
	/**
	 * Execute an sql statement with basic error handling
	 *
	 * @param	string	$sql
	 * @deprecated
	 */
	function _execute($sql,$fetchMode = ADODB_FETCH_ASSOC) {
		Celini::deprecatedWarning("ORDataObject::_execute");
		return $this->dbHelper->execute($sql,$fetchMode);	
	}

	/**
	 * @deprecated
	 */
	function _quote($str) {
		Celini::deprecatedWarning("ORDataObject::_quote");
		return $this->dbHelper->quote($str);
	}
	
	/**
	 */
	function _case_sql() {
		$case_sql = "";
		if (isset($this->storage_metadata)) {
			$case_sql = ", ";
			foreach ($this->storage_metadata as $sm_tname => $sm) {
				$table = "storage_" . $sm_tname;
				foreach ($sm as $s_name => $s_value) {
						$case_sql .= " MAX(CASE WHEN $table.value_key = '$s_name' THEN $table.value END)  as '" . $s_name  . "', "; 
				}
			}
			$case_sql = substr($case_sql,0,-2);		}
		return $case_sql;
	}

	/**
	 * @deprecated
	 */
	function _genId($sequence_name = "sequences") {
		Celini::deprecatedWarning("ORDataObject::_genId");
		return $this->dbHelper->sequence_name;
	}
	
	/**
	 * @deprecated
	 */
	function _form_hidden_fields() {
		Celini::deprecatedWarning("ORDataObject::_form_hidden_fields");
		$field_array =  array();
		$field_string = "";
		$methods = $this->get_class_methods();
		foreach ($methods as $method) {
			if (substring($method,0,4) == "get_") {
				$field_array[substring($method,4)] = $$method();
				$field_string .= '<input type="hidden" name="'. substring($method,4) .'" value="'. $$method() .'">';	
			}	
		}	
		return $field_string;
	}

	/**
	 * Helper function that loads enumerations from the data as an array, this is also efficient
	 * because it uses psuedo-class variables so that it doesnt have to do database work for each instance
	 *
	 * @param string $field_name name of the enumeration in this objects table
	 * @param boolean $blank optional value to include a empty element at position 0, default is true
	 * @return array array of values as name to index pairs found in the db enumeration of this field  
	 * @deprecated use EnumManager instead
	 */
	function _load_enum($field_name,$blank = true) 
	{
		Celini::deprecatedWarning("ORDataObject::_load_enum");
		if ($this->enumTable === false) {
			$manager =& Celini::enumManagerInstance();
			if ($manager->enumExists($field_name)) {
				return array_flip($manager->enumArray($field_name));
			}
		}

		$table = "enumeration";
		if ($this->enumTable != false) {
			$table = $this->enumTable;
		}
		if (	isset($GLOBALS['static']['enums'][$table][$field_name]) 
			&& is_array($GLOBALS['static']['enums'][$table][$field_name]) 
		) { 
			$enum = $GLOBALS['static']['enums'][$table][$field_name];
		}
		else 
		{
			$cols = $this->_db->MetaColumns($table);
			if ($cols === false) {
				$cols = $this->_db->MetaColumns("enumerations");
			}
			$enum = array();
			if (is_array($cols)) {
				foreach($cols as $col) 
				{
					if ($col->name == $field_name && substr($col->type,0,4) == "enum") {
						preg_match_all("|[\'](.*)[\']|U",$col->type,$enum_types);
						//position 1 is where preg_match puts the matches sans the delimiters
						$enum = $enum_types[1];
						//for future use
						//$enum[$col->name] = $enum_types[1];
					}
				}
			}
			else {
				if ($cols && !$cols->EOF) 
				{
					//why is there a foreach here? at some point later there will be a scheme to autoload all enums 
					//for an object rather than 1x1 manually as it is now
					foreach($cols as $col) 
					{
						if ($col->name == $field_name && substr($col->type,0,4) == "enum") {
							preg_match_all("|[\'](.*)[\']|U",$col->type,$enum_types);
							//position 1 is where preg_match puts the matches sans the delimiters
							$enum = $enum_types[1];
							//for future use
							//$enum[$col->name] = $enum_types[1];
						}
					}
				}
			}
			array_unshift($enum," ");
			   
			$enum = array_flip($enum);
			$GLOBALS['static']['enums'][$table][$field_name] = $enum;
		}	
		//keep indexing consistent whether or not a blank is present
		if (!$blank) 
		{
			unset($enum[" "]);
		}
		return $enum;
	}

	/**
	 * Take a date and put it in format compatible with PHP's date().
	 *
	 * Example: Y-m-d (2004-01-31)
	 *
	 * Can be used statically.
	 *
	 * @param	string	$date
	 * @param	string	$format	Defaults to MySQL date format
	 * @return	string
	 * @deprecated
	 */
	function _mysqlDate($date) {
		Celini::deprecatedWarning("ORDataObject::_mysqlDate");
		if (empty($date)) {
			return '';
		}
		if ($date == "0000-00-00") {
			return $date;
		}
		if (preg_match('/^(\d{1,2})[\/-](\d{1,2})$/',$date,$match)) {
			$date = date('Y')."-".$match[1]."-".$match[2];
		}
		
		//special handling of dates before 1971 due to strtotime problems with that on many platforms
		$ret = ORDataObject::_dateToArray($date);
		if (is_array($ret)) {
			return sprintf('%s-%s-%s', $ret[0], $ret[1], $ret[2]);
		}
		/*
		if(preg_match('/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/',$date,$match)) {
			if ($match[3] < 1971) {
				$ret = $match[3] . "-" . $match[1] . "-" . $match[2];
				return $ret;
			}	
		}
		//date already in ISO format
		elseif(preg_match('/^(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})$/',$date,$match)) {
			return $date;
		}
		*/
		$ret = @date('Y-m-d',strtotime($date));
		if ($ret == false) {
			return '';
		}
		return $ret;
	}
	
	
	/**
	 * Transforms an ISO date into the traditional English.
	 *
	 * @param string An ISO formatted date
	 *
	 * @todo This and all the other date methods really need to be moved into
	 *   their own object to handle transforming dates.
	 */
	function _fromISODate($date) {
		if (empty($date)) {
			return '';
		}
		$ISODateArray = ORDataObject::_dateToArray($date);
		return sprintf("%s/%s/%s", $ISODateArray[1], $ISODateArray[2], $ISODateArray[0]);
	}
	
	
	/**
	 * Returns a date string in an array
	 *
	 * @return array|false
	 *    An array in ISO format ("YYYY", "DD", "MM") if it recognizes the format
	 *    (mm/dd/yyyy or yyyy/mm/dd), or FALSE if it can't understand it. 
	 */
	function _dateToArray($date) {
		if(preg_match('/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/',$date,$matches)) {
			return array($matches[3], $matches[1], $matches[2]);
		}
		elseif(preg_match('/^(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})$/',$date,$matches)) {
			array_shift($matches);
			return $matches;
		}
		
		return false;
	}

	/**
	 * Static method used to create a new instance of an ordataobject
	 *
	 * handles including the class files in the app with fallback to Celini default implmentations
	 * also handles directories like contacts/person
	 *
	 * All parameters after $name are passed to the ordo object's {@link setup()} method
	 *
	 * This method will begin being deprecated in favor of the more flexible
	 * {@link Celini::newORDO()} method.
	 *
	 * @param	string	$name
	 * @static
	 * @deprecated
	 * @see Celini::newORDO()
	 */
	function &factory($name) {
		$args = func_get_args();
		array_shift($args);
		$return =& Celini::newORDO($name, $args);
		return $return;
	}

	/**
	 * Static method used to include an ordo file for an object
	 *
	 * @todo This method needs to be phased out.
	 *
	 * @param	string	$name
	 * @static
	 */
	function factory_include($name) {
		if (!isset($GLOBALS['_ORDOFileLoader'])) {
			$GLOBALS['_ORDOFileLoader'] =& new ORDOFileLoader();
		}
		
		$GLOBALS['_ORDOFileLoader']->loadORDO($name);
	}

	/**
	 * Does a given variable exist on this object
	 *
	 * Storage variables will never return true
	 */
	function exists($key) {
		$getter = "get_$key";
		if (method_exists($this,$getter)) {
			return true;
		}
		elseif (isset($this->$key)) {
			return true;
		}
		elseif (isset($this->storage_metadata['int'][$key])) {
			return true;
		}
		elseif (isset($this->storage_metadata['date'][$key])) {
			return true;
		}
		elseif (isset($this->storage_metadata['string'][$key])) {
			return true;
		}
		elseif (isset($this->storage_metadata['text'][$key])) {
			return true;
		}
		return false;
	}

	/**
	 * Unified get method
	 *
	 * Automatically maps to storage variables with metadata
	 */
	function get($key) {
		$getter = "get_$key";
		if (method_exists($this,$getter)) {
			return $this->$getter();
		}
		else if (isset($this->$key)) {
			return $this->_filterProperty($key);
		}
		else if (isset($this->storage_metadata['int'][$key])) {
			return $this->_int_storage->get($key);
		}
		else if (isset($this->storage_metadata['date'][$key])) {
			return $this->_date_storage->get($key);
		}
		else if (isset($this->storage_metadata['string'][$key])) {
			return $this->_string_storage->get($key);
		}
		else if (isset($this->storage_metadata['text'][$key])) {
			return $this->_text_storage->get($key);
		}
		else {
			// If _unknownMessage is not a string, return it without modification
			if (!is_string($this->_unknownMessage)) {
				return $this->_unknownMessage;
			}
			
			return sprintf($this->_unknownMessage, $key);
		}
	}
	
	
	/**
	 * Unified get method for human-readable values.
	 *
	 * This works exactly like {@link get()}, except that it allows an ORDO to specify a value_*()
	 * accessor to handle formatting of raw data into something that's human readable.
	 *
	 * @see    get()
	 * @param  string
	 * @return mixed
	 */
	function value($key) {
		$accessor = 'value_' . $key;
		if (method_exists($this, $accessor)) {
			return $this->$accessor();
		}
		// do enum look-up if this is an enum and the method doesn't exist
		elseif (isset($this->_enumList[$key])) {
			return $this->_valueEnum($this->_enumList[$key], $this->get($key));
		}
		else {
			return $this->get($key);
		}
	}
	
	
	/**
	 * Unified get method for key/value "valueLists" for enum data and foreign keys
	 *
	 * This works exactly like the other unified methods in that you can specify "valueList_*()" 
	 * custom accessors for creating lists.
	 *
	 * @see    get(), value()
	 * @param  string|null
	 * @return array
	 */
	function valueList($key = null) {
		if (is_null($key)) {
			if (method_exists($this, 'genericList')) {
				return $this->genericList();
			}
		}
		else {
			$accessor = 'valueList_' . $key;
			if (method_exists($this, $accessor)) {
				return $this->$accessor();
			}
			elseif (isset($this->_enumList[$key])) {
				return $this->_listEnum($this->_enumList[$key]);
			}
			elseif (isset($this->_foreignKeyList[$key])) {
				$ordo =& Celini::newORDO($this->_foreignKeyList[$key]);
				return $ordo->genericList();
			}
			elseif (in_array($key,$this->dbHelper->listFields($this->_table))){
				$db = new clniDb();
				$pkey = 'id';
				if(!empty($this->_key)){
					$pkey = $this->_key;
				}
				$sql = "SELECT $pkey AS pkey, $key AS lkey FROM ".$this->tablename()." WHERE 1";
				$result = $db->execute($sql);
				$list = array();
				while($result && !$result->EOF) {
					$list [$result->fields['pkey']] = $result->fields['lkey'] ;
					$result->MoveNext();
				}
				return $list;
			}
		}
		return array();
	}
	
	
	/**
	 * This filters a property if necessary and returns the value.
	 *
	 * @return mixed
	 *
	 * @todo Expand this to encompass a more robust filtering/scrubbing system
	 */
	function _filterProperty($key) {
		if (is_object($this->$key) && is_a($this->$key,'clniValue')) {
			return $this->$key;
		}
		if (is_string($this->$key)) {
			if (get_magic_quotes_gpc()) {
				return stripslashes($this->$key);
			}
			else {
				return $this->$key;
			}
		}
		return $this->metadata->enforceType($key,$this->$key);
	}

	/**
	 * Unified set method
	 *
	 * Automatically maps to storage variables with metadata
	 *
	 * @todo value might get filtered by the setter and we wouldn't pick that up, it could give us false changed readings in the metadata, does this matter?
	 */
	function set($key,$value) {
		$value = $this->metadata->enforceType($key,$value);
	
		$setter = "set_$key";
		if (method_exists($this,$setter)) {
			$this->$setter($value);
		}
		elseif (isset($this->$key)) {
			$this->$key = $value;
		}
		elseif (isset($this->storage_metadata['int'][$key])) {
			$this->_int_storage->set($key,$value);
		}
		elseif (isset($this->storage_metadata['date'][$key])) {
			$this->_date_storage->set($key,$value);
		}
		elseif (isset($this->storage_metadata['string'][$key])) {
			$this->_string_storage->set($key,$value);
		}
		elseif (isset($this->storage_metadata['text'][$key])) {
			if (!is_a($this->_text_storage, 'storage')) {
				trigger_error('text storage not enabled in config and is required for ' . get_class($this));
				exit;
			}
			$this->_text_storage->set($key,$value);
		}
		else {
			return false;
		}
		$df = $this->_dbFormat;
		$this->_dbFormat = true;
		$this->metadata->updateChanged($key,$this->get($key));
		$this->_dbFormat = $df;
		return true;
	}

	/**
	 * Get checked or "" from a boolean
	 */
	function getChecked($key) {
		if ($this->get($key)) {
			return " CHECKED";
		}
		return "";
	}

	/**
	 * Delete the record
	 */
	function drop() {
		if ($this->get('id') > 0) {
			if ($this->auditChanges) {
				// this is pretty much a hack, will need to extend the metaData object for this to work correctly in php5 at least
				$meta = $this->metadata;
			}
			$this->removeRelationship();
			$pkeys = $this->dbHelper->PrimaryKeys($this->tableName());
			$this->dbHelper->execute("delete from ".$this->tableName()." where ".$this->dbHelper->genSqlPrimaryKeyWhere($this));
			if ($this->auditChanges) {
				$this->helper->audit($this,$meta,$this->auditFieldChanges,true);
			}

		}
	}

	function toString() {
		$ret = get_class($this)."(".$this->get('id').") {\n";;
		$list = $this->metadata->listFields();
		foreach($list as $field) {
			$value = $this->get($field);

			$ret .= "  $field => $value\n";
		}
		$ret .= "}\n";
		return $ret;
	}
	
	
	/**
	 * Returns a boolean depending on whether or not this has been populated
	 *
	 * @return bool
	 */
	function isPopulated() {
		return $this->_populated;
	}
	
	
	/**
	 * Returns a properly formatted English date from an internal $key.
	 *
	 * @param	string		The key of the date that we're retrieving. 
	 * @return	string
	 * @access	protected
	 */
	function _getDate($key) {
		// Return empty if it's not set
		if (empty($this->$key)) {
			return '';
		}
		
		// This makes the assumption that the date is stored internally in the 
		// correct form.
		$date =& $this->$key->getDate();
		if ($this->_inPersist || $this->_dbFormat) {
			return $this->$key->toISO();
		}
		else {
			return $date->toString();
 		}
	}

	/**
	 * Returns a properly formatted English time from an internal $key.
	 *
	 * @param	string		The key of the date that we're retrieving. 
	 * @return	string
	 * @access	protected
	 */
	function _getTime($key) {
		// Return empty if it's not set
		if (empty($this->$key)) {
			return '';
		}
		
		// This makes the assumption that the date is stored internally in the 
		// correct form.
		$time =& $this->$key->getTime();
		if ($this->_inPersist || $this->_dbFormat) {
			return $time->toString();
		}
		else {
			return $time->toString();
 		}
	}
	
	/**
	 * Insures that the date is stored internally in ISO format.
	 *
	 * @param	string		The key of the value date we're setting
	 * @param	string		The actual date/timestamp that is being set
	 * @access	protected
	 */
	function _setDate($key, $string) {
		$timestamp =& TimestampObject::create($string);
		$this->$key = $timestamp;
	}
	
	
	/**
	 * Insures that a timestamp is stored internally in ISO format.
	 *
	 * @param	string		The key of the value we're setting
	 * @param	string		The actual timestamp that's being used
	 * @access	protected
	 */
	function _getTimestamp($key) {
		if (empty($this->$key)) {
			return '';
		}
		if (($this->_inPersist || $this->_dbFormat) && is_object($this->$key)) {
			return $this->$key->toString('%Y-%m-%d %H:%i:%s');
		}
		elseif(is_object($this->$key)) {
			return $this->$key->toString();
		}
		else {
			return $this->$key;
		}
	}
	
	
	/**
	 * Returns an enum value for a given property.
	 *
	 * @param  string
	 * @param  int|string
	 * @return mixed
	 */
	function _valueEnum($enumName, $key) {
		$em =& Celini::enumManagerInstance();
		return $em->lookup($enumName, $key);
	}

	/**
	 * Returns an enum array
	 *
	 * @param  string
	 * @return mixed
	 */
	function _listEnum($enumName) {
		$em =& Celini::enumManagerInstance();
		return $em->enumArray($enumName);
	}
	
	/**
	 * Loads a datasource object.
	 *
	 * If $key does not exist as a property of this ordo, then it will be 
	 * passed directly to the new data source.
	 *
	 * @param	string	The name of the datasource object to load
	 * @param	mixed	The name of the key to load 
	 */
	function &loadDatasource($name, $key = 'id') {
		$myClassName = isset($this->_internalName) ? $this->_internalName : ucwords(get_class($this));
		$realName = sprintf('%s_%s_DS', $myClassName, $name);
		if (!class_exists($realName)) {
			$loader =& new DatasourceFileLoader();
			$parents = array($myClassName);
			if (isset($this->_merge)) {
				$parents = array_merge($parents, $this->_merge);
			}
			$foundName = $loader->loadFromUnknownParents($name, $parents);
			if ($foundName === false) { 
				trigger_error("Unknown Datasource $name",E_USER_ERROR);
			}
			else {
				$realName = $foundName;
			}
		}
		
		$param = $this->exists($key) ? $this->get($key) : $key;
		$return =& new $realName($param);
		return $return;
	}

	/**
	 * Get an ordo helper object
	 */
	function &getHelper() {
		return $this->helper;
	}

	/**
	 * Populate an ordo for an array of values
	 */
	function fromArray($array) {
		$this->helper->populateFromArray($this,$array);
	}

	/**
	 * Create an array from the data in the ordo
	 */
	function toArray() {
		return $this->helper->persistToArray($this);
	}

	
	/**
	 * Returns the ID of this ORDO
	 *
	 * @return string
	 * @access protected
	 */
	function get_id() {
		if ($this->_key === false && isset($this->id) || $this->_key == 'id') {
			return $this->id;
		}
		return $this->get($this->_key);
	}

	
	/**
	 * Sets the ID of this ordo
	 *
	 * @param  string|int
	 * @access protected
	 */
	function set_id($id) {
		if ($this->_key === false || $this->_key == 'id') {
			$this->id = $id;
		}
		else {
			$this->set($this->_key,$id);
		}
	}
	
	/**
	 * Easier way to use setRelationship so you don't have to remember which way 
	 * $relationshipType goes.  This sets the ordo fed to the function as a child
	 * of $this.
	 *
	 * @param ORDataObject $other
	 */
	function setChild($other){
		return $this->setRelationship($other,'child');
	}
	
	/**
	 * Easier way to use setRelationship so you don't have to remember which way 
	 * $relationshipType goes.  This sets the ordo fed to the function as a parent
	 * of $this.
	 *
	 * @param ORDataObject $other
	 */
	function setParent($other){
		return $this->setRelationship($other,'parent');
	}

	/**
	 * Checks to see if a relationship exists.  If not, it is created.
	 * You should use setParent or setChild methods to make coding easier.
	 *
	 * @param ORDataObject|null $other
	 * @param string $relationType (parent|child) child if other should be a child of $this
	 * @param string Optional, used when not setting $other
	 * @param int	 Optional, used when not setting $other
	 * @return 
	 */
	function setRelationship(&$other, $relationshipType = 'child'){
		if($this->get('id') < 1 || $other->get('id') < 1)
			return false;
		switch ($relationshipType) {
			case 'child':
				$child =& $this->getChild($other->name(), $other->get('id'));
				if ($child->isPopulated()) {
					// already exists
					return true;
				}
				
				$relationship=&Celini::newORDO('Relationship');
				$relationship->set('parent_type',$this->name());
				$relationship->set('parent_id',$this->get('id'));
				$relationship->set('child_type',$other->name());
				$relationship->set('child_id',$other->get('id'));
				$relationship->persist();
				break;
			
			case 'parent' :
				$parent=&$this->getParent($other->name(), $other->get('id'));
				if ($parent->isPopulated()) {
					// already exists
					return true;
				}
				
				$relationship=&Celini::newORDO('Relationship');
				$relationship->set('child_type',$this->name());
				$relationship->set('child_id',$this->get('id'));
				$relationship->set('parent_type',$other->name());
				$relationship->set('parent_id',$other->get('id'));
				$relationship->persist();
				break;
		}
		return true;
	}
	
	
	/**
	 * Deletes a relationship if it exists.
	 *
	 * @param string|null
	 * @param int|null
	 * @todo Should this be deleted, or mark a column deleted as 1?
	 */
	function removeRelationship($ordoName=null,$id=null){
		$db =& new clniDB();
		$parent='';
		$child='';
		
		if(!is_null($ordoName)){
			$parent .= 'parent_type=' . $db->quote($ordoName) . ' AND ';
			$child .= 'child_type=' . $db->quote($ordoName) . ' AND ';
		}
		if(!is_null($id)){
			$parent .= 'parent_id=' . $db->quote($id) . ' AND ';
			$child .= 'child_id=' . $db->quote($id) . ' AND ';
		}
		
		$whereSql = '(' . $parent . ' child_type=' . $db->quote($this->name()) . ' AND child_id= ' . $db->quote($this->get('id')) . ')
		 	OR (' . $child . ' parent_type=' . $db->quote($this->name()) . ' AND parent_id=' . $db->quote($this->get('id')) . ')';
		
		$sql = 'DELETE FROM relationship WHERE ' . $whereSql;
		$db->execute($sql);
	}
	
	
	/**#@+
	 * @todo Add caching to this
	 */
	
	/**
	 * Returns a child object of this ORDO by a given ORDO <i>$childType</i> and optionally 
	 * <i>$childId</i>.
	 *
	 * If $childId is null, the first child of the given type is returned.
	 *
	 * If no child ORDO is found, this will return a blank ordo of the type <i>$childType</i>.
	 *
	 * This allows for custom child getters via getChild_childType().
	 *
	 * @param  string        Name of the child ORDO
	 * @param  int           Id of the specific child to return (optional)
	 * @return ORDataObject
	 * @see    getChildren(), getChildrenFinder()
	 */
	function &getChild($childType, $childId = null) {
		$accessor = 'getChild_' . $childType;
		if (method_exists($this, $accessor)) {
			$ordo =& $this->$accessor();
		}
		else {
			$childOrdo =& Celini::newORDO($childType);
			$childOrdo->set('id', $childId);
			
			$finder =& $this->getChildrenFinder($childOrdo);
			$collection =& $finder->find();
			if ($collection->count() <= 0) {
				$ordo =& Celini::newORDO($childType);
			}
			else {
				$ordo =& $collection->current();
			}
		}
		return $ordo;
	}
	
	
	/**
	 * Returns a parent object of this ORDO by a given ORDO <i>$parentType</i> and optionally
	 * <i>$parentId</i>.
	 *
	 * If <i>$parentId</i> is null, the first parent of the given type is returned.
	 *
	 * If no parent ORDO is found, this will return a blank ordo of the type <i>$parentType</i>.
	 *
	 * This allows for custom child getters via getParent_parentType().
	 *
	 * @param  string        Name of the parent ORDO
	 * @param  int           Id of the specific parent to return (optional)
	 * @return ORDataObject
	 * @see    getParents(), getParentFinder()
	 */
	function &getParent($parentType, $parentId = null) {
		$accessor = 'getParent_' . $parentType;
		if (method_exists($this, $accessor)) {
			$ordo =& $this->$accessor();
		}
		else {
			$parentOrdo =& Celini::newORDO($parentType);
			$parentOrdo->set('id', $parentId);
			
			$finder =& $this->getParentsFinder($parentOrdo);
			$collection =& $finder->find();
			if ($collection->count() <= 0) {
				$ordo =& Celini::newORDO($parentType);
			}
			else {
				$ordo =& $collection->current();
			}
		}
		return $ordo;
	}
	
	
	/**
	 * Returns all of the children for this ORDO that match the provided <i>$childType</i>.
	 *
	 * This allows for custom children getters via getChildren_childType().
	 *
	 * @param  string          Name of the children ORDOs
	 * @return ORDOCollection
	 * @see    getChild(), getChildrenFinder()
	 */
	function &getChildren($childType) {
		$accessor = 'getChildren_' . $childType;
		if (method_exists($this, $accessor)) {
			$collection =& $this->$accessor();
		}
		else {
			$finder =& $this->getChildrenFinder(Celini::newORDO($childType));
			$collection =& $finder->find();
		}
		return $collection;
	}
	
	/**
	 * Returns all of the children IDs for this ORDO that match the provided <i>$childType</i>.
	 *
	 * This allows for custom children getters via getChildrenIds_childType().
	 *
	 * @param  string          Name of the children ORDOs
	 * @return array
	 * @see    getChild(), getChildrenFinder()
	 */
	function getChildrenIds($childType) {
		$accessor = 'getChildrenIds_' . $childType;
		if (method_exists($this, $accessor)) {
			$collection = $this->$accessor();
		}
		else {
			$finder =& $this->getChildrenFinder(Celini::newORDO($childType));
			$collection = $finder->findIds();
		}
		return $collection;
	}

	/**
	 * Returns all of the parents for this ORDO that match the provided <i>$parentType</i>.
	 *
	 * This allows for custom parent getters via getParents_parentType()
	 *
	 * @param  string          Name of the parent ORDOs
	 * @return ORDOCollection
	 * @see    getParent(), getParentFinder()
	 */
	function &getParents($parentType) {
		$accessor = 'getParents_' . $parentType;
		if (method_exists($this, $accessor)) {
			$collection =& $this->$accessor();
		}
		else {
			$finder =& $this->getParentsFinder(Celini::newORDO($parentType));
			$collection =& $finder->find();
		}
		return $collection;
	}
	
	/**
	 * Returns all of the parent IDs for this ORDO that match the provided <i>$parentType</i>.
	 *
	 * This allows for custom children getters via getChildrenIds_childType().
	 *
	 * @param  string          Name of the parent ORDOs
	 * @return array
	 * @see    getParents(), getParentFinder()
	 */
	function getParentIds($parentType) {
		$accessor = 'getParentIds_' . $parentType;
		if (method_exists($this, $accessor)) {
			$collection = $this->$accessor();
		}
		else {
			$finder =& $this->getParentsFinder(Celini::newORDO($parentType));
			$collection = $finder->findIds();
		}
		return $collection;
	}

	/**#@-*/
	
	
	/**
	 * Returns a {@link RelationshipFinder} object for custom searching
	 *
	 * This automatically sets the relatedType to this ORDO.
	 *
	 * If the type of relationship is know, use {@link getChildren()} or {@link getParents()} 
	 * directly so they can handle the setup for you.
	 *
	 * @return RelationshipFinder
	 * @see    getChildrenFinder(), getParentsFinder()
	 */
	function &relationshipFinder() {
		$finder =& new RelationshipFinder();
		$finder->setRelatedType($this);
		return $finder;
	}
	
	
	/**
	 * Returns a {@link RelationshipFinder} setup to find a given type of child.
	 *
	 * For simple cases, use {@link getChildren()} or {@link getChild()} directly.
	 *
	 * @param  string
	 * @return RelationshipFinder
	 * @see    getChild(), getChildren(), relationshipFinder()
	 */
	function &getChildrenFinder(&$childOrdo) {
		$finder =& $this->relationshipFinder();
		$finder->setParent($this);
		$finder->setRelatedType($childOrdo);
		return $finder;		
	}
	
	
	/**
	 * Returns a {@link RelationshipFinder} setup to find a given type of parent.
	 *
	 * For simple cases, use {@link getParents()} or {@link getParent()} directly.
	 *
	 * @param  string
	 * @return RelationshipFinder
	 * @see    getParent(), getParents(), relationshipFinder()
	 */
	function &getParentsFinder(&$parentOrdo) {
		$finder =& $this->relationshipFinder();
		$finder->setChild($this);
		$finder->setRelatedType($parentOrdo);
		return $finder;
	}
	
	
	/**
	 * Handles finding a collection of this ORDO via a generic means.
	 *
	 * The method looks for custom Finder objects located in includes/finder/ORDOName and
	 * includes/finder in that order for a file name "<i>$type</i>.class.php".  Those objects should
	 * have a constructor that takes two parameters: a reference to the calling ordo, and an array
	 * of parameters.
	 *
	 * This method can also utilize find_*() that work exactly like the {@link set()}, 
	 * {@link value()}, etc. methods.
	 *
	 * @param  string
	 * @param  array
	 * @return RelationshipCollection
	 */
	function find($type, $parameters) {
		settype($parameters, 'array');
		
		// Look for custom find_*() case
		$accessor = 'find_' . $type;
		if (method_exists($this, $accessor)) {
			return $this->$accessor($parameters);
		}
		
		// Look for custom Finder object case		
		$finders[$this->name()] = &new FileFinder();
		$finders[$this->name()]->initCeliniPaths('/includes/finders/' . $this->name());
		$finders['generic'] = &new FileFinder();
		$finders['generic']->initCeliniPaths('/includes/finders');
		
		for ($i = 0; $i < count($finders); $i++) {
			$fileName = $finders[$i]->find($type . '.class.php');
			if ($fileName !== false) {
				$using = $i;
				break;
			}
		}
		
		require_once $fileName;
		$finderClassName = ($using != 'generic') ? $using . '_' . $type : $type;
		$finderObj =& new $finderClassName($this, $parameters);
		return $finderObj;
	}
	
	
	/**
	 * Returns true if this ordo, or any of its children has been updated
	 *
	 * @return boolean
	 * @todo implement checking of children
	 */
	function isModified() {
		if ($this->metadata->isModified()) {
			return true;
		}
		
		// iterate through children
		
		return false;
	}

	/**
	 * Set an audit message
	 */
	function setAuditMessage($message) {
		$this->_auditMessage = $message;
	}

	/**
	 * Get the audit message
	 */
	function getAuditMessage() {
		return $this->_auditMessage;
	}

public static function toXml($data, $rootNodeName = 'data', $xml=null)	{
	if ($xml === null) {
		$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
	}
	// loop through the data passed in.

	foreach($data as $key => $value) {
		if (is_object($data) && method_exists($data, "get_".$key)) {
			//$value = call_user_method("get",$data,$key);
			$value = $data->get($key);
		}
	// no numeric keys in our xml please!
	if (is_numeric($key))	{
	// make string key...
		//$key = "unknownNode_". (string) $key;
		$key = "array";
	}
	// replace anything not alpha numeric
	$key = preg_replace('/[^a-z_0-9]/i', '', $key);

	// if there is another array found recrusively call this function
	if (strpos($key,'_') === 0 
		|| strpos($key,'audit') === 0 
		|| strpos($key,'inPersist') === 0 
		|| strpos($key,'table') === 0 
		|| $key == "meta" 
		|| $key == "metaHints" 
		|| $key == "db" 
		|| $key == "valuePlaceholders" 
		|| $key == "dbHelper" 
		|| $key == "helper" 
		|| $key == "enumTable" 
		|| $key == "celini" 
		|| $key == "metadata") {

	}
	elseif ($key == "storage_metadata") {
		// recrusive call.
		foreach ($value as $storarr => $stortype) {
			foreach ($stortype as $storname => $storval) {
				//$value = call_user_method("get",$data,$storname);
				$value = $data->get($storname);
				$xml->addChild($storname,$value);
			}
		}
	}
	elseif (strtolower($key) == "patientpicture") {
		$d = Document::FirstDocumentByCategoryName((int)$value,"Picture");
		if (!is_object($d)) continue;
		$node = $xml->addChild($key,base64_encode(file_get_contents(Celini::config_get('document_manager:repository') . $value."/". $d->get('name'))));
		//$node = $xml->addChild($key, base64_encode(file_get_contents('/tmp/homer.gif')));
		//$node->addAttribute("xmlns:xfa","http://www.xfa.org/schema/xfa-data/1.0/");
		//$node->addAttribute("contentType","image/jpg");
		//$node->addAttribute("href",htmlentities($value));
	}
	elseif (strtolower($key) == "estimatedencounterfee") {
		$total = 0;
		$fees = array();
		if (!(int)$value > 0) continue;
		$GLOBALS['loader']->requireOnce('controllers/C_Coding.class.php');
		$ccd = new C_Coding();
		$ccd->_CalculateEncounterFees((int)$value,true); //second argument is to show descriptions and codes instead of just codes
		if (isset($ccd->_feeDiscountDS) && is_object($ccd->_feeDiscountDS)) {
			$fees = $ccd->_feeDiscountDS->toArray();
			$total = $fees[count($fees)-1]['fee'];
		}
		elseif (isset($ccd->_feeDS) && is_object($ccd->_feeDS)) {
			$fees = $ccd->_feeDS->toArray();
			$total = $fees[count($fees)-1]['fee'];
		}
		
		$node = $xml->addChild($key,$total);
		foreach ($fees as $feeRow) {
			if (isset($feeRow['code']) && $feeRow['code'] == "<b>Total</b>") continue;
			$node = $xml->addChild('feeRow');
			$node->addChild('description',$feeRow['description']);
			$node->addChild('fee',$feeRow['fee']);
		}
	}
	else {
	if (is_array($value) || is_object($value)) {
		$node = $xml->addChild($key);
		// recrusive call.
		ORDataObject::toXml($value, $rootNodeName, $node);
	}
	else {
		// add single node.
		if (is_resource($value)) {
			$value = "resource";
		}
		$value = htmlentities($value);
		$xml->addChild($key,$value);
	}
	}
	}
	// pass back as string. or simple xml object if you want!
	$xmlstr = $xml->asXML();
	/*$xmlstr .= <<<EOF
<logo xmlns:xfa="http://www.xfa.org/schema/xfa-data/1.0/">Qk1uAQAAAAAAAD4AAAAoAAAAJgAAACYAAAABAAEAAAAAADABAADYDgAA2A4AAAIAAAAAAAAAAAAAAP///wD//////AAAAP/////8AAAA//////wAAAD//////AAAAP/////8AAAA//////wAAAD8AAAA/AAAAP38AH78AAAA/fAAHvwAAAD9wAAG/AAAAP2AAAb8AAAA/QAAAvwAAAD9AAAC/AAAAPwAAAD8AAAA/AAAAPwAAAD8AAAA/AAAAPwAAAD8AAAA/AAAAPwAAAD8AAAA/AAAAPwAAAD8AAAA/AAAAPwAAAD8AAAA/AAAAPwAAAD8AAAA/AAAAPwAAAD9AAAC/AAAAP0AAAL8AAAA/YAABvwAAAD9wAAG/AAAAP3gAA78AAAA/fAAHvwAAAD9/AB+/AAAAPwAAAD8AAAA//////wAAAD//////AAAAP/////8AAAA//////wAAAD//////AAAAP/////8AAAA</logo>
EOF;*/

	return preg_replace('/<\?.*\?>/','',$xmlstr);

}
} // end of ORDataObject
?>
