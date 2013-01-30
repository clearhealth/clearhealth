<?php
/*****************************************************************************
*       App.php
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


class WebVista {

	protected static $_instance = null;
    	protected $_paths = array();
	protected $_config;

	public static function getInstance() {
        	if (null === self::$_instance) {
        		self::$_instance = new self();
        	}

		return self::$_instance;
	}

	protected function _computePaths() {
        	$this->_paths['application'] = realpath(dirname(__FILE__) . '/../..');
        	$this->_paths['base'] = realpath(dirname(__FILE__) . '/../../../');
        	$this->_paths['library']     = $this->_paths['application'] . '/library';
        	$this->_paths['models']        = $this->_paths['application'] . '/models';
        	$this->_paths['controllers']        = $this->_paths['application'] . '/controllers';

        	return $this;
	}

	public function run() {
		$this->_setupEnvironment()
			->_setupDb()
			->_setupFrontController()
			->_setupCache()
			->_setupAcl() // this must be called right after setting up cache
			->_setupViews()
			->_setupTranslation()
			->_dispatchFrontController();
		return $this;
	}
	
	protected function _setupEnvironment() {
		error_reporting(E_ALL | E_STRICT);
		set_include_path($this->getPath('library') . PATH_SEPARATOR 
					. $this->getPath('models') . PATH_SEPARATOR
					. $this->getPath('controllers') . PATH_SEPARATOR
					. get_include_path());
		require_once('WebVista/Model/ORM.php');
		require_once('User.php');
		require_once('Person.php');
		require_once('Zend/Session.php');
		require_once('WebVista/Session/SaveHandler.php');
		Zend_Session::setSaveHandler(new WebVista_Session_SaveHandler());
		Zend_Session::start();
		require_once 'Zend/Loader.php';
		Zend_Loader::registerAutoLoad();
		$sessionTimeout = ini_get('session.gc_maxlifetime') - (5 * 60);
		Zend_Registry::set('sessionTimeout',$sessionTimeout);
		$this->_config = new Zend_Config_Ini($this->getPath('application') . "/config/app.ini", APPLICATION_ENVIRONMENT);
		Zend_Registry::set('config', $this->_config);
		Zend_Registry::set('baseUrl',substr($_SERVER['PHP_SELF'],0,strpos(strtolower($_SERVER['PHP_SELF']),'index.php')));
		Zend_Registry::set('basePath',$this->getPath('base') . DIRECTORY_SEPARATOR);
		try {
			date_default_timezone_set(Zend_Registry::get('config')->date->timezone);
		}
		catch (Zend_Exception $e) {
			die($e->getMessage());
		}

		AuditLog::setDbConfig($this->_config->database->toArray()); // this MUST be required as this is used as DB connection
		// register shutdown function
		register_shutdown_function(array('AuditLog','closeConnection'));
		ob_start(); // this MUST be required after register shutdown

		return $this;	
	}
		
	protected function _setupAutoloader() {
		return $this;
	}

	protected function _loadConfig() {
		return $this;
	}
	
	protected function _setupDb() {
		try {
			$dbConfig = Zend_Registry::get('config')->database;
			//$dbConfig->driver_options = array();
			//$dbConfig->driver_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_WARNING;
	
			$dbAdapter = Zend_Db::factory($dbConfig);
			$dbAdapter->query("SET NAMES 'utf8'");
		}
		catch (Zend_Exception $e) {
			die ($e->getMessage());
		}
		Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
		Zend_Registry::set('dbAdapter',$dbAdapter);
		/*$res = $dbAdapter->query("SHOW VARIABLES LIKE 'c%';");
		foreach($res->fetchAll() as $row) {
			//var_dump($row);
			echo $row['Variable_name'] . " :: " . $row['Value'] . "<br />";
		}
		exit;*/
		return $this;
	}

	protected function _setupFrontController() {
		Zend_Controller_Front::getInstance()
			->throwExceptions(true)
			->setBaseUrl(Zend_Registry::get('baseUrl'))
			->setControllerDirectory($this->getPath('application') . "/controllers")
			->registerPlugin(new WebVista_Controller_Plugin_Dispatch_LayoutContext())
			->registerPlugin(new WebVista_Controller_Plugin_Dispatch_CheckAuth())
			->registerPlugin(new WebVista_Controller_Plugin_Dispatch_Check())
			->returnResponse(true);
			Zend_Layout::startMvc();

			return $this;


	}

	protected function _setupViews() {
		$view = Zend_Layout::getMvcInstance()->getView();
		Zend_Dojo::enableView($view);
		return $this;
	}

	protected function _dispatchFrontController() {
		try {
			try {
			Zend_Controller_Front::getInstance()
				->dispatch()
				->sendResponse();

			}
			catch (WebVista_App_AuthException $wae) {
                        	try {
					$loginRequest = new Zend_Controller_Request_Http();
					$loginRequest->setControllerName('login')
						->setActionName('index');
					$view = Zend_Layout::getMvcInstance()->getView();
					$view->errorMessage = $wae->getMessage();
					Zend_Controller_Front::getInstance()->getRouter()->removeDefaultRoutes();
					Zend_Controller_Front::getInstance()->getRouter()->addRoute('default', new Zend_Controller_Router_Route('login/:index'));
                        		Zend_Controller_Front::getInstance()
                                		->setRequest($loginRequest)
						->dispatch()
						->sendResponse();
                        	}
                        	catch (WebVista_App_AuthException $wae2) {
					//do nothing, we are directing to login controller so this exception is expected
                        	}
                	}
		}
		catch (Exception $e) {
			Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(500);
			$uniqErrCode = uniqid();
			echo "There was an error processing your request, it is error number '$uniqErrCode' , contact your adminsitrator for details";
			trigger_error("Exception error ($uniqErrCode): " . $e->getMessage(), E_USER_NOTICE);
			
		}
		return $this;
	}

	public function getPath($key) {
        	if (isset($this->_paths[$key])) {
        	    return $this->_paths[$key];
        	} else {
        	    return null;
        	}
	}

	private function __construct() {
		$this->_computePaths();
	}

	private function __clone() {}

	public static function getORMFields($className) {
                $class = new ReflectionClass($className);
                $properties = $class->getProperties();
                $fields = array();
                foreach ($properties as $property) {
                        if (substr($property->name,0,1) == "_") continue;
                        $fields[] = $property->name;
                }
                return $fields;
        }

    protected function _setupCache() {
	/*
        $frontendOptions = array('lifetime' => 3600, 'automatic_serialization' => true);
        $backendOptions = array('servers' => array('host' => '127.0.0.1', 'port' => 11211, 'persistent' => true));
        $cache = Zend_Cache::factory('Core', 'Memcached', $frontendOptions, $backendOptions);
        Zend_Registry::set('memcache', $cache);
	*/

        $frontendOptions = array('lifetime' => 3600, 'automatic_serialization' => true);
        $backendOptions = array('file_name_prefix' => 'clearhealth', 'hashed_directory_level' => 1, 'cache_dir' => '/tmp/', 'hashed_directory_umask' => 0700);
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        Zend_Registry::set('cache', $cache);

	$memcache = new Memcache();
	$memcache->connect('127.0.0.1',11211);
	$status = $memcache->getServerStatus('127.0.0.1',11211);
	if ($status === 0) {
		// memcache server failed, do error trapping?
	}
	Zend_Registry::set('memcache', $memcache);
	Zend_Registry::set('memcacheStatus', $status);

        return $this;
    }

	protected function _setupTranslation() {
		//$cache = Zend_Registry::get('cache');	
		//Zend_Translate::setCache($cache);
		//require_once('spanish.php');
		//$translate = new Zend_Translate('array',$spanish,'es');
		//Zend_Registry::set('translate',$translate);
		return $this;
	}

	protected function _setupAcl() {
		//$aclBuilder = new WebVista_AclBuilder();
		$memcache = Zend_Registry::get('memcache');

		$key = 'acl';
		$acl = $memcache->get($key);
		if ($acl === false) {
			$acl = WebVista_Acl::getInstance();
			// populate acl from db
			$acl->populate();
			// save to memcache
			$memcache->set($key,$acl);
		}
		Zend_Registry::set('acl',$acl);
		return $this;
	}

	public static function debug($msg,$level = E_USER_NOTICE) {
		$config = Zend_Registry::get('config');
		if (strtolower($config->error->logDebugMessages) == 'true') {
			if (is_object($msg) || is_array($msg)) {
				$msg = print_r($msg,true);
			}
			switch ($level) { // level only works for E_USER family
				case E_USER_ERROR:
				case E_USER_WARNING:
				case E_USER_NOTICE:
					break;
				case E_ERROR:
					$level = E_USER_ERROR;
					break;
				case E_WARNING:
					$level = E_USER_WARNING;
					break;
				case E_NOTICE:
				default:
					$level = E_USER_NOTICE;
			}
			trigger_error($msg,$level);
		}
	}

	public static function log($msg,$level = E_USER_NOTICE) {
		$client = '127.0.0.1';
		if (isset($_SERVER['REMOTE_ADDR'])) {
			$client = $_SERVER['REMOTE_ADDR'];
		}
		self::debug('['.date('D M d H:i:s Y').'] [client '.$client.'] '.$msg,$level); // temporarily set as alias for debug
	}

}

