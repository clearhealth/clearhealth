<?php
/*****************************************************************************
*       Acl.php
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

 
class WebVista_Acl extends Zend_Acl {
 
	protected static $_instance = null;
 
	private function __construct() {}
 
	private function __clone() {}
 
	protected function _initialize() {
	}

	public function populate() {
		$db = Zend_Db_Table::getDefaultAdapter();
		$dbSelect = $db->select()
			       ->from(array('arp' => 'aclRolePrivileges'),array('aclRoleId'))
			       ->join(array('ap'=>'aclPrivileges'),'arp.aclPrivilegeId = ap.aclPrivilegeId',array('aclPrivilegeName'))
			       ->join(array('ar'=>'aclResources'),'ap.aclResourceId = ar.aclResourceId',array('aclResourceName'))
			       ->join(array('am'=>'aclModules'),'ar.aclModuleId = am.aclModuleId',array('aclModuleName'));

		$roles = $db->fetchAll($dbSelect);
		foreach ($roles as $role) {
			if (!$this->has($role['aclModuleName'].'_'.$role['aclResourceName'])) {
				$this->add(new Zend_Acl_Resource($role['aclModuleName'].'_'.$role['aclResourceName']));
			}
			if (!$this->hasRole($role['aclRoleId'])) {
				$this->addRole(new Zend_Acl_Role($role['aclRoleId']));
			}
		}

		$defaultResources = array('default_error','default_login');
		// check default resource if exists; add otherwise
		foreach ($defaultResources as $def) {
			if (!$this->has($def)) {
				$this->add(new Zend_Acl_Resource($def));
			}
		}
 
		$this->deny();
		$this->allow(null,$defaultResources);
 
		foreach ($roles as $role) {
			$this->allow($role['aclRoleId'], $role['aclModuleName'].'_'.$role['aclResourceName'], $role['aclPrivilegeName']);
		}
	}

	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self();
			self::$_instance->_initialize();
		}
		return self::$_instance;
	}

	public function getLists() {
		$whiteLists = array();
		$whiteLists['read'] = array('index','list','get','ajaxget','view');
		$whiteLists['write'] = array('edit','ajaxedit','add','ajaxadd','process','ajaxprocess');
		$whiteLists['delete'] = array('delete','ajaxdelete');

		$ret = array();
		// retrieves all controllers directories registered at front controller
		$controllerDirs = Zend_Controller_Front::getInstance()->getControllerDirectory();
		foreach ($controllerDirs as $moduleName=>$controllerDir) {
			try {
				$directoryIterator = new DirectoryIterator($controllerDir);
			} catch (Exception $e) { // just use the parent Exception instead of UnexpectedValueException
				// just continue if path is unreadable or could not open
				continue;
			}
			$ret[$moduleName] = array();
			foreach ($directoryIterator as $file) {
				if ($file->isDot() || !$file->isFile() ||
				    !$file->isReadable() || substr($file->getFilename(),-3) != 'php') {
					continue;
				}
				include_once $controllerDir.DIRECTORY_SEPARATOR.$file;
				$className = substr($file->getFilename(),0,-4);
				// make sure $className ends with Controller
				if (substr($className,-10) != 'Controller') {
					// this is not a controller class
					continue;
				}
				$prettyClass = substr($className,0,-10);
				$prettyClassName = ucwords(strtolower(preg_replace('/([A-Z]{1})/',' \1',$prettyClass)));
				$ret[$moduleName][$prettyClass] = array();
				$ret[$moduleName][$prettyClass]['name'] = $className;
				$ret[$moduleName][$prettyClass]['prettyName'] = $prettyClassName;
				$ret[$moduleName][$prettyClass]['read'] = array();
				$ret[$moduleName][$prettyClass]['write'] = array();
				$ret[$moduleName][$prettyClass]['delete'] = array();
				$ret[$moduleName][$prettyClass]['other'] = array();
				$class = new ReflectionClass($className);
				$methods = $class->getMethods();
				foreach ($methods as $method) {
					$methodName = $method->getName();
					// make sure method is public and ends with Action
					if (!$method->isPublic() || substr($methodName,-6) != 'Action') {
						continue;
					}
					$prettyMethod = substr($methodName,0,-6);
					$prettyMethodName = ucwords(strtolower(preg_replace('/([A-Z]{1})/',' \1',$prettyMethod)));
					$item = array('name'=>$methodName,'prettyName'=>$prettyMethodName);
					$isFound = false;
					foreach ($whiteLists as $mode=>$lists) {
						foreach ($lists as $list) {
							if (strtolower(substr($methodName,0,strlen($list))) == $list) {
								$ret[$moduleName][$prettyClass][$mode][] = $item;
								$isFound = true;
								break 2;
							}
						}
					}
					if (!$isFound) {
						$ret[$moduleName][$prettyClass]['other'][] = $item;
					}
				}
			}
		}
		return $ret;
	}

	/* returns an SimpleXMLElement object */
	public function getDefaultList() {
		$whiteLists = array();
		$whiteLists['read'] = array('index','list','get','ajaxget','view');
		$whiteLists['write'] = array('edit','ajaxedit','add','ajaxadd','process','ajaxprocess');
		$whiteLists['delete'] = array('delete','ajaxdelete');

		$xml = new SimpleXMLElement('<clearhealth/>');
		// retrieves all controllers directories registered at front controller
		$controllerDirs = Zend_Controller_Front::getInstance()->getControllerDirectory();
		foreach ($controllerDirs as $moduleName=>$controllerDir) {
			try {
				$directoryIterator = new DirectoryIterator($controllerDir);
			} catch (Exception $e) { // just use the parent Exception instead of UnexpectedValueException
				// just continue if path is unreadable or could not open
				continue;
			}
			$module = $xml->addChild($moduleName);

			foreach ($directoryIterator as $file) {
				if ($file->isDot() || !$file->isFile() ||
				    !$file->isReadable() || substr($file->getFilename(),-3) != 'php') {
					continue;
				}
				include_once $controllerDir.DIRECTORY_SEPARATOR.$file;
				$className = substr($file->getFilename(),0,-4);
				// make sure $className ends with Controller
				if (substr($className,-10) != 'Controller') {
					// this is not a controller class
					continue;
				}
				$prettyClass = substr($className,0,-10);
				$prettyClassName = ucwords(strtolower(preg_replace('/([A-Z]{1})/',' \1',$prettyClass)));
				$resource = $module->addChild($prettyClass);

				$class = new ReflectionClass($className);
				$methods = $class->getMethods();
				foreach ($methods as $method) {
					$methodName = $method->getName();
					// make sure method is public and ends with Action
					if (!$method->isPublic() || substr($methodName,-6) != 'Action') {
						continue;
					}
					$prettyMethodName = substr($methodName,0,-6);
					$isFound = false;
					foreach ($whiteLists as $mode=>$lists) {
						foreach ($lists as $list) {
							if ($prettyMethodName == $list) {
								$res = $resource->addChild($mode,$prettyMethodName);
								$isFound = true;
								break 2;
							}
						}
					}
					if (!$isFound) {
						$res = $resource->addChild('other',$prettyMethodName);
					}
					$res->addAttribute('access','0');
				}
			}
		}
		return $xml;
	}
 
}
