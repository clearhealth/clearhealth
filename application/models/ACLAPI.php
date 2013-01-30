<?php
/*****************************************************************************
*       ACLAPI.php
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
 * API for ACL
 *
 * ACL Items structure in memcache:
 * array('module [default]'=>array('controller [IndexController]'=>array('read'=>array('permission [indexAction]','permission [listAction]'),
 *                                               'write'=>array('permission [addAction]','permission [editAction]')),
 *                         'controller [ErrorController]'=>array('read'=>array('permission [indexAction]','permission [listAction]'),
 *                                               'write'=>array('permission [addAction]','permission [editAction]')),
 *      ))
 */
class ACLAPI {

	protected static $_aclItemsKey = 'aclItems';
	protected static $_modes = array('read','write','delete','other');

	/**
	 * Save ACL Items
	 *
	 * @param array $items
	 * @return boolean
	 * @throw Exception
	 */
	public static function saveACLItems(Array $items) {
		$memcache = Zend_Registry::get('memcache');
		$aclItems = $memcache->get(self::$_aclItemsKey);
		if ($aclItems === false) {
			// initialize an empty items
			$memcache->set(self::$_aclItemsKey,array());
		}
		foreach ($items as $moduleName=>$resourceList) {
			self::addModule($moduleName);
			foreach ($resourceList as $id=>$resources) {
				self::addResource($resources['name'],$moduleName);
				foreach (self::$_modes as $mode) {
					foreach ($resources[$mode] as $permission) {
						self::addPermission($mode,$permission['name'],$resources['name'],$moduleName);
					}
				}
			}
		}

	}

	/**
	 * Add module to existing ACL Items
	 *
	 * @param string $module
	 * @return boolean
	 * @throw Exception
	 */
	public static function addModule($module) {
		$memcache = Zend_Registry::get('memcache');
		$aclItems = $memcache->get(self::$_aclItemsKey);
		if ($aclItems === false) {
			$msg = __('No defined ACL.');
			throw new Exception($msg);
		}
		$ret = false;
		if (array_key_exists($module,$aclItems)) {
			$msg = __('Module ').$module.__(' already in the list.');
			throw new Exception($msg);
		}
		$aclItems[$module] = array();
		$ret = true;
		$memcache->set(self::$_aclItemsKey,$aclItems);
		return $ret;
	}

	/**
	 * Add resource to existing ACL Items
	 *
	 * @param string $resource
	 * @param string $module
	 * @return boolean
	 * @throw Exception
	 */
	public static function addResource($resource,$module='default') {
		$memcache = Zend_Registry::get('memcache');
		$aclItems = $memcache->get(self::$_aclItemsKey);
		if ($aclItems === false) {
			$msg = __('No defined ACL.');
			throw new Exception($msg);
		}
		$ret = false;
		foreach ($aclItems as $moduleName=>$resourceList) {
			if ($moduleName != $module) {
				continue;
			}
			if (array_key_exists($resource,$resourceList)) {
				$msg = __('Resource ').$resource.__(' already in the list.');
				throw new Exception($msg);
			}
			$aclItems[$moduleName][$resource] = array('read'=>array(),'write'=>array(),
								  'delete'=>array(),'other'=>array());
			// TODO: save to db?
			$ret = true;
			break;
		}
		if ($ret) {
			$memcache->set(self::$_aclItemsKey,$aclItems);
		}
		return $ret;
	}

	/**
	 * Add permission to existing ACL Items
	 *
	 * @param string $mode read|write|delete|other
	 * @param string $permission
	 * @param string $resource
	 * @return boolean
	 * @throw Exception
	 */
	public static function addPermission($mode,$permission,$resource,$module='default') {
		if (!in_array($mode,self::$_modes)) {
			$msg = __('Mode '.$mode.' does not supported.');
			throw new Exception($msg);
		}
		$memcache = Zend_Registry::get('memcache');
		$aclItems = $memcache->get(self::$_aclItemsKey);
		if ($aclItems === false) {
			$msg = __('No defined ACL.');
			throw new Exception($msg);
		}
		$ret = false;
		foreach ($aclItems as $moduleName=>$resourceList) {
			if ($moduleName != $module) {
				continue;
			}
			foreach ($resourceList as $resourceName=>$permissions) {
				if ($resource != $resourceName) {
					continue;
				}
				$permissionList = $permissions[$mode];
				if (in_array($permission,$permissionList)) {
					$msg = __('Permission ').$permission.__(' already in the list.');
					throw new Exception($msg);
				}
				$aclItems[$moduleName][$resourceName][$mode][] = $permission;
				// TODO: save to db?
				$ret = true;
				break 2;
			}
		}
		if ($ret) {
			$memcache->set(self::$_aclItemsKey,$aclItems);
		}
		return $ret;
	}

	/**
	 * check if access is valid
	 *
	 * @param string $mode read|write|delete|other
	 * @param string $permission
	 * @return boolean
	 * @throw Exception
	 */
	public static function checkACL($mode,$permission,$resource=null,$module=null) {
		if (!in_array($mode,self::$_modes)) {
			$msg = __('Mode '.$mode.' does not supported.');
			throw new Exception($msg);
		}
		$memcache = Zend_Registry::get('memcache');
		$aclItems = $memcache->get(self::$_aclItemsKey);
		if ($aclItems === false) {
			$msg = __('No defined ACL.');
			throw new Exception($msg);
		}
		$ret = false;
		foreach ($aclItems as $moduleName=>$resourceList) {
			if ($module !== null && $moduleName != $module) {
				continue;
			}
			foreach ($resourceList as $resourceName=>$permissions) {
				if ($resource !== null && $resourceName != $resource) {
					continue;
				}
				$permissionList = $permissions[$mode];
				if (in_array($permission,$permissionList)) {
					$ret = true;
					break 2;
				}
			}
		}
		return $ret;
	}
}

