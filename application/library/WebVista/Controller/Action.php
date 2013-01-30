<?php
/*****************************************************************************
*       Action.php
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


class WebVista_Controller_Action extends Zend_Controller_Action {
    public function preDispatch() {
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $auth = Zend_Auth::getInstance();
        $currentUser = "Anonymous";
	if ($auth->hasIdentity()) {
		$currentUser = $auth->getIdentity()->username;
	}
	/*
	else {
		$user = new User();
                $user->username = $_SESSION['frame']['me']->_objects['user']->username;
                $user->userId = $_SESSION['frame']['me']->_objects['user']->id;
                Zend_Auth::getInstance()
                 ->getStorage()
                 ->write($user);
	}
	*/

        $view->authenticated = $auth->hasIdentity();

        $view->user = new WebVista_Model_User($auth->getIdentity());

	$request = Zend_Controller_Front::getInstance();

        $view->baseUrl = $request->getBaseUrl();
        $view->doctype('XHTML1_STRICT');
        $view->headTitle()->setSeparator(' / ');
        $view->headScript()->setAllowArbitraryAttributes(true);

        $view->headTitle(ucwords($request->getRequest()->getControllerName()));
        $view->headTitle(ucwords($request->getRequest()->getActionName()));
        //$currentUser = "Anonymous";
        $view->headTitle("Connected as " . $currentUser);

	if ($this->getRequest()->getControllerName() == 'login' &&
	    $this->getRequest()->getActionName() != 'complete') {
		return;
	}
	$cssUrl = $view->baseUrl . '/cache-file.raw/css?files=dojocss,dhtmlxcss';
	$view->headLink()->appendStylesheet($cssUrl);

	$view->headScript()->appendScript("function getBaseUrl() { return '{$view->baseUrl}'; }");

	$jsUrl = $view->baseUrl . '/cache-file.raw/js?files=chbootstrap,dojojs,dhtmlxjs';
	$view->headScript()->appendFile($jsUrl);

	$this->view->baseUrl = $view->baseUrl;
    }

	public static function buildJSJumpLink($objectId,$patientId,$objectClass) {
		$js = <<<EOL
// check if mainTabbar object exists
if (typeof mainTabbar != "undefined") {
	// check if tabId exists
	var tabId = 'tab_{$objectClass}';
	var tab = mainTabbar._getTabById(tabId);
	// set active patientId
	mainController.setActivePatient(patientId);
	if (tab == mainTabbar._lastActive) {
		mainTabbar.forceLoad(tabId); // force reload if tabId is the same as active tab
		return;
	}
	mainTabbar.setTabActive(tabId); // tabName should be dynamic
}

EOL;
		return $js;
	}

	public function __call($name, $args) {
		$request = Zend_Controller_Front::getInstance();
		$controllerName = $request->getRequest()->getControllerName();
		$actionName = substr($name,0,-6);
		throw new Exception('Sorry, the requested controller/action does not exist: '.$controllerName.'/'.$actionName);
	}


	protected function _renderToolbar($phtml = "toolbar") {
		header("Cache-Control: public");
		header("Pragma: public");

		$cache = Zend_Registry::get('cache');

		$className = get_class($this);
		$className = str_replace('Controller','',$className);
		$keyPref = substr(strtolower(preg_replace('/([A-Z]{1})/','_\1',$className)),1);

		$cacheKey = $keyPref . "-toolbar-" . Menu::getCurrentlySelectedActivityGroup() . "-" . Menu::getCurrentUserRole();
		$cacheKey = str_replace('-', '_', $cacheKey);
		$cacheKey = str_replace('/', '_', $cacheKey);
		if ($cache->test($cacheKey."_hash")) {
			$hash = $cache->load($cacheKey."_hash");
			$lastModified = $cache->load($cacheKey."_lastModified");
			$headers = getallheaders();
			if (isset($headers['If-None-Match']) && preg_match('/'.$hash.'/', $headers['If-None-Match'])) {
				header("Last-Modified: " . $lastModified);
				header('HTTP/1.1 304 Not Modified');
				exit;
			}
		}

		if ($cache->test($cacheKey)) {
			$items = $cache->load($cacheKey);
		}
		else {
			$items = $this->render($phtml);
			$hash = md5($items);
			$lastModified = gmdate("D, d M Y H:i:s")." GMT";
			$objConfig = new ConfigItem();
			$objConfig->configId = 'enableCache';
			$objConfig->populate();
			if ($objConfig->value) {
				$cache->save($hash, $cacheKey."_hash", array('tagToolbar'));
				$cache->save($lastModified, $cacheKey."_lastModified", array('tagToolbar'));
				$cache->save($items, $cacheKey, array('tagToolbar'));
			}
			header("ETag: ". $hash);
			header("Last-Modified: ". $lastModified);
			header("Content-length: "  . mb_strlen($items));
		}
		header("Content-type: text/xml");
		return $items;
	}

	protected function _setORMPersistMode(ORM $orm,$cascadePersist = false) {
		$fields = $orm->ormFields();
		foreach($fields as $value) {
			if ($orm->$value instanceof ORM) {
				$orm->$value->_cascadePersist = $cascadePersist;
				$this->_setORMPersistMode($orm->$value,$cascadePersist);
			}
		}
	}

}
