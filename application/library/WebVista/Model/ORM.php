<?php
/*****************************************************************************
*       ORM.php
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


class WebVista_Model_ORM implements ORM,Iterator {

	const REPLACE = 1;
	const INSERT = 2;
	const UPDATE = 3;
	const DELETE = 4;
	protected $_inPersist = false;
	protected $_persistMode = WebVista_Model_ORM::REPLACE;
	protected $_primaryKeys = array();
	protected $_table;
	protected $_cascadePersist = true;
	protected $_shouldAudit = true;
	protected $_legacyORMNaming = false;
	protected $_cascadePopulate = true;
	public static $_nsdrNamespace = false;

	function __construct() {
		if (count($this->_primaryKeys) == 0) {
			$this->_primaryKeys[] =  lcfirst(get_class($this)) . "Id";
		}
		if (empty($this->_table)) {
			$this->_table = lcfirst(get_class($this));
		}
	}
	function __set($key, $value) {
		if (method_exists($this,"set" . ucfirst($key))) {
			$method = 'set' . ucfirst($key);
			$this->$method($value);
			return $this;
		}
		elseif ($this->_legacyORMNaming == true && strpos($key,'_') === false) {
			$newKey = strtolower(preg_replace('/([A-Z]{1})/','_\1',$key));
			if (strpos($newKey,'_') !== false && in_array($newKey,$this->ORMFields())) {
				$this->$newKey = $value;
				return $this;
			}
		}
		$this->$key = $value;
		return $this;
	}	
	function __get($key) {
		if (method_exists($this,"get" . ucfirst($key))) {
			$method = 'get' . ucfirst($key);
			return $this->$method();
		}
		elseif ($this->_legacyORMNaming == true && strpos($key,'_') === false) {
			$newKey = strtolower(preg_replace('/([A-Z]{1})/','_\1',$key));
			if (strpos($newKey,'_') !== false && in_array($newKey,$this->ORMFields())) {
				return $this->$newKey;
			}
		}
		$ret = null;
		if (isset($this->$key)) {
			$ret = $this->$key;
		}
		return $ret;
	}

	public function __isset($key) {
		return $this->__get($key);
	}

	public function populateWithArray($array) {
		//$fields = $this->ormFields();
		foreach($array as $key => $value) {
			//echo "key: $key -- $value<br/>";
			if ($this->$key instanceof ORM) {
				$this->$key->populateWithArray($value);
			}
			else {
				$this->__set($key,$value);
			}
		}
		$this->postPopulate();
	}

	public function populate() {
		$sql = "SELECT * from " . $this->_table . " WHERE 1 ";
		$doPopulate = false;
		foreach($this->_primaryKeys as $key) {
			if ($this->$key > 0 || strlen($this->$key) > 0) {
				$doPopulate = true;
				$sql .= " and $key = '" . preg_replace('/[^0-9a-z_A-Z-\.]/','',$this->$key) . "'";
			}
		}
		if ($doPopulate == false) return false;
		$retval = false;
		$retval = $this->populateWithSql($sql);
		$this->postPopulate();
		return $retval;
	}

	public function postPopulate() {
		if (!$this->_cascadePopulate) {
			return true;
		}
		$fields = $this->ORMFields();
		foreach ($fields as $field) { 
			$obj = $this->__get($field);
			if ($obj instanceof ORM) {
				foreach($obj->_primaryKeys as $key) {
					if (in_array($key,$this->ORMFields())) {
						$obj->$key = $this->$key;
					}
					else {
						// check if there's an underscore
						if (strpos($key,'_') !== false) { // table_id
							$newKey = str_replace(' ','',ucwords(str_replace('_',' ',$key)));
							// lower case the first character
							$newKey[0] = strtolower($newKey[0]);
						}
						else {
							$newKey = preg_replace('/([A-Z]{1})/','_\1',$key);
						}
						if (in_array($newKey,$this->ORMFields())) {
							$obj->$newKey = $this->$newKey;
						}
					}
				}
				$obj->populate();
			}
		}

	}

	public function populateWithSql($sql) {
		$db = Zend_Registry::get('dbAdapter');
		$stmt = $db->query($sql);
		$fields = $this->ORMFields();
		$retval = false;
		while($row = $stmt->fetch()) {
			$retval = true;
			foreach($row as $col => $val) {
				if (in_array($col,$fields)) {
					unset($fields[$col]);
					//echo "set: $col val: $val <br />";
					$this->__set($col,$val);
				}
			}	
		}
		$stmt->closeCursor();

		//var_dump($this);exit;
		return $retval;	
	}
	
	/**
	 * Persist an ordo to the database
	 *
	 * @param ORDataObject	$ordo
	 */
	function persist() {
		$db = Zend_Registry::get('dbAdapter');
		$this->_inPersist = true;

		$sql = $this->toSQL();

		$this->_inPersist = false;
		//echo $sql . "<br />";ob_flush();
		$stmt = $db->query($sql);
		$stmt->closeCursor();
		$this->postPersist();
		if ($this instanceof Document && $this->signatureNeeded()) {
			ESignature::createSignatureEntry($this);
		}
		if ($this->shouldAudit() && get_class($this) != "Audit" && get_class($this) != "AuditValue") {
			WebVista_Model_ORM::audit($this);
		}
		return $this;
	}
	function setPersistMode($mode) {
		$this->_persistMode = $mode;
	}

	/** 
	 * Store changes to the audit log
	 */
	function audit($obj) {
		$audit = new Audit();
		$audit->objectClass = get_class($obj);
		$classObjectIdKey = lcfirst($audit->objectClass);
		$objectIdKey = $classObjectIdKey . "Id";
		$objectLegacyIdKey = strtolower(preg_replace('/([A-Z]{1})/','_\1',$classObjectIdKey)) . "_id";
		if (!isset($obj->$objectIdKey) && !isset($obj->$objectLegacyIdKey)) {
			//trigger_error("objIdKey not found: $objectIdKey for " . get_class($obj),E_USER_NOTICE);
			return false;
		}
		$audit->auditId = $this->nextSequenceId('auditSequences');
		$audit->objectId = $obj->$objectIdKey;
		$audit->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$audit->type = $obj->_persistMode;
		$audit->dateTime = date('Y-m-d H:i:s');
		if ($obj instanceof ORM) {
			foreach ($obj->ORMFields() as $field) {
				$auditValue = new AuditValue();
				$auditValue->auditId = $audit->auditId;
				$auditValue->key = $field;
				if ($field == "person_id"  || $field == "personId" || $field == "patientId" || $field == "patient_id") {
					$audit->patientId = (string)$obj->$field;
				}
				if (is_object($obj->$field)) {
					$auditValue->value = get_class($obj->$field);
				}
				else {
					$auditValue->value = (string)$obj->$field;
				}
				$auditValue->persist();
			}
		}
		$audit->persist();
	}

	public static function nextSequenceId($seqTable = "") {
		$db = Zend_Registry::get('dbAdapter');
		$id = null;
		if (empty($seqTable)) {
			$seqTable = Zend_Registry::get('config')->orm->sequence->table;
		}
		$db->beginTransaction();
		try {
			$db->query("UPDATE " . $seqTable . " set id = id+1");
			$id = $db->fetchOne("select id from " . $seqTable);
			$db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
			echo $e->getMessage();
		}
		return $id;
	}

	public function ormFields() {
		$fields =  WebVista::getORMFields(get_class($this));
		return $fields;
        }
	public function toString() {
		return print_r($this->toArray(),true);
	}
	public function toArray() {
		$fields = $this->ormFields();
		$array = array();
		foreach($fields as $value) {
			if ($this->$value instanceof ORM) {
				$array[$value] = $this->$value->toArray();
			}
			else {
				$array[$value] = $this->$value;
			}
		}
		return $array;
	}

	public function toDocument() {
                $fields = $this->ormFields();
                $array = array();
		$docString = "";
                foreach($fields as $field) {
                        if ($this->__get($field) instanceof Document) {
                                $docString .= $this->__get($field)->toDocument();
                        }
			elseif(substr($field,-2) == "Id") {
				//ignore
			}
			elseif (is_object($this->__get($field))) {
				//ignore
			}
                        else {
				if (strlen($this->__get($field)) > 255) {
					$docString .= $field . ":\n" . $this->__get($field) . "\n";
				}
				else {
					$docString .= $field . ": " . $this->__get($field) . "\n";
				}
                        }
                }
		return $docString;
        }

	public function toSQL() {
		$db = Zend_Registry::get('dbAdapter');
                $fields = $this->ormFields();
		//var_dump($fields);
		$sql = "";

		if ($this->_persistMode == WebVista_Model_ORM::REPLACE) {
			$sql = "REPLACE INTO `" . $this->_table . "` SET ";
		}
		elseif ($this->_persistMode == WebVista_Model_ORM::INSERT) {
			$sql = "INSERT INTO `" . $this->_table . "` SET "; 
		}
		elseif ($this->_persistMode == WebVista_Model_ORM::DELETE) {
			$sql = "DELETE FROM `" . $this->_table . "` "; 
		}
		$pWhere = "WHERE 1 ";
		for ($i=0,$fieldsCount=count($fields);$i<$fieldsCount;$i++) {
			$field = $fields[$i];
			//echo "setting: " . get_class($this)  ." " .  $field ."<br />";
			$val = $this->__get($field);
			if (is_object($val)) {
				if ($val instanceof ORM && $this->_cascadePersist == true) {
					$val->setPersistMode($this->_persistMode);
					$val->persist();
				}
				continue;
			}
			elseif (is_array($val)) {
				foreach($val as $item) {
					if ($item instanceof ORM) {
						$item->persist();
					}
				}
				continue;
			}

			if ($this->_persistMode == WebVista_Model_ORM::DELETE) {
				if (in_array($field,$this->_primaryKeys) && ($val > 0 || (!is_numeric($val) && strlen($val) > 0))) {
					$pWhere .= " and `$field` = '" . preg_replace('/[^0-9_a-z-\.]/i','',$val) . "' ";
				}
				// code below is just for replace/insert
				continue;
			}

			if (in_array($field,$this->_primaryKeys) && !$val > 0) {
				$pWhere .= " and `$field` = '" . preg_replace('/[^0-9_a-z-\.]/i','',$val) . "' ";
				$seqTable = "";
				if (get_class($this) == "Audit" || get_class($this) == "AuditValue") {
					$seqTable = Zend_Registry::get('config')->audit->sequence->table;
				}
				if (get_class($this) == "Audit" || get_class($this) == "AuditValue" || $this->_persistMode != WebVista_Model_ORM::DELETE) {
					$lastId = WebVista_Model_ORM::nextSequenceId($seqTable);
					//echo $lastId . "<br />";
					//ob_flush();
					$this->__set($field,$lastId);
					$val = $lastId;
				}

				/*if ($ordo->_createOwnership) {
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
				}*/
			}
			
			if (substr($field,0,1) != "_" ) {
				//echo "field: " . $field . "<br/>";
				$sql .= " `$field` = " . $db->quote($val) .",";
			}
		}

		if (strrpos($sql,",") == (strlen($sql) -1)) {
			$sql = substr($sql,0,(strlen($sql) -1));
		}
		
		if ($this->_persistMode == "update" || $this->_persistMode == WebVista_Model_ORM::DELETE) {
			$sql .= " $pWhere ";
		}
		return $sql;
	}

	function clearPrimaryKeys() {
		$this->_primaryKeys = array();
	}

	public function getIterator($dbSelect = null) {
		$iter = null;
		$class = get_class($this) . "Iterator";
		if (Zend_Loader::isReadable($class.".php")) {
			$iter = new $class();
		}
		else {
			$iter = new WebVista_Model_ORMIterator(get_class($this), $dbSelect);
		}
		return $iter;
	}

	public function postPersist() {

	}

	public function toXml() {
		return WebVista_Model_ORM::recurseXML($this);
	}

	public static function recurseXml($data, $rootNodeName = 'data', $xml=null)	{
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
		if (strpos($key,'_') === 0) { 
		}
		else {
			if (is_array($value) || is_object($value)) {
				$node = $xml->addChild($key);
				// recrusive call.
				WebVista_Model_ORM::recurseXml($value, $rootNodeName, $node);
			}
			else {
				// add single node.
				if (is_resource($value)) {
					$value = "resource";
				}
				$value = htmlentities(iconv("UTF-8","ASCII//TRANSLIT",$value));
				$xml->addChild($key,$value);
			}
		}
		}
		// pass back as string. or simple xml object if you want!
		$xmlstr = $xml->asXML();

		return preg_replace('/<\?.*\?>/','',$xmlstr);

	}
	function shouldAudit() {
		return $this->_shouldAudit;
	}
	function getLegacyORMNaming() {
		return $this->_legacyORMNaming;
	}


	public function rewind() {
		if ($this->_tracking == null) $this->_tracking = $this->getObjectProperties();
		reset($this->_tracking);
	}

	public function current() {
		$key = current($this->_tracking);
		$var = $this->__get($key);
		return $var;
	}

	public function key() {
		$var = current($this->_tracking);
		return $var;
	}

	public function next() {
		$var = next($this->_tracking);
		return $var;
	}

	public function valid() {
		$var = current($this->_tracking) !== false;
		return $var;
	}
	private function getObjectProperties() {
		$obj = new ReflectionObject($this);
                $properties = $obj->getProperties();
                $fields = array();
                foreach ($properties as $property) {
                        if (substr($property->name,0,1) == "_") continue;
                        $fields[] = $property->name;
                }
		return $fields;
	}

/*
	public function getControllerName() {
		$className = get_class($this);
		$controllerName = $className.'Controller';
		return $controllerName;
	}
*/
	public function signatureNeeded() {
		return true;
	}
}

Interface ORM {
	public function persist();
	public function setPersistMode($mode);
	public function populate();
	public function ormFields();
	public function postPersist();
}

Interface Document {
        public function getContent();
        public function getSummary();
	public function getDocumentId();
	public function setSigned($eSignatureId);
	static public function getPrettyName();
}

interface NSDRMethods {
	public function nsdrPersist($tthis,$context,$data);
	public function nsdrPopulate($tthis,$context,$data);
	public function nsdrMostRecent($tthis,$context,$data);
}

if (!function_exists('lcfirst')) {
	function lcfirst($str) {
		$str[0] = strtolower($str[0]);
		return $str;
	}
}
