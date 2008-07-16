<?php
/**
* Helper methods useful in building Celini apps
*
* All methods may be used statically unless otherwise documented
*
* @author	Joshua Eichorn	<jeichorn@mail.com>
*/
$GLOBALS['CeliniPageTypes'] = array(
	'main',
	'util',
	'minimal',
	'print'
);
class Celini {

	/**
         * Passthrough to static config check method
         *
         **/
        function config_get($value,$default = false, $parent = NULL) {
                return clniConfig::cget($value,$default,$parent);
        }

	/**
	 * Gets the full path to a template
	 *
	 * First checks if there is an app template
	 * Then checks if there is a Celini Template
	 * If neither raises an error
	 */
	function getTemplatePath($template) {
		$template_file = Celini::_findTemplatePath($template);
		return $template_file === false ? $template : realpath($template_file);
	}
	
	
	/**
	 * Quick template finder for {@link getTemplatePath()}.  Follows the same
	 * pattern (little P) as the last two I've committed.
	 *
	 * @param  string
	 * @return string|false
	 * @access private
	 */
	function _findTemplatePath($template) {
		if (substr($template,0,1) !== '/') {
			$template = '/'.$template;
		}
		$path = '/templates' . $template;
		if (file_exists(APP_ROOT . '/local' . $path)) {
			return APP_ROOT . '/local' . $path;
		}
		
		if (isset($GLOBALS['configObj'])) {
			$module_paths = $GLOBALS['configObj']->get('module_paths');
			// insures that module_paths is an array so foreach doesn't choke on it
			settype($module_paths, 'array');
			
			foreach ($module_paths as $module_path) {
				if (file_exists($module_path . '/local' . $path)) {
					return $module_path . '/local' . $path;
				}
			}
		}
		
		if (file_exists(CELINI_ROOT . $path)) {
			return CELINI_ROOT . $path;
		}
		
		return false;
	}

	/**
	 * Check if a string is a controller
	 *
	 * @param	string	$controller	The controller name	(default,contacts,etc)
	 * @return	boolean
	 */
	function checkController($controller) {
		$controller = ucfirst($controller);
		$path = "/controllers/C_" . $controller . ".class.php";
		if (file_exists(APP_ROOT."/local/$path") || file_exists(CELINI_ROOT.$path)) {
			return true;
		}
		else {
			// Check for installed modules.
			if (isset($GLOBALS['configObj'])) {
				$module_paths = $GLOBALS['configObj']->get('module_paths');
				settype($module_paths, 'array');
				
				foreach ($module_paths as $module_path) {
					if (file_exists($module_path . '/local' . $path)) {
						return true;
					}
				}
			}
			return false;
		}
	}

	/**
	* Create a link
	*
	* Pagetype and controller are autodetected by method default values
	*
	* <code>
	* // setting and action and default value auto detecting controller and pagetype
	* Celini::link('test',true,true,1); // output might be: /index.php/main/default/test/1
	* </code>
	*
	* The args map too:
	* /$basePath/$pageType/$controller/$action/$defaultArg/$managerArg
	* 
	*
	* @param	bool|string	$action		if true return teh current action
	* @param	bool|string	$controller	if false current controller is used
	* @param	bool|string	$pagetype	if false no pagetype, if true we autodect the current setting, if a string the string value is used
	* @param	bool|string	$defaultArg	false for no argument, argument appended to the url
	* @param	bool		$basePath	false to not include _SERVER[SCRIPT_NAME] at the front of the path
	* @todo support GET style
	*/
	function link($action,$controller = true,$pagetype = true,$defaultArg = false,$managerArg = false,$basePath = true) {

		if ($basePath === true) {
			$path = $_SERVER['SCRIPT_NAME'];
		}
		else if(strlen($basePath) > 0) {
			$path = $basePath;
		}
		else {
			$path = "";
		}

		if ($action === true) {
			$action = Celini::getCurrentAction();
		}

		if (is_string($pagetype)) {
			$path .= "/{$pagetype}";
		}
		elseif (Celini::getCurrentPageType() != '') {
			if ($pagetype === true) {
				$path .= "/".Celini::getCurrentPageType();	
			}
			else if ($pagetype !== false) {
				$path .= "/$pagetype";
			}
		}

		if ($controller === true) {
			$path .= "/".Celini::getCurrentController()."/";
		}
		else {
			$path .= "/$controller/";
		}

		$path .= $action;

		if ($managerArg !== false) {
			$path .= "/".$managerArg;
		}

		if ($defaultArg !== false) {
			$path .= "/".$defaultArg;
		}
		$path .= "?";
		return $path;
	}

	/**
	 * Manager link, better ordered link method
	 *
	 * @see link
	 */
	function managerLink($managerArg = false, $defaultArg = false, $action = true, $controller = true, $pagetype = true) {
		return Celini::link($action,$controller,$pagetype,$defaultArg,$managerArg);
	}


	/**
	 * Get the current action from the path
	 *
	 * @todo support GET style
	 */
	function getCurrentAction() {
		$uri = "";
		if (isseT($_SERVER['PATH_INFO'])) {
			$uri = $_SERVER['PATH_INFO'];
		}
		if (substr($uri,-1) == "/") {
			$uri = substr($uri,0,-1);	
		}
		if (substr($uri,0,1) == "/") {
			$uri = substr($uri,1);	
		}
		$tmp = explode("/",$uri);
		if (isset($tmp[0]) && isset($tmp[1]) && isset($tmp[2])) {
			return $tmp[2];
		}
		elseif (isset($tmp[0]) && !in_array(strtolower($tmp[0]),$GLOBALS['CeliniPageTypes']) && isset($tmp[1])) {
			return $tmp[1];
		}
		return "default";
	}

	/**
	 * Get the current page type from the path
	 *
	 * @todo support GET style
	 */
	function getCurrentPageType() {
		$uri = "";
		if (isseT($_SERVER['PATH_INFO'])) {
			$uri = $_SERVER['PATH_INFO'];
		}
		if (substr($uri,-1) == "/") {
			$uri = substr($uri,0,-1);	
		}
		if (substr($uri,0,1) == "/") {
			$uri = substr($uri,1);	
		}
		$tmp = explode("/",$uri);
		if ((isset($tmp[0]) && $tmp[0] != Celini::getCurrentController()) || Celini::getCurrentController() === false) {
			if (in_array(strtolower($tmp[0]),$GLOBALS['CeliniPageTypes'])) {
				if (isset($GLOBALS['util']) && $GLOBALS['util'] == true) {
					return "util";
				}
				return $tmp[0];
			}
			else {
				return 'main';
			}
		}	
		return "";
	}

	/**
	 * Get the current controller from the path
	 *
	 * @todo support GET style
	 */
	function getCurrentController() {
		$uri = "";
		if (isseT($_SERVER['PATH_INFO'])) {
			$uri = $_SERVER['PATH_INFO'];
		}
		if (substr($uri,-1) == "/") {
			$uri = substr($uri,0,-1);	
		}
		if (substr($uri,0,1) == "/") {
			$uri = substr($uri,1);	
		}
		$tmp = explode("/",$uri);
		if (isset($tmp[0]) && in_array(strtolower($tmp[0]),$GLOBALS['CeliniPageTypes'])) {
			if (isset($tmp[1])) {
				$controller = $tmp[1];
			}
			else {
				$controller = "default";
			}
		}
		else if (isset($tmp[0])) {
			$controller = $tmp[0];
		}

		$controller = ucfirst($controller);
		if (Celini::checkController($controller)) {
			return $controller;
		}
		return false;
	}

	/** 
	 * Get the current base_dir
	 */
	function getBaseDir() {
		$base_dir = dirname($_SERVER['SCRIPT_NAME']);
		if ($base_dir == "/") {
			$base_dir = "/";
		}
		else {
			$base_dir .= "/";
		}
		return htmlspecialchars($base_dir);
	}
	
	/** 
	 * Get the current base_uri
	 */
	function getBaseURI() {
		return $GLOBALS['base_uri'];
	}

	/**
	* Get billing variation template dir
	*/
	function getVariationsDir() {
		return APP_ROOT . "/modules/billing/local/templates/variations/";
	}

	/**
	 * Raise an error
	 */
	function raiseError($message,$type = E_USER_ERROR) {
		if (!function_exists('xdebug_enable')) {
			//echo "<pre>";
			//var_dump(debug_backtrace());
			//echo "</pre>";
		}
		debug_print_backtrace();
		trigger_error($message,$type);
	}

	/**
	 * Raise a deprecation warning if those are enabled
	 */
	function deprecatedWarning($message) {
		if (isset($GLOBALS['config']['show_deprecated_warnings']) && $GLOBALS['config']['show_deprecated_warnings']) {
			Celini::raiseError("Deprecation Warning: ".$message);
		}
	}

	/**
	 * Redirect the user to there default link location
	 *
	 * If the user has a default set in prefs (or in default prefs) goto that
	 * else goto app specifed default
	 *
	 * @todo read default location from prefs
	 */
	function redirectDefaultLocation() {

		// site defaults
		$action = '';
		$controller = '';
		if(isset($GLOBALS['config']['default_action'])){
			$action = $GLOBALS['config']['default_action'];
		}
		if(isset($GLOBALS['config']['default_controller'])){
			$controller = $GLOBALS['config']['default_controller'];
		}

		if (isset($_SESSION['prefs']['default'])) {
			$dprefs = $_SESSION['prefs']['default'];
			$uprefs = $_SESSION['prefs']['user'];
			
			$prefs = $dprefs->tree;
			if (count($uprefs->tree) > 0) {
				$prefs = array_merge_recursive($dprefs->tree,$uprefs->tree); 	
			}
	
			// get id of Start Page
			if (isset($dprefs->_name_id['Start Page'])) {
				$id = $dprefs->_name_id['Start Page']['id'];
				$value = $prefs[$dprefs->_root][$id];
				
				list($controller,$action) = explode('/',$value);
	
			}
		}

		$location = Celini::link($action,$controller);

		header("Location: $location");
		exit;
	}

	/**
	 * Get the default database instance
	 */
	function &dbInstance() {
		return $GLOBALS['config']['adodb']['db'];
	}

	/**
	 * Get an clniHTMLHead instance
	 */
	function &HTMLHeadInstance() {
		$GLOBALS['loader']->requireOnce('includes/clni/clniHTMLHead.class.php');
		return clniHTMLHead::getInstance();
	}

	/**
	 * Get an clniSession instance
	 */
	function &sessionInstance() {
		$GLOBALS['loader']->requireOnce('includes/clni/clniSession.class.php');
		if (!isset($GLOBALS['_CACHE']['clniSession'])) {
			$GLOBALS['_CACHE']['clniSession'] =& new clniSession();
		}
		return $GLOBALS['_CACHE']['clniSession'];
	}
	function isTabSelected($tabKey) {
                $tabKey = preg_replace('/[^A-Za-z0-9\/]/','',$tabKey);
                $tabKeys = split('/',$tabKey);
                if (count($tabKeys) == 2) {
                  $session =& Celini::SessionInstance();
                  $session->setNamespace('tabState'.$tabKeys[0]);
		 // var_dump($session->_getAll());
		  //var_dump($_SESSION['_clniSession']);
		  if ($session->_getAll() === false) {
			//no tab for this group currently selected so set this one as default
			$this->setTabSelected($tabKey);
			$session->setNamespace("default");
			return true;		
		  }
                  elseif ($session->_get($tabKeys[1]) == 1) {
			$session->setNamespace("default");
                        return true;
                  }
                }
			$session->setNamespace("default");
                        return false;

	}
	
	function setTabSelected($tabKey) {
		$tabKey = preg_replace('/[^A-Za-z0-9\/]*/','',$tabKey);
                $tabKeys = split('/',$tabKey);
                if (count($tabKeys) == 2) {
                  $session =& Celini::SessionInstance();
                  $session->setNamespace('tabState'.$tabKeys[0]);
                  $session->clear();
                  $session->set($tabKeys[1],1);
                }
		$session->setNamespace("default");
	}

	function setPaletteSelected($tabKey) {
                $tabKey = preg_replace('/[^A-Za-z0-9\/]*/','',$tabKey);
                $session =& Celini::SessionInstance();
                $session->setNamespace('paletteState');
                $session->set($tabKey,1);
		$session->setNamespace("default");
        }
	function setPaletteUnselected($tabKey) {
                $tabKey = preg_replace('/[^A-Za-z0-9\/]*/','',$tabKey);
                $session =& Celini::SessionInstance();
                $session->setNamespace('paletteState');
                $session->set($tabKey,0);
		$session->setNamespace("default");
        }
	function isPaletteSelected($tabKey) {
                $tabKey = preg_replace('/[^A-Za-z0-9\/]/','',$tabKey);
                $session =& Celini::SessionInstance();
                $session->setNamespace('paletteState');
                if ($session->_get($tabKey) == 1) {
			$session->setNamespace("default");
                        return true;
                }
		$session->setNamespace("default");
                return false;
	}
	

	/**
	 * Get an ajax helper instance
	 */
	function &ajaxInstance() {
		if (!isset($GLOBALS['_CACHE']['HTML_AJAX'])) {
			$GLOBALS['loader']->requireOnce('/lib/PEAR/HTML/AJAX/Helper.php');
			$GLOBALS['_CACHE']['HTML_AJAX'] = new HTML_AJAX_Helper();
			$url = Celini::link(false,'ajax',false,false);
			$url = substr($url,0,strlen($url)-1);
			$GLOBALS['_CACHE']['HTML_AJAX']->serverUrl = $url;
			$GLOBALS['_CACHE']['HTML_AJAX']->jsLibraries[] = 'Alias';
			$GLOBALS['_CACHE']['HTML_AJAX']->jsLibraries[] = 'validate';
			$GLOBALS['_CACHE']['HTML_AJAX']->jsLibraries[] = 'clniBehaviors';
			$GLOBALS['_CACHE']['HTML_AJAX']->jsLibraries[] = 'clniUtil';
			$GLOBALS['_CACHE']['HTML_AJAX']->jsLibraries[] = 'urlserializer';
			$GLOBALS['_CACHE']['HTML_AJAX']->jsLibraries[] = 'queues';
		}
		return $GLOBALS['_CACHE']['HTML_AJAX'];
	}

	/**
	 * Get an instance of the ajax server class
	 */
	function &ajaxServerInstance() {
		if (!isset($GLOBALS['_CACHE']['HTML_AJAX_SERVER'])) {
			$GLOBALS['loader']->requireOnce('/lib/PEAR/HTML/AJAX/Server.php');
			$GLOBALS['_CACHE']['HTML_AJAX_SERVER'] =& new HTML_AJAX_Server();
			$GLOBALS['_CACHE']['HTML_AJAX_SERVER']->clientJsLocation = CELINI_ROOT . '/js/HTML_AJAX/';
		}
		return $GLOBALS['_CACHE']['HTML_AJAX_SERVER'];
	}

	function utility_array($objs, $key, $value) {
		$ua = array();
		
		foreach ($objs as $obj) {
			$ua[$obj->$key] = $obj->$value;	
		}
		return $ua;	
	}

	/**
	 * Create an associative array from an array of ORDO objects
	 */
	function utilityArray($objs, $key, $value) {
		$ua = array();
		
		foreach ($objs as $obj) {
			$ua[$obj->$key] = $obj->$value;	
		}
		return $ua;	
	}
	
	
	/**
	 * Used for creating and returning new ORDO object
	 *
	 * @param  string
	 * @param  string
	 * @return ORDataObject
	 */
	function &newORDO($name, $arguments = array(), $setupSuffix = '') {
		$ordoFactory =& new ORDOFactory($name, $setupSuffix);
		$return =& $ordoFactory->newORDO($arguments);
		return $return;
	}

	/**
	 * Get a config instance
	 *
	 * @param string	$type	Optional config type, if none is specified a default celini config is returned
	 * @todo init config as needed from here
	 */
	function &configInstance($type = true) {
		if ($type === true) {
			return $GLOBALS['configObj'];
		}
		else {
			if (!isset($GLOBALS['configObjs'][$type])) {
				$class = $type.'Config';
				if (!class_exists($class)) {
					$GLOBALS['loader']->requireOnce("includes/$class.class.php");
				}
				$GLOBALS['configObjs'][$type] = new $class();
			}
			return $GLOBALS['configObjs'][$type];
		}
	}

	/**
	 * Get an instance of the trail
	 */
	function &trailInstance() {
		if (!isset($_SESSION['CLNITRAIL'])) {
			$_SESSION['CLNITRAIL']	= new clniTrail();
		}
		return $_SESSION['CLNITRAIL'];
	}

	/**
	 * Get an enum manager instance
	 */
	function &enumManagerInstance() {
		$GLOBALS['loader']->requireOnce('includes/EnumManager.class.php');
		if (!isset($GLOBALS['_CACHE']['ENUM_MANAGER'])) {
			$GLOBALS['_CACHE']['ENUM_MANAGER'] = new EnumManager();
		}
		return $GLOBALS['_CACHE']['ENUM_MANAGER'];
	}
	
	
	/**
	 * Returns a reference to a {@link clniFilter}ed version of the $_GET 
	 * super-global.
	 *
	 * @return clniFilter
	 */
	function &filteredGet() {
		if (!isset($GLOBALS['_cleanedGet'])) {
			$GLOBALS['_cleanedGet'] =& new clniFilter('GET');
		}
		
		return $GLOBALS['_cleanedGet'];
	}
	
	
	/**
	 * Returns a reference to a {@link clniFilter}ed version of $_POST super-global.
	 *
	 * @return clniFilter
	 */
	function &filteredPost() {
		if (!isset($GLOBALS['_cleanedPost'])) {
			$GLOBALS['_cleanedPost'] =& new clniFilter('POST');
		}
		
		return $GLOBALS['_cleanedPost'];
	}


	function &dpmInstance() {
		if (!isset($GLOBALS['_dpmInstance'])) {
			$GLOBALS['_dpmInstance'] =& new DestinationProcessorManager();
			$GLOBALS['_dpmInstance']->init();
		}
		return $GLOBALS['_dpmInstance'];
		
	}
	
	
	/**
	 * Return the {@link UserProfile} object associated with the current {@link User}
	 *
	 * @return UserProfile
	 * @static
	 */
	function &getCurrentUserProfile() {
		if (!isset($GLOBALS['_CACHE']['UserProfile'])) {
			$me =& Me::getInstance();
			$GLOBALS['loader']->requireOnce('includes/UserProfile.class.php');
			$GLOBALS['_CACHE']['UserProfile'] =& new UserProfile($me->get_user_id());
		}
		
		return $GLOBALS['_CACHE']['UserProfile'];
	}
	
	
	/**
	 * Handle a redirection inside the Celini application
	 *
	 * @see redirectURL()
	 * @param string $controller Controller name
	 * @param string $action Action name
	 * @param array  $queryParamters Query string parameters to send
	 */
	function redirect($controller, $action, $queryParameters = array()) {
		$queryString = '';
		if (count($queryParameters) > 0) {
			$queryString = http_build_query($queryParameters);
		}
		Celini::redirectURL(Celini::link($action, $controller).$queryString);
	}
	
	/**
	 * Handle a redirection to a given URL
	 *
	 * @see redirect()
	 * @param string $url
	 *
	 * @todo Check to see if output has already happened, if it has redirect via JS or <meta> tags
	 * @todo Look into extending headers to contain proper 30* message for redirection
	 */
	function redirectURL($url) {
		header('Location: ' . $url);
		exit;
	}
}
?>
