<?php
/*****************************************************************************
*       NSDRDefinitionMethod.php
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

class NSDRDefinitionMethod extends WebVista_Model_ORM {

	protected $uuid;
	protected $nsdrDefinitionUuid;
	protected $methodName;
	protected $method;
	protected $_table = "nsdrDefinitionMethods";
	protected $_primaryKeys = array("uuid");

	// overrides parent populate due to key problem
	public function populate() {
		return $this->populateBy('uuid',$this->uuid);
	}

	public function persist() {
		$db = Zend_Registry::get('dbAdapter');
		if ($this->_persistMode == WebVista_Model_ORM::DELETE) return parent::persist();
		$data = $this->toArray();
		if (!strlen($data['uuid']) > 0) {
			$this->uuid = NSDR::create_guid();
			$data['uuid'] = $this->uuid;
			$db->insert($this->_table,$data);
		}
		else {
			$db->update($this->_table,$data,'uuid = '.$db->quote($data['uuid']));
		}
		if ($this->shouldAudit()) {
			$audit = array();
			$audit['objectClass'] = get_class($this);
			$audit['objectId'] = $data['uuid'];
			$audit['auditValues'] = $data;
			Audit::persistManualAuditArray($audit);
		}
		return $this;
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

	public function getIteratorByParentId($id) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('`nsdrDefinitionUuid` = ?',$id);
		return parent::getIterator($dbSelect);
	}

	public function getNSDRDefinitionMethodId() {
		return $this->uuid;
	}

	public function isMethodNameExists() {
		$ret = false;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table,'uuid')
				->where('`nsdrDefinitionUuid` = ?',$this->nsdrDefinitionUuid)
				->where('`methodName` = ?',$this->methodName);
		if (strlen($this->uuid) > 0) {
			$sqlSelect->where('`uuid` != ?',$this->uuid);
		}
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		return $ret;
	}

	public static function normalizeMethodName($name) {
		$name = ucwords($name);
		$name = preg_replace('/\ /','',$name);
		$name = preg_replace('/[^a-z0-9]/i','_',$name);
		$name = lcfirst($name);
		if (is_numeric(substr($name,0,1))) {
			$name = '_'.$name;
		}
		return $name;
	}

	public static function isPHPCodeValid($code,$methodName) {
		$ret = true;
		$file = tempnam('/tmp','tmp_');
		$code = '<?php '.$code;
		if (file_put_contents($file,$code)) {
			$cmd = 'php -l '.$file;
			$result = shell_exec($cmd);
			$result = str_replace($file,$methodName,trim($result));
			if (!preg_match('/^No syntax errors detected(.*)/i',$result)) {
				$ret = $result;
			}
		}
		else {
			trigger_error('Unable to write file to '.$file);
		}
		unlink($file);
		return $ret;
	}

}
