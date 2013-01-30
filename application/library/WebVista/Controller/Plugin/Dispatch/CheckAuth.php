<?php
/*****************************************************************************
*       CheckAuth.php
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

class WebVista_Controller_Plugin_Dispatch_CheckAuth extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$auth = Zend_Auth::getInstance();
		$publicPages = array();
		$publicPages['controllers'] = array('login','logout');
		$publicPages['actions'] = array();
		$controllerName = $request->getControllerName();
		$actionName = $request->getActionName();

		if (in_array($controllerName,$publicPages['controllers'])) {
			return true;
		}
		PermissionTemplate::auditAccess($controllerName,$actionName);
		if (!$auth->hasIdentity() && $controllerName != 'index') { // this MUST be placed before checking permission
			do {
				if (isset($_SERVER['PHP_AUTH_USER']) && strlen($_SERVER['PHP_AUTH_USER']) > 0) {
					User::processLogin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
					if ($auth->hasIdentity()) {
						break; // allow to check permission below
					}
				}
				header('WWW-Authenticate: Basic realm="Unauthorized Access Prohibited (ClearHealth)"');
				header('HTTP/1.0 401 Unauthorized');
				die(__('You must enter a valid username and password to access.'));
			} while(false);
		}
		if ($auth->hasIdentity()) {
			$permissionTemplateId = $auth->getIdentity()->permissionTemplateId;
			if (	file_exists('/tmp/emergency') 
				&& $controllerName != 'admin-persons' 
				&& PermissionTemplate::hasAccess($permissionTemplateId,'emergency-access','allow-emergency-access')
			) {
					if (!($controllerName == "emergency-access" && $actionName == 'index')) {
						return true;
					}
			}
			if ($permissionTemplateId != 'superadmin' && !PermissionTemplate::hasAccess($permissionTemplateId,$controllerName,$actionName)) {
				$error = 'Access denied. '.$controllerName.'/'.$actionName.'. ';
				$error .= 'Please <a href="'.$request->getBaseUrl().'/logout" title="Login">Login</a>.';
				trigger_error($error,E_USER_NOTICE);
				throw new WebVista_App_AuthException($error);
			}
			else {
				return true;
			}
		}
		throw new WebVista_App_AuthException('You must be authenticated to access the system.');
	}

}
