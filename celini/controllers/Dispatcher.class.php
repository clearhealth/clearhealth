<?php
$loader->requireOnce('/includes/ControllerFileLoader.class.php');
$loader->requireOnce('/controllers/Controller.class.php');
/**
 * Handles dispatching events to controllers
 *
 * @package	com.clear-health.celini
 */
class Dispatcher {
	var $stringOutput = true;

	var $_controllers = array();

	// make $_controllers a static, this is a bit of a hack
	function Dispatcher() {
		if (!isset($GLOBALS['_Dispatcher']['controllers'])) {
			$GLOBALS['_Dispatcher']['controllers'] = array();
		}
		$this->_controllers =& $GLOBALS['_Dispatcher']['controllers'];
	}

	function &controllers() {
		return $GLOBALS['_Dispatcher']['controllers'];
	}

	/**
	* Check _GET and _POST, cleaning:
	*
	* in _POST all keys are renamed to only contain: /[^A-Za-z0-9_]/
	*
	* in _GET action is replaced to only contain: 
	*
	* @todo	how do we specify other indexes to be cleaned
	*/
	function check_input() {
		// clean post
		foreach($_POST as $key => $value)
		{
			if (!preg_match("/^[A-Za-z0-9_]+$/",$key))
			{
				$_POST[preg_replace("/[^A-Za-z0-9_]/","",$key)] = $value;
				unset($_POST[$key]);
			}
		}

		// clean controller get vars
		foreach($_GET as $key => $value)
		{
			if (!preg_match("/^[A-Za-z0-9_]+$/",$key))
			{
				$_POST[preg_replace("/[^A-Za-z0-9_]/","",$key)] = $value;
				unset($_GET[$key]);
			}
		}

	}

	/**
	 * Dispatch an event from a DispatcherAction
	 */
	function dispatch($action) {
		return $this->act($action->toArray());
	}

	/**
	 * These method was pulled out of the base controller, it still needs to be updated at some point, but the overall processes didn't change at all
	 * Main actor of the controller
	 *
	 * 
	 * creates the me object:
	 * the Me object is used by controllers to run permissions checks
	 * Default me object is basically an anonymous user with the reserved id of 0
	 *
	 * Runs a basic security check on me using the security object:
	 * Should "me" be allowed to access any actions whatsoever.
	 * This will likely return to be true except when using client side certs or where absolutely
	 * no anonymous access if allowed. I don't know how you could elimintate anonymous access when
	 * not using client side certs and still be able to login though?
	 *
	 * Additional layers of security should be handled by the controllers and actions themselves.
	 *
	 * The controller and the action is then selected using $qarray, the controller defauls to Access if none is specified
	 * Usually this will be a some sort of access controller with an ability to log in and set new
	 * {@link Me} session objects.
	 *
	 * action defaults to "default"
	 *
	 * @param	$qarray	Query String array, usually _GET, if using _GET make sure to call check_input first
	 */
	function act($qarray,$assign=array()) {
		if (isset($_GET['process'])){
			$_POST['process'] = $_GET['process'];

			if ($_POST['process'] == 1) {
				$_POST['process'] = 'true';
			}
			unset($_GET['process']);
			unset($qarray['process']);
		}
		//remove grid vars from get so not to screw up mapping of get vars to controller action arguments
		foreach ($_GET as $key => $getvar) {
		  if ($key === 0) continue;
		  switch($key) { 
			case 'PAGER_PAGE':
			case 'GRID_MODE':
			case 'GRID':
			case 'ORDER':
			case 'ORDER[direction]':
			case 'ORDER[order]':
			case 'ORDER[column]':
			case 'MOVE':
			$GLOBALS[$key] = $_GET[$key];
			unset($_GET[$key]);
			unset($qarray[$key]);
			break;
		  }
		}
		$tmp = $qarray;

		// bootstrap me
		$me =& me::getInstance();

		// get the controller name
		$args = array_reverse($qarray);
		$c_name = preg_replace("/[^A-Za-z0-9_]/","",array_pop($args));
		//var_dump($args);
		$parts = split("_",$c_name);
		$name = "";

		foreach($parts as $p) {
			$name .= ucfirst($p);
		}

		$c_name = $name;
		
		if (empty($c_name)) {
			$c_name = "Access";
		}
		
		// get the action name
		$c_action = preg_replace("/[^A-Za-z0-9_]/","",array_pop($args));

		$obj_name = "C_" . $c_name;
		$c_obj =& $this->controllerFactory($c_name);

		foreach($assign as $key => $val) {
			$c_obj->assign($key,$val);
		}

		if (empty ($c_action)) {
			$c_action = "default";
		}

		// if the user isn't logged in run the requireLogin method on the target controller to see if login is required to view this page
		if (!$me->isLoggedIn()) {
			if ($c_obj->requireLogin($c_name,$c_action)) {
				return $this->act(array('Access'=>'','login'=>''));	
			}
		}

		$manager =& Manager::factory($c_name);
		if ($manager->isValid()) {
			$manager->setController($c_obj);
			$c_obj->_manager =& $manager;
			$manager->preProcess();
			$tmp = $args;
			$possible_argument = preg_replace("/[^A-Za-z0-9_]/","",array_pop($tmp));
			if ($manager->exists($possible_argument)) {
				$manager_argument = $possible_argument;
				array_pop($args);
			}
			else {
				$manager_argument = false;
			}
		}
		$args = array_reverse($args);

		// apply automatic acls and get the method postfix
		$c_action_postfix = $this->autoAcl($c_obj,strtolower($c_name),strtolower($c_action));

		if (isset($_GET['set_print_view'])) {
			$c_obj->_print_view = true;	
		}
		
		if ($this->stringOutput) {
			$output = "";
		}
		else {
			$output = array();
		}
		//print_r($args_array);

		if (isset($_POST['process']) && strtolower($_POST['process']) === 'generic') {
			$this->processPost();
		}
		else {
			if (isset($_POST['process']) && $_POST['process'] == "true") {
				if ($manager->isValid() && $manager_argument) {
					$this->aclCheck($c_obj,$c_name,$manager_argument,$manager->aclRole($manager_argument,'process'));
					$output .= $manager->dispatch($manager_argument,$args,'process');
				}
				else if ($c_obj->exists($c_action,'process')) {
					$this->aclCheck($c_obj,$c_name,$c_action,$c_obj->aclRole($c_action,'process'));
					$output .= $c_obj->dispatch($c_action,$args,'process');
					
					/* 
					 * If Controller::$_state == false, we stop at this process and
					 * use whatever output was generated by that method.  See
					 */
					if ($c_obj->_state == false) {
						$this->_continue_processing = $c_obj->_continue_processing;
						return $output;
					}
				}
			}
		}
		
		if ($c_obj->exists($c_action)) {
			$this->aclCheck($c_obj,$c_name,$c_action,$c_obj->aclRole($c_action,'action'));
			if ($this->stringOutput) {
				$output .= $c_obj->dispatch($c_action,$args);
			}
			else {
				$output = $c_obj->dispatch($c_action,$args);
			}
		}
		else if ($manager->isValid() && $manager->exists($c_action,'action')) {
			$this->aclCheck($c_obj,$c_name,$c_action,$manager->aclRole($c_action,'action'));
			$output .= $manager->dispatch($c_action,$args,'action');
		} else {
			header('HTTP/1.1 404 Not Found', true, 404);
			echo "<h1>Not found</h1>";
			echo "<p>{$c_action} action does not exist</p>";
			exit;
			//Celini::raiseError("The action trying to be performed: " . $c_action ." does not exist controller: ". $name);
		}
		$this->_continue_processing = $c_obj->_continue_processing;

		return $output;
	}

	/**
  	 * Figure out if the action were calling has a action postfix
	 * if so do an acl check
	 *
	 * As far as i can tell this method isn't really doing anything anymore since it won't match any of the new syntax methods
	 * check out the aclCheck method
	 */
	function autoAcl($controller,$controller_name,$action_name)
	{
		if ($controller_name == "main") {
			return "";
		}
		if ($GLOBALS['config']['debug']) {
			$username = $this->_me->get_username();
			echo "Debug: Auto Acl check for: Controller=$controller_name, Action=$action_name, User=$username<br>\n";
		}

		$methods =  get_class_methods(get_class($controller));
		if (in_array($action_name.'_action',$methods)) {
			// no postfix so no auto acl check
			if ($GLOBALS['config']['debug']) {
				echo "&nbsp;&nbsp;&nbsp;&nbsp; No postfix, no acl call<br>\n";
			}
			return "";
		}
		$method = null;
		foreach($methods as $methodName)
		{
			if (strstr($methodName,$action_name.'_action'))
			{
				$method = $methodName;
				break;
			}
		}
		if (is_null($method)) {
			return "";
		}

		// grab the postfix
		$exploded = explode('_', $method);
		$postfix = array_pop($exploded);

		if ($postfix == "process") {
			return "";
		}

		if ($controller->autoAcl === false) {
			return '_'.$postfix;
		}

		if ($controller->_me->get_id() == 0 && $controller_name == 'access' && 
			($action_name == 'login' || $action_name == 'logout' || $action_name == 'default')) 
		{
			// were not logged in yet
			return '_'.$postfix; 
		}

		if ($GLOBALS['config']['debug']) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp; Security call ACO = $postfix, ARO = ".$this->_me->get_username().", AXO = $controller_name<br>\n";
		}
		$controller->security->acl_qcheck($postfix,$controller->_me,'resources',$controller_name,$this,false);
		if ($GLOBALS['config']['debug']) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp; Security called passed<br>\n";
		}
		return '_'.$postfix;
	}


	function trail_build($stop) {

		$otrail =& Celini::trailInstance();
		$otrail->addCurrentPage();
	}

	function processPost() {
		if(isset($GLOBALS['dispatcherProcessPost'])){
			return false;
		}
		$GLOBALS['dispatcherProcessPost'] = TRUE;
		// get the keys
		$targets = array_keys($_POST);
		foreach($targets as $target) {
			$this->_dispatchTarget($target,$_POST[$target]);
		}
		return true;
	}

	/**
	 * Add a security check, have to have the edit permission to run process
	 */
	function _dispatchTarget($targetClass,$payload) {
		if ($this->controllerExists($targetClass)) {
			$this->_controllers[$targetClass] =& $this->controllerFactory($targetClass);

			if (is_callable(array(&$this->_controllers[$targetClass],'process'))) {
				$this->aclCheck($this->_controllers[$targetClass],$targetClass,'process','edit');
				$this->_controllers[$targetClass]->process($payload);
				return true;
			}
		}

		// check for a manager
		$manager =& Manager::factory($targetClass);
		if ($manager->isValid()) {
			if (is_callable(array(&$manager,'process'))) {
				$this->aclCheck($this->_controllers[$targetClass],$targetClass,'process','edit');
				$manager->process($payload);
				return true;
			}
		}

		return false;
	}

	function &controllerFactory($name) {
		if (isset($this->_controllers[$name])) {
			return $this->_controllers[$name];
		}

		// load the controller
		$loader = new ControllerFileLoader();
		if (!$loader->loadController($name)) {
			Celini::raiseError("Unable to load: $name"); 
		}
		$obj_name = "C_" . $name;

		$this->_controllers[$name] =& new $obj_name();
		return $this->_controllers[$name];
	}

	function controllerExists($name) {
		if (class_exists("C_$name")) {
			return true;
		}
		$loader = new ControllerFileLoader();
		if ($loader->loadController($name)) {
			return true;
		}
		return false;
	}

	/**
	 * Check a controller action or process method against its matching acls
	 *
	 * @param Controller	$controller
	 * @param string	$controller_name
	 * @param string	$action
	 * @param string	$role
	 */
	function aclCheck($controller,$controller_name,$action,$role) {
		if ( ($GLOBALS['config']['require_login'] === false) || ($role === false) ) {
			return true;
		}
		$config=& Celini::ConfigInstance();
		if(!$config->get('autoAcl',true)){
			return true;
		}
		$controller_name = strtolower($controller_name);
		$action = strtolower($action);

		if ($GLOBALS['config']['debug']) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp; Security call ACO = $role, ARO = ".$this->_me->get_username().", AXO = $controller_name<br>\n";
		}
		return $controller->security->acl_qcheck($role,$controller->_me,'resources',$controller_name,$this,false);
		if ($GLOBALS['config']['debug']) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp; Security called passed<br>\n";
		}
	}
}

class DispatcherAction {
	var $wrapper = 'main';
	var $controller;
	var $action;
	var $managerAction = false;
	var $defaultValue = false;
	var $get = array();
	
	function toArray() {
		$ret = array();
		if ($this->wrapper) {
			$ret[$this->wrapper] = $this->wrapper;
		}
		$ret[$this->controller] = $this->controller;
		$ret['action'] = $this->action;
		if ($this->managerAction) {
			$ret[$this->managerAction] = $this->managerAction;
		}
		if ($this->defaultValue) {
			$ret[$this->defaultValue] = $this->defaultValue;
		}
		return array_merge($ret,$this->get);
	}
}
?>
