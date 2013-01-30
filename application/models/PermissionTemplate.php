<?php
/*****************************************************************************
*       PermissionTemplate.php
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


class PermissionTemplate extends WebVista_Model_ORM {

	protected $permissionTemplateId;
	protected $name;
	protected $template;
	protected $_table = 'permissionTemplates';
	protected $_primaryKeys = array('permissionTemplateId');

	protected static $_states = array('started','starting','reloaded','reloading');
	protected static $_statusKey = 'permissionTemplateStatus';
	const ACL_MEMKEY = 'aclList';

	public function persist() {
		if ($this->_persistMode == WebVista_Model_ORM::DELETE) return parent::persist();
		$db = Zend_Registry::get('dbAdapter');
		$permissionTemplateId = (int)$this->permissionTemplateId;
		$data = $this->toArray();
		if ($permissionTemplateId > 0) {
			$ret = $db->update($this->_table,$data,'permissionTemplateId = '.$permissionTemplateId);
		}
		else {
			$this->permissionTemplateId = WebVista_Model_ORM::nextSequenceId();
			$data['permissionTemplateId'] = $this->permissionTemplateId;
			$ret = $db->insert($this->_table,$data);
		}
		return $this;
	}

	public static function serviceStatus() {
		$memcache = Zend_Registry::get('memcache');
		return $memcache->get(self::$_statusKey);
	}

	public static function serviceReload() {
		$memcache = Zend_Registry::get('memcache');
		$memcache->set(self::$_statusKey,self::$_states[3]);
		self::serviceStop();
		self::serviceStart();
		$memcache->set(self::$_statusKey,self::$_states[2]);
	}

	public static function serviceStop() {
		$memcache = Zend_Registry::get('memcache');
		$memcache->delete(self::$_statusKey,0);
	}

	public static function serviceStart() {
		$memcache = Zend_Registry::get('memcache');
		$memcache->set(self::$_statusKey,self::$_states[1]);
		$permissionTemplate = new self();
		$permissionTemplateIterator = $permissionTemplate->getIterator();
		foreach ($permissionTemplateIterator as $permission) {
			try {
				$xml = new SimpleXMLElement($permission->template);
				foreach ($xml as $moduleName=>$modules) {
					foreach ($modules as $resourceName=>$resources) {
						$arrResources = array();
						foreach ($resources as $key=>$value) {
							$access = (int)$value->attributes()->access;
							$value = (string)$value;
							$arrResources[$value] = array($key=>$access);
						}
						$memKey = hash('sha256',self::ACL_MEMKEY.'_'.$permission->permissionTemplateId.'_'.$resourceName);
						$memcache->set($memKey,$arrResources);
					}
				}
			}
			catch (Exception $e) {
				trigger_error($e->getMessage(),E_USER_NOTICE);
			}
		}
		$memcache->set(self::$_statusKey,self::$_states[0]);
	}

	public function buildTemplate(Array $templates,$moduleName='default') {
		$validModes = array('chkRead'=>'read','chkWrite'=>'write','chkDelete'=>'delete','chkOther'=>'other');
		$xml = new SimpleXMLElement('<clearhealth/>');
		$module = $xml->addChild($moduleName);
		foreach ($templates as $key=>$value) {
			$keys = explode('_',$key);
			if (!isset($keys[2]) || !isset($validModes[$keys[2]])) continue;
			$controllerName = $keys[0];
			$actionName = $keys[1];
			$mode = $validModes[$keys[2]];
			if (!isset($$controllerName)) {
				$$controllerName = $module->addChild($controllerName);
			}
			$action = $$controllerName->addChild($mode,$actionName);
			$action->addAttribute('access',$value);
			//LocationSelectController_indexAction_chkRead
		}
		$this->template = $xml->asXML();
		return $this;
	}

	public static function hasAccess($permissionTemplateId,$controllerName,$actionName) {
		$memcache = Zend_Registry::get('memcache');
		$ret = false;

		$controller = str_replace(' ','',ucwords(strtr($controllerName,'-.','  ')));
		$action = lcfirst(str_replace(' ','',ucwords(strtr($actionName,'-.','  '))));
		$memKey = hash('sha256',self::ACL_MEMKEY.'_'.$permissionTemplateId.'_'.$controller);
		$resources = $memcache->get($memKey);
		if ($resources !== false && isset($resources[$action])) {
			list($k,$v) = each($resources[$action]);
			if ($v) {
				$ret = true;
			}
		}
		return $ret;
	}

	public static function auditAccess($controllerName,$actionName) {
		$audit = new Audit();
		$audit->objectClass = 'AccessRecord';
		$audit->message = 'Accessed '.$controllerName.'/'.$actionName;
		if (isset($_GET['person_id']) && $_GET['person_id'] > 0) {
			$audit->patientId = (int)$_GET['person_id'];
		}
		if (isset($_GET['patient_id']) && $_GET['patient_id'] > 0) {
			$audit->patientId = (int)$_GET['patient_id'];
		}
		if (isset($_GET['personId']) && $_GET['personId'] > 0) {
			$audit->patientId = (int)$_GET['personId'];
		}
		if (isset($_GET['patientId']) && $_GET['patientId'] > 0) {
			$audit->patientId = (int)$_GET['patientId'];
		}
		//trigger_error($audit->message . $audit->patientId,E_USER_NOTICE);
		$audit->dateTime = date('Y-m-d H:i:s');
		$audit->persist();
	}

	public static function hasPermission($controllerName,$actionName) {
		$ret = false;
		$auth = Zend_Auth::getInstance();
		$permissionTemplateId = $auth->getIdentity()->permissionTemplateId;
		if ($auth->getIdentity()->emergencyAccess && ($controllerName != 'admin' && $controllerName != 'admin-persons')) $ret = true;
		if ($permissionTemplateId == 'superadmin' || self::hasAccess($permissionTemplateId,$controllerName,$actionName)) {
			$ret = true;
		}
		return $ret;
	}

}
