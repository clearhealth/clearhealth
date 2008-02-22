<?php
/**
* Base page controller
*
* @package	com.clear-health.celini
*/

/**
* Override the default smarty install dir
*/
define("SMARTY_DIR", CELINI_ROOT."/lib/smarty/");

/**#@+
* Basic Classes
*/
$loader->requireOnce('includes/clniView.class.php');
$loader->requireOnce('includes/clniFilter.class.php');
$loader->requireOnce('includes/clniMapper.class.php');
$loader->requireOnce("includes/Me.class.php");
$loader->requireOnce("includes/Messages.class.php");
$loader->requireOnce("/controllers/Manager.class.php");
$loader->requireOnce('/includes/EnforceType.class.php');
/**#@-*/


/**
* Base page controller
* 
*/
class Controller {

	var $_state;
	var $security;
	var $_args = array();
	var $sec_obj;
	var $_me;
	var $messages;
	var $base_dir = "/";
	var $_print_view = false;
	var $autoAcl = true;
	var $_continue_processing = true;
	var $controller_vars = array();
	var $_manager = false;
	var $trail = null;
	var $_mapper = false;
	var $_actionMethodNameCache = false;

	/**
	 * Template name modifier
	 * @deprecated use clniView::$templateType
	 */
	var $template_mod;

	/**
	 * Object that handles displaying the content
	 */
	var $view;

	/**
	 * Wrapper around _GET with basic content filtering
	 */
	var $GET;

	/**
	 * Wrapper around _POST with basic content filtering
	 */
	var $POST;

	/**
	 * Wrapper around _SERVER with basic content filtering
	 */
	var $SERVER;
	
	/**
	 * Contains a reference to a {@link EnforceType} class for sub-classes to
	 * handle validation/enforcing of a specific input type.
	 *
	 * @var object
	 * @access protected
	 */
	var $_enforcer = null;
	
	
	/**
	 * Determines whether or not this controller should render, or just return the template name
	 * that it would normally render.
	 *
	 * @var boolean
	 * @access public
	 */
	var $noRender = false;

	/**
	 * Contains session information.
	 */
	var $session = null;
	

	function Controller() {
		// view setup
		$this->view = new clniView(substr(strtolower(get_class($this)),2));
		
		$this->template_mod = "general";

		$this->_state = true;

		$this->assign("PROCESS", "true");
		$this->assign("HEADER", "<html><head></head><body>");
		$this->assign("FOOTER", "</body></html>");
		$this->assign("CONTROLLER", "controller.php?");
		if (isset($_SERVER['QUERY_STRING'])) {
			$this->assign("CONTROLLER_THIS", Celini::link(true) . $_SERVER['QUERY_STRING']);
		}
		else {
			$this->assign("CONTROLLER_THIS", Celini::link(true));
		}

		$this->assign('FORM_ACTION',Celini::link(true));
		
		if (isset($GLOBALS['config']['nav'])) {
			$this->assign("nav_list",$GLOBALS['config']['nav']);
		}
		if (isset($GLOBALS['frame']['security'])) {
			$this->sec_obj = $GLOBALS['frame']['security'];
			$this->assign_by_ref("sec_obj",$this->sec_obj);
		}
		$this->_me =& Me::getInstance();
		$this->assign_by_ref("me",$this->_me); 

		$this->messages =& Messages::getInstance();
		$this->assign_by_ref("messages",$this->messages); 

		$this->security =& $GLOBALS['security'];


		if ($GLOBALS['config']['dir_style_paths']) {
			$base_dir = Celini::getBaseDir();
			$this->assign('base_dir',$base_dir);
			$this->base_dir = $base_dir;
			$proto = "https://";
			$config = Celini::configInstance();
			$forcehttps = $config->get('forceHTTPS');
			if (!isset($_SERVER['HTTPS']) && !$forcehttps) {
				$proto= "http://";
			}
			$this->view->assign('base_uri',$proto.$_SERVER['SERVER_NAME'].$base_dir);
		}
		$this->assign('entry_file',$GLOBALS['config']['entry_file']);
		if (isset($GLOBALS['config']['autoAcl'])) {
			$this->autoAcl = $GLOBALS['config']['autoAcl'];
		}

		if (isset($GLOBALS['C_ALL'])) {
			foreach($GLOBALS['C_ALL'] as $key => $val) {
				$this->assign($key,$val);
			}
		}

		$this->assign('APP_ROOT',APP_ROOT);
		$this->assign('CELINI_ROOT',CELINI_ROOT);
		
		$this->_load_controller_vars();
		
		// Load enforcer
		$this->_enforcer =& new EnforceType();

		// setup GET,POST,SERVER wrappers
		$this->GET =& Celini::filteredGet();
		$this->POST =& Celini::filteredPost();
		$this->SERVER =& new clniFilter('SERVER');
		
		// assign filtered GET
		$this->view->assign_by_ref('GET', $this->GET);

		$this->_mapper = new clniMapper($this);

		$this->session =& Celini::sessionInstance();

		$this->trail =& Celini::trailInstance();
	}


    /**
     * 
     * Given an array of objects, returns the last value of a property
     * from those objects as the only value of an array key.
     * 
     * <code>
     * // get the last value of $bar from this stack of objects.
     * $objs = array($obj1, $obj2, $obj3);
     * $key = 'foo';
     * $value = 'bar';
     * $array = $this->utility_array($objs, $key, $value);
     * // $array['foo'] is the value of $obj3->bar.
     * </code>
     * 
     */
	function utility_array($objs, $key, $value) {
		$ua = array();
		
		foreach ($objs as $obj) {
			$ua[$obj->$key] = $obj->$value;	
		}
		return $ua;	
	}


	/**
	 * Method that tells if a process exists
	 */
	function exists($process,$mode ='action') {
		$method = $this->_mapper->getMethod($mode,$process);
		if ($method !== false) {
			return true;
		}
		$method = $this->_methodName($mode,$process);
		if (method_exists($this,$method)) {
			return true;
		}
		return false;
	}

	/**
	 * Process and return its results
	 */
	function dispatch($action,$args,$mode = 'action') {
		if ($this->exists($action,$mode)) {
			$method = $this->_methodName($mode,$action);
			return call_user_func_array(array(&$this,$method),$args);
		}
	}

	/**
	 * Get the acl role for an action
	 */
	function aclRole($action,$mode='action') {
		return $this->_mapper->getRole($mode,$action);
	}

	/**
	 * Helper function used to build a method name
	 * Provides bc
	 */
	function _methodName($mode,$action) {
		$method = $this->_mapper->getMethod($mode,$action);

		if ($method !== false) {
			return $method;
		}

		// compat syntax
		if ($mode === 'process') {
			$method = strtolower($action).'_action_process';
		}
		else if ($mode === 'action') {
			$method = strtolower($action).'_action';
			if (!method_exists($this,$method)) {
				// search for a version with a security postfix
				$method = $this->_lookupActionMethodName($method);
			}
		}

		return $method;
	}

	/**
	 * Looks for a method name given a method without a security postfix
	 */
	function _lookupActionMethodName($method) {
		if ($this->_actionMethodNameCache === false) {
			$this->_actionMethodNameCache = array();
			$methods = get_class_methods($this);
			foreach($methods as $m) {
				$key = substr($m,0,strrpos($m,'_'));
				$postfix = substr($m,strrpos($m,'_'));
				if ($postfix !== '_process') {
					$this->_actionMethodNameCache[$key] = $m;
				}
			}
		}
		if (isset($this->_actionMethodNameCache[$method])) {
			return $this->_actionMethodNameCache[$method];
		}
		return false;
	}

	/**
	* Called when an acl_check fails
	*
	* @param action_sec Name of action section used in call
	* @param action_val Name of actions value used in call
	* @param role_sec Name of role section used in call, usually persons
	* @param role_val Name of role value used in call, usually a user name or user id
	* @param resource_sec Name of resource section used in call
	* @param resource_val Name of resource whose access was attempted
	*/
	function permissions_error($action_sec = "",$action_val = "",$role_sec = "", $role_val = "", $resource_sec =  "", $resource_val = "") {
		$this->assign("action_sec", $action_sec);
		$this->assign("action_val", $action_val);
		$this->assign("role_sec", $role_sec);
		$this->assign("role_val", $role_val);
		$this->assign("resource_sec", $resource_sec);
		$this->assign("resource_val", $resource_val);
							       
		$this->view->path = 'error';
		echo $this->view->render('permissions.html');
		exit;
	}

	/**
	 * Assign a variable to the view
	 *
	 * @param array|string $name the template variable name(s)
	 * @param mixed $value the value to assign
	 */
	function assign($name, $value = null) {
		$this->view->assign($name,$value);
	}

	/**
	 * Assign a variable to the view by reference
	 *
	 * @param string $name the template variable name
	 * @param mixed $value the referenced value to assign
	 */
	function assign_by_ref($name, &$value)
	{
		$this->view->assign_by_ref($name,$value);
	}

	/**
	 * Appends variables to the view
	 *
	 * @param array|string $name the template variable name(s)
	 * @param mixed $value the value to append
	 */
	function append($name, $value=null, $merge=false) {
		$this->view->append($name,$value,$merge);
	}

	/**
	 * Clear an already assigned variable form the view
	 *
	 * @param string $name the template variable to clear
	 */
	function clear_assign($name) {
		$this->view->clear_assign($name);
	}

	/**
	 * Get an assign var from the view
	 */
	function &get_template_vars($var=null) {
		return $this->view->get_template_vars($var);
	}
	
	function isAssigned($var) {
		return isset($this->view->_tpl_vars[$var]);
	}

	/**
	* Wrapper around smarty fetch adds some extra error handling
	*/
	function fetch($template) {
		if (is_object($this->_manager)) {
			$this->_manager->postProcess();
		}	
		return $this->view->fetch($template);
	}

	/**
	 * redirect for images that are missing base_dir
	 */
	function images_action($image = false) {
		header('Location: '.$this->base_dir."images/".$image);
	}
	
	/**
	 * load controller vars
	 * this function is a way of handling namespace for session variables and
	 * making them automatically available in the local controller object scope
	 * The variables are also made available via the generic get and set
	 * functions.
	 */
	function _load_controller_vars() {
		$className = strtolower(get_class($this));
		if (isset($_SESSION[$GLOBALS['config']['app_name']]['controller_vars'][$className])) {
			$controller_vars = $_SESSION[$GLOBALS['config']['app_name']]['controller_vars'][$className];
			$this->controller_vars = array_merge($this->controller_vars,$controller_vars);
		}
	}

	/**
	 * The the controller vars for any controller
	 */
	function get_controller_vars($className) {
    $className=strtolower($className);
		if (isset($_SESSION[$GLOBALS['config']['app_name']]['controller_vars'][$className])) {
			return $_SESSION[$GLOBALS['config']['app_name']]['controller_vars'][$className];
		}
	}
	
	function get($name,$controller=true) {
		if ($controller === true) {
			$controller = strtolower(get_class($this));
		}
		else {
			$controller = strtolower($controller);	
		}
		if (isset($_SESSION[$GLOBALS['config']['app_name']]['controller_vars'][$controller][$name])) {
			return $_SESSION[$GLOBALS['config']['app_name']]['controller_vars'][$controller][$name];
		}
	}
	
	function set($name,$value,$controller = true) {
		if ($controller === true) {
			$controller = strtolower(get_class($this));
		}
		else {
			$controller = strtolower($controller);
		}
		$_SESSION[$GLOBALS['config']['app_name']]['controller_vars'][$controller][$name] = $value;
	}

	/**
	 * Support the report action on any controller
	 */
	function actionReport_view($report_id,$template_id=0) {
		$GLOBALS['loader']->requireOnce('includes/ReportAction.class.php');
		$action =& new ReportAction();
		$action->controller =& $this;

		return $action->action($report_id,$template_id);
	}
	
	/**
	 * Check if an action on this method requires the user to be logged in
	 *
	 * This check is only ran if the user isn't currently logged in so don't try to use for a general permission systems use acls
	 *
	 * This default implementation is based off 
	 * 
	 * @param	string	$controller
	 * @param	string	$action
	 * @return	boolean	true - the user needs to be logged into view this action, false the user doesn't need to be logged in
	 */
	function requireLogin($controller, $action) {
		$c = strtolower($controller);
		if (isset($GLOBALS['config']['require_login']) && $GLOBALS['config']['require_login'] == false) {
			
			return false;
		}

		// By pass the checks that are done on the top level controller
		// the user only cares about the 2nd controllers as far as this
		// is concerned
		if($c == 'main' || $c == 'print' || $c == 'pdf' || $c == 'util'){
			return false;
		}
		
		if ($c !== "access") {
			$login = true;
			if (isset($GLOBALS['config']['no_login_for'])) {
				foreach($GLOBALS['config']['no_login_for'] as $mc) {
					if ($c === strtolower($mc)) {
						$login = false;
					}
				}
			}
			return $login;
		}	
		return false;
	}
	
	
	/**
	 * Send headers to tell browser that this display should be downloaded
	 *
	 * @param	string	Type of file
	 * @param	string	Filename
	 */
	function _sendFileDownloadHeaders($mimeType, $name) {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-type: ' . $mimeType);
		header('Content-Disposition: attachment; filename="' . $name . '"');
	}

	/**
	 * Map a default controller variable to a GET variable
	 */
	function mapDefault($to) {
		if (isset($_GET[0])) {
			if (!$this->GET->exists($getParam)) {
				$this->GET->set($to,$_GET[0]);
			}
		}
	}

	/**
	 * Grab a variable either from default if its set or a get var if its set
	 */
	function getDefault($getParam,$defaultValue = null) {
		if ($this->GET->exists($getParam)) {
			return $this->GET->get($getParam);
		}
		if (isset($_GET[0])) {
			return $_GET[0];
		}
		return $defaultValue;
	}
}
?>
