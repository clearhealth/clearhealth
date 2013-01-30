<?php
/*****************************************************************************
*       AclController.php
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


class AclController extends WebVista_Controller_Action {

	protected $_memcache = null;
	protected $_chkLabelRead = 'chkRead';
	protected $_chkLabelWrite = 'chkWrite';
	protected $_chkLabelDelete = 'chkDelete';
	protected $_chkLabelOther = 'chkOther';

	public function init() {
		$this->_memcache = Zend_Registry::get('memcache');
	}

	public function indexAction() {
		$this->view->chkLabelRead = $this->_chkLabelRead;
		$this->view->chkLabelWrite = $this->_chkLabelWrite;
		$this->view->chkLabelDelete = $this->_chkLabelDelete;
		$this->view->chkLabelOther = $this->_chkLabelOther;
		$permissionTemplate = new PermissionTemplate();
		$permissionTemplateIterator = $permissionTemplate->getIterator();
		$this->view->templates = $permissionTemplateIterator->toArray('permissionTemplateId','name');
		$this->render();
	}

	protected function _generateDefaultTemplateXML() {
		$aclMemKey = PermissionTemplate::ACL_MEMKEY.'_default';
		$items = $this->_memcache->get($aclMemKey); // get returns FALSE if error or key not found
		if ($items === false) {
			trigger_error("before generating list: " . calcTS(),E_USER_NOTICE);
			$xml = WebVista_Acl::getInstance()->getDefaultList();
			$items = $xml->asXML();
			$this->_memcache->set($aclMemKey,$items);
			trigger_error("after generating list: " .calcTS(),E_USER_NOTICE);
		}
		if (!isset($xml)) {
			$xml = new SimpleXMLElement($items);
		}
		return $xml;
	}

	public function listAction() {
		$templateId = (int)$this->_getParam('templateId');
		$refresh = (int)$this->_getParam('refresh');
		$rows = array();
		if ($templateId > 0 && !$refresh > 0) {
			$permissionTemplate = new PermissionTemplate();
			$permissionTemplate->permissionTemplateId = $templateId;
			if ($templateId > 0 && $permissionTemplate->populate()) {
				try {
					$xml = new SimpleXMLElement($permissionTemplate->template);
				}
				catch (Exception $e) {
					trigger_error($e->getMessage(),E_USER_ERROR);
				}
			}
		}
		else {
			$xml = $this->_generateDefaultTemplateXML();
		}
		$xmlResources = array();
		foreach ($xml as $moduleName=>$modules) {
			foreach ($modules as $resourceName=>$resources) {
				if (!isset($xmlResources[$moduleName])) {
					$xmlResources[$moduleName] = array();
				}
				$xmlResources[$moduleName][$resourceName] = $resources;
			}
		}
		ksort($xmlResources);
		ksort($xmlResources['default']);
		foreach ($xmlResources as $moduleName=>$modules) {
			foreach ($modules as $resourceName=>$resources) {
				$arrResources = array();
				$arrResources['read'] = array();
				$arrResources['write'] = array();
				$arrResources['delete'] = array();
				$arrResources['other'] = array();
				foreach ($resources as $key=>$value) {
					$access = (int)$value->attributes()->access;
					$value = (string)$value;
					$prettyName = ucwords(strtolower(preg_replace('/([A-Z]{1})/',' \1',$value)));
					$arrResources[$key][] = array('name'=>$value,'prettyName'=>$prettyName,'value'=>$access);
				}
				$prettyName = ucwords(strtolower(preg_replace('/([A-Z]{1})/',' \1',$resourceName)));
				$tmp = array();
				$tmp['id'] = $resourceName;
				// Resource
				$tmp['data'][] = $prettyName;
				// Read
				$tmp['data'][] = implode("<br />\n",$this->_generateCheckboxInputs($this->_chkLabelRead,$arrResources['read'],$resourceName));
				// Write
				$tmp['data'][] = implode("<br />\n",$this->_generateCheckboxInputs($this->_chkLabelWrite,$arrResources['write'],$resourceName));
				// Delete
				$tmp['data'][] = implode("<br />\n",$this->_generateCheckboxInputs($this->_chkLabelDelete,$arrResources['delete'],$resourceName));
				// Other
				$tmp['data'][] = implode("<br />\n",$this->_generateCheckboxInputs($this->_chkLabelOther,$arrResources['other'],$resourceName));
				$rows[] = $tmp;
			}
		}

		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function reloadPermissionsAction() {
		$acl = WebVista_Acl::getInstance();
		// populate acl from db
		$acl->populate();
		// save to memcache
		$this->_memcache->set('acl',$acl);
		Zend_Registry::set('acl',$acl);

		$items = $acl->getLists();
		$this->_memcache->set(PermissionTemplate::ACL_MEMKEY,$items);
		ACLAPI::saveACLItems($items);

		$data = array();
		$data['msg'] = __('Permissions reload successfully.');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function getMenuXmlAction() {
		$this->view->xmlHeader = '<?xml version=\'1.0\' encoding=\'iso-8859-1\'?>' . "\n";
		$contentType = (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) ? "application/xhtml+xml" : "text/xml";
		header("Content-type: ". $contentType);
		$this->render();
	}

	public function getRolesXmlAction() {
		$this->view->xmlHeader = '<?xml version=\'1.0\' encoding=\'iso-8859-1\'?>' . "\n";
		$contentType = (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) ? "application/xhtml+xml" : "text/xml";
		header("Content-type: ". $contentType);
		$this->render();
	}

	public function ajaxSavePermissionAction() {
		$chkName = $this->_getParam('name');
		$value = $this->_getParam('value');
		$template = $this->_getParam('template');
		$access = $this->_getParam('access');
		if ($access == 'all') {
			// save all permission
		}
		else {
			$eAccess = explode('_',$access);
			$resourceName = $eAccess[0]; // controller name
			$permissionName = $eAccess[1]; // action name
			// individual save
		}
		$data = array();
		$data['msg'] = __('Permissions save successfully.');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _generateCheckboxInputs($name,Array $values,$resourceName) {
		$ret = array();
		foreach ($values as $value) {
			$checked = '';
			if ($value['value'] > 0) {
				$checked = 'checked="checked"';
			}
			$id = $resourceName.'_'.$value['name'].'_'.$name;
			$ret[] = '<input type="hidden" name="acl['.$id.']" id="'.$id.'" value="'.$value['value'].'" />
				<input type="checkbox" id="chk_'.$id.'" name="'.$name.'" value="'.$id.'" onClick="toggleItem(this)" '.$checked.' /> '.$value['prettyName'];
		}
		return $ret;
	}

	public function refreshResourcesAction() {
		$acl = WebVista_Acl::getInstance();
		// populate acl from db
		$acl->populate();
		// save to memcache
		$this->_memcache->set('acl',$acl);
		Zend_Registry::set('acl',$acl);

		$items = $acl->getLists();
		$this->_memcache->set(PermissionTemplate::ACL_MEMKEY,$items);
		ACLAPI::saveACLItems($items);

		$data = array();
		$data['msg'] = __('Resources refresh successfully.');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processSaveTemplateAction() {
		$params = $this->_getParam('acl');
		$templateId = (int)$params['templateId'];
		$templateName = $params['templateName'];
		unset($params['templateId']);
		unset($params['templateName']);
		$permissionTemplate = new PermissionTemplate();
		if ($templateId > 0) {
			$permissionTemplate->permissionTemplateId = $templateId;
			$permissionTemplate->populate();
		}
		else {
			$permissionTemplate->name = $templateName;
		}
		$permissionTemplate->buildTemplate($params);
		$permissionTemplate->persist();

		$data = array();
		$data['id'] = $permissionTemplate->permissionTemplateId;
		$data['name'] = $permissionTemplate->name;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function copyTemplateAction() {
		$permissionTemplate = new PermissionTemplate();
		$permissionTemplateIterator = $permissionTemplate->getIterator();
		$permissionTemplates = $permissionTemplateIterator->toArray('permissionTemplateId','name');
		//$permissionTemplates['superadmin'] = 'Super Administrator';
		$this->view->permissionTemplates = $permissionTemplates;
		$this->view->permissionTemplateId = (int)$this->_getParam('permissionTemplateId');
		$this->render('copy-template');
	}

	public function processCopyTemplateAction() {
		$permissionTemplateId = (int)$this->_getParam('permissionTemplateId');
		$templateType = $this->_getParam('templateType');
		$templateValue = $this->_getParam('templateValue');

		$permissionTemplate = new PermissionTemplate();
		$permissionTemplate->permissionTemplateId = $permissionTemplateId;
		if (!$permissionTemplate->populate()) {
			$this->_helper->autoCompleteDojo(array());
		}

		$templateName = $templateValue;
		if ($templateType == 'user') {
			$user = new User();
			$user->userId = $templateValue;
			$user->populate();
			$templateName = 'Special template for User '.$user->username;
		}
		$permissionTemplate->permissionTemplateId = 0;
		$permissionTemplate->name = $templateName;
		$permissionTemplate->persist();
		if (isset($user)) {
			$user->permissionTemplateId = $permissionTemplate->permissionTemplateId;
			$user->persist();
		}

		$data = array();
		$data['id'] = $permissionTemplate->permissionTemplateId;
		$data['name'] = $permissionTemplate->name;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processAddAction() {
		$permissionTemplateId = (int)$this->_getParam('aclTemplateId');
		$type = $this->_getParam('type');
		$value = preg_replace('/[^a-zA-Z]+/','',ucwords(strtolower($this->_getParam('value',''))));
		$prettyName = ucwords(preg_replace('/([A-Z]{1})/',' \1',$value));

		$data = false;
		$isDefault = false;
		$permissionTemplate = new PermissionTemplate();
		$permissionTemplate->permissionTemplateId = $permissionTemplateId;
		if ($permissionTemplate->populate()) {
			$xml = new SimpleXMLElement($permissionTemplate->template);
		}
		else {
			$xml = $this->_generateDefaultTemplateXML();
			$isDefault = true;
		}
		$defaultModule = 'default';
		$error = '';
		switch ($type) {
			case 'resource':
				if (isset($xml->$defaultModule->$value)) {
					$error = __('Resource already exists').': '.$value;
					trigger_error($error,E_USER_NOTICE);
					break;
				}
				$xml->$defaultModule->addChild($value);
				$data['id'] = $value;
				$data['name'] = $prettyName;
				break;
			case 'permission':
				$resourceId = $this->_getParam('resourceId');
				$mode = strtolower($this->_getParam('mode'));
				if (!isset($xml->$defaultModule->$resourceId)) {
					$error = __('Resource not exists').': '.$resourceId;
					trigger_error($error,E_USER_NOTICE);
					break;
				}
				$action = $xml->$defaultModule->$resourceId->addChild($mode,lcfirst($value));
				$action->addAttribute('access','0');
				$newMode = ucfirst($mode);
				$chkMode = '_chkLabel'.$newMode;
				$data = $this->_generateCheckboxInputs($this->$chkMode,array(array('name'=>$value,'prettyName'=>$prettyName,'value'=>0)),$resourceId);
				break;
			default:
				$error = __('Invalid type').': '.$type;
				trigger_error($error,E_USER_NOTICE);
		}

		if (strlen($error) > 0) {
			$data['error'] = $error;
		}
		else {
			if ($isDefault) {
				$aclMemKey = PermissionTemplate::ACL_MEMKEY.'_default';
				$this->_memcache->set($aclMemKey,$xml->asXML());
			}
			else {
				$permissionTemplate->template = $xml->asXML();
				$permissionTemplate->persist();
			}
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}

