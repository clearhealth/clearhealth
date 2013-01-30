<?php
/*****************************************************************************
*       NSDRDefinition.php
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


/**
 * WebVista_Model_ORM
 */
require_once 'WebVista/Model/ORM.php';

/**
 * Zend_Registry
 */
require_once 'Zend/Registry.php';

class NSDRDefinition extends WebVista_Model_ORM {

	protected $uuid;
	protected $namespace;
	protected $aliasFor;
	protected $ORMClass;
	protected $_table = "nsdrDefinitions";
	protected $_primaryKeys = array("uuid");

	// overrides parent persist
	public function persist() {
		$ret = parent::persist();
		if ($this->_persistMode !== WebVista_Model_ORM::DELETE) {
			return $ret;
		}
		$method = new NSDRDefinitionMethod();
		$methodIterator = $method->getIteratorByParentId($this->uuid);
		$methods = $methodIterator->toArray('uuid','uuid');
		foreach ($methods as $key=>$val) {
			$item = clone $method;
			$item->uuid = $key;
			$item->setPersistMode($this->_persistMode);
			$item->persist();
		}
		return $ret;
	}

	// overrides parent populate due to key problem
	public function populate() {
		return $this->populateBy('uuid',$this->uuid);
	}

	public function populateByNamespace($namespace) {
		return $this->populateBy('namespace',$namespace);
	}

	public function populateBy($field,$value) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where("`{$field}` = ?",$value);
		$retval = $this->populateWithSql($dbSelect->__toString());
		$this->postPopulate();
		return $retval;
	}

	// remove all entries in the database including its associated methods
	public function truncate() {
		$db = Zend_Registry::get('dbAdapter');
		$sql = "DELETE FROM `{$this->_table}`";
		$db->query($sql);
		$sql = "DELETE FROM `nsdrDefinitionMethods`";
		$db->query($sql);
	}

	public function removeByNamespace($namespace) {
		$db = Zend_Registry::get('dbAdapter');
		$sql = "DELETE FROM `{$this->_table}` WHERE `namespace`='{$namespace}'";
		return $db->query($sql);
	}

	public static function isORMClassImplementsMethod($className) {
		$ok = false;

		if (class_exists($className)) {
			$rc = new ReflectionClass($className);
			$interfaces = $rc->getInterfaceNames();
			$ok = in_array('NSDRMethods',$interfaces);
		}

		return $ok;
	}

	public function getMethods() {
		$method = new NSDRDefinitionMethod();
		return $method->getIteratorByParentId($this->uuid);
	}

	public function persistMethods(Array $methods) {
		$method = new NSDRDefinitionMethod();
		foreach ($methods as $row) {
			$item = clone $method;
			$item->nsdrDefinitionUuid = $this->uuid;
			$item->setPersistMode($this->_persistMode);
			$item->populateWithArray($row);
			$item->persist();
		}
	}

	public function isNamespaceExists($namespace) {
		$ok = false;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('namespace = ?',$namespace);
		if ($rows = $db->fetchRow($sqlSelect)) {
			$ok = true;
		}
		return $ok;
	}

	public function addNamespace($namespace,$ORMClass='',$aliasFor='',$recursive = true) {
		if ($recursive) {
			$x = explode('.',$namespace);
			$ns = array();
			foreach ($x as $n) {
				$ns[] = $n;
				$tmpNamespace = implode('.',$ns);
				if ($this->isNamespaceExists($tmpNamespace)) {
					continue;
				}
				$this->addNamespace($tmpNamespace,$ORMClass,$aliasFor,false);
			}
			return true;
		}
		$nsdrDefinition = new self();
		$nsdrDefinition->namespace = $namespace;
		$nsdrDefinition->aliasFor = $aliasFor;
		$nsdrDefinition->ORMClass = $ORMClass;
		$nsdrDefinition->uuid = NSDR::create_guid(); // random uuid
		$nsdrDefinition->persist();
		return true;
	}

	public function getNSDRDefinitionId() {
		return $this->uuid;
	}

}
