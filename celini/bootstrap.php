<?php
/**
* Sets up the basic environment needed for things to run
*/


/**
* Base Celini dir
*/
if (!defined('CELINI_ROOT')) {
	define('CELINI_ROOT',dirname(__FILE__));
}

/**
* Base application dir
*/
if (!defined('APP_ROOT')) {
	define('APP_ROOT',realpath(CELINI_ROOT."/../"));
}

/**
* Base module dir
*/
if (!defined('MODULE_ROOT')) {
	define('MODULE_ROOT',realpath(APP_ROOT."/modules"));
}

/**
* Base web path
*/
global $config;
$config = array();
if (!isset($config['entry_file'])) {
	$config['entry_file'] = basename($_SERVER['SCRIPT_NAME']);
}
//$config['entry_file'] = $_SERVER['SCRIPT_NAME'];
$wr = substr($_SERVER['PHP_SELF'],0,strpos(strtolower($_SERVER['PHP_SELF']),$config['entry_file']));
if ($wr == "") $wr = "/"; 
define('WEB_ROOT',$wr);

// add local pear install to include path
ini_set('include_path',".".PATH_SEPARATOR.CELINI_ROOT."/lib/PEAR");

// Check for register globals
if(function_exists('ini_get')){
	if(ini_get('register_globals') > 0){
		die("register_globals must be set to off in your php.ini or htaccess file!");	
	}
}

if (file_exists(APP_ROOT."/local/config.defaults.php")) {
	require_once APP_ROOT."/local/config.defaults.php";
}
if (file_exists(APP_ROOT."/local/config.php") && filesize(APP_ROOT."/local/config.php") >  0) {
	require_once APP_ROOT."/local/config.php";
}else{
	// See if the installer is around
	$installer_path = realpath(dirname(__FILE__).'/../installer/');
	if($installer_path && is_dir($installer_path)){
		$url = WEB_ROOT.'installer/';
		header("Location: ".$url."\r\n");
		exit();
	} else {
		die("Neither application config nor installer found.  Please see documentation to set up your application.");
	}
}

require_once CELINI_ROOT."/config.php";

if (isset($GLOBALS['config']['maintenanceMode']) && $GLOBALS['config']['maintenanceMode'] === true) {
	$f = APP_ROOT . "/user/maintenance.tpl.html";
  if (file_exists($f)) {
	require_once($f);
	exit;	
  }

}



require_once CELINI_ROOT."/includes/FileLoader.class.php";
require_once CELINI_ROOT."/includes/FileFinder.class.php";
$finder =& new FileFinder();
$finder->initCeliniPaths('/local');
$GLOBALS['loader'] = new FileLoader($finder);

// Load and initialize Security object
$loader->requireOnce('includes/Security.class.php');
$security = new Security($gacl_options);
                                                                                
//Setup global config options, unset db setting for security
if(!isset($GLOBALS['no_bootstrap_unsets'])) {
unset($GLOBALS['config']['db_user']);
unset($GLOBALS['config']['db_password']);
unset($security->_db_user);
unset($security->_db_password);
}
if (isset($security->db)) {
	unset($security->db->user);
	unset($security->db->password);
}
$GLOBALS['frame']['security'] =& $security;


// objects stored in a session have to be included before its started
$loader->requireOnce("includes/Me.class.php");
$loader->requireOnce("includes/PreferenceTree.class.php");
$loader->requireOnce('includes/clni/clniTrail.class.php');

if (file_exists(APP_ROOT."/local/bootstrap.php")) {
	require_once APP_ROOT."/local/bootstrap.php";
}

if (!session_id()) {
	if (isset($_GET['session_id'])) {
		session_id($_GET['session_id']);
	}

	if (isset($config['application_name'])) {
		$config['app_name'] = $config['application_name'];
	}
	if (isset($config['app_name'])) {
		session_name($config['app_name']);
	}
	session_start();
}

if (file_exists(APP_ROOT."/local/postsession.php")) {
	require_once APP_ROOT."/local/postsession.php";
}
if (file_exists(APP_ROOT."/local/post_bootstrap.php")) {
	require_once APP_ROOT."/local/post_bootstrap.php";
}

// load module post_bootstrap
if(isset($config['module_paths'])) {
	foreach($config['module_paths'] as $path) {
		if (file_exists($path."/local/post_bootstrap.php")) {
			require_once $path."/local/post_bootstrap.php";
		}
	}
}

// make sure some basic classes always exist
$loader->requireOnce('controllers/Controller.class.php');
?>
