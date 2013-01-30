#!/usr/bin/php
<?php
/*****************************************************************************
*       Service.php
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

if (!isset($argv[1])) {
	die("Invalid arguments.\n");
}

function __($str) {
	return $str;
}

function calcTS() {
        list($usec, $sec) = explode(" ", microtime());
        $ts = ((float)$usec + (float)$sec);
        if (!isset($GLOBALS['gts'])) $GLOBALS['gts'] = $ts;
        return $ts-$GLOBALS['gts'];
}

define('APPLICATION_ENVIRONMENT','production');
$appFile = realpath(dirname(__FILE__) . '/../application/library/WebVista/App.php');
require_once $appFile;

class ScriptService extends WebVista {

	public static function getInstance() {
        	if (null === self::$_instance) {
        		self::$_instance = new self();
			self::$_instance->_init();
        	}
		return self::$_instance;
	}

	protected function _init() {
		$this->_setupEnvironment()
			->_setupDb()
			->_setupCache()
			->_setupTranslation();
		return $this;
	}

	private function __construct() {
		$this->_computePaths();
	}

	private function __clone() {}

	protected function _setupEnvironment() {
		parent::_setupEnvironment();
		// disable strict reporting
		error_reporting(E_ALL);
		return $this;
	}

	public function NSDRStart() {
		return NSDR2::systemStart();
	}

	public function NSDRReload() {
		return NSDR2::systemReload();
	}

	public function NSDRUnload() {
		return NSDR2::systemUnload();
	}

	public function permissionStart() {
		return PermissionTemplate::serviceStart();
	}

	public function permissionReload() {
		return PermissionTemplate::serviceReload();
	}

	public function permissionUnload() {
		return PermissionTemplate::serviceStop();
	}

	public function pharmacyDownload($args) {
		$daily = 0; // default to full sync
		if (isset($args[0])) $daily = (int)$args[0];
		$ret = Pharmacy::activateDownload($daily);
		if (strlen($ret['error']) > 0) { // error
			echo $ret['error'];
			return false;
		}
		$filename = Pharmacy::downloadPharmacy($ret['downloadUrl'],$ret['cookieFile']);
		$counter = Pharmacy::loadPharmacy($filename);
		return true;
	}

}

set_error_handler(create_function('$errno,$errstr','return true;'));
$script = ScriptService::getInstance();
$methodName = trim($argv[1]);
if (method_exists($script,$methodName)) {
	echo 'Processing '.$methodName.'... ';
	$script->$methodName(array_slice($argv,2));
	Service::getServices(); // set nominal service
	echo 'done.'.PHP_EOL;
}
else {
	die("Argument {$methodName} is invalid.\n");
}

