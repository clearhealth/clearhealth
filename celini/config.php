<?php
// setup error reporting levels
error_reporting(E_ALL);

require_once CELINI_ROOT."/includes/Celini.class.php";

/*
// settings that should be set in the app config file
$GLOBALS['config']['debug'] = false;
$GLOBALS['config']['db_type'] = "mysql";
$GLOBALS['config']['db_host'] = "localhost";
$GLOBALS['config']['db_user'] = "root";
$GLOBALS['config']['db_password'] = "root";
$GLOBALS['config']['db_name'] = "hnr-erp";
$GLOBALS['config']['db_table_prefix'] = "";
*/

// defaults values that are used if app doesn't configure
if (!isset($GLOBALS['config']['use_storage'])) {
	$GLOBALS['config']['use_storage'] = true;
}
if (!isset($GLOBALS['config']['use_text_storage'])) {
	$GLOBALS['config']['use_text_storage'] = false;
}
if (!isset($GLOBALS['config']['dir_style_paths'])) {
	$GLOBALS['config']['dir_style_paths'] = true;
}
if (!isset($GLOBALS['config']['translate'])) {
	$GLOBALS['config']['translate'] = array();
}
if (!isset($GLOBALS['config']['htmldoc'])) {
	$GLOBALS['config']['htmldoc'] = "/usr/bin/htmldoc";
}
if (!isset($GLOBALS['config']['menu']['attachArrays'])) {
	$GLOBALS['config']['menu']['attachArrays'] = true;
}



// other junk
$gacl_options = array(
			'debug' => false,
			'items_per_page' => 100,
			'max_select_box_items' => 100,
			'max_search_return_items' => 200,
			'db_type' => $GLOBALS['config']['db_type'],
			'db_host' => $GLOBALS['config']['db_host'],
			'db_user' => $GLOBALS['config']['db_user'],
			'db_password' => $GLOBALS['config']['db_password'],
			'db_name' => $GLOBALS['config']['db_name'],
			'db_table_prefix' => 'gacl_',
			'caching' => FALSE,
			'force_cache_expire' => TRUE,
			'cache_dir' => '/tmp/phpgacl_cache',
			'cache_expire_time' => 600
		);							

$GLOBALS['config']['gacl'] = $gacl_options;
$GLOBALS['config']['smarty']['smarty_dir'] = APP_ROOT."/tmp";

if (!defined('ADODB_DIR')) {
	define('ADODB_DIR',CELINI_ROOT.'/lib/adodb');
	require_once CELINI_ROOT . "/lib/adodb/adodb.inc.php";
}

$db = NewADOConnection("mysql");
if (!$db->Connect($GLOBALS['config']['db_host'], $GLOBALS['config']['db_user'], $GLOBALS['config']['db_password'], $GLOBALS['config']['db_name'])) {
	echo $db->errorMsg();
	exit;
}
$db->SetFetchMode(ADODB_FETCH_ASSOC);

$GLOBALS['config']['adodb']['db'] = $db;
$GLOBALS['config']['adodb']['dbh'] = $db->_connectionID;
$GLOBALS['frame']['adodb']['link'] = $db->_connectionID;

$GLOBALS['config']['template_dir'] = APP_ROOT."/local/templates";
$GLOBALS['config']['template_c_dir'] = APP_ROOT."/tmp";
$GLOBALS['config']['template_secure_dir'] = array(APP_ROOT."/local/templates",CELINI_ROOT."/templates");
$GLOBALS['config']['smarty_security_settings'] = array(
                                    'PHP_HANDLING'    => false,
                                    'IF_FUNCS'        => array('array', 'list',
                                                               'isset', 'empty',
                                                               'count', 'sizeof',
                                                               'in_array', 'is_array',
                                                               'true','false',
							       'strstr'
						       ),
                                    'INCLUDE_ANY'     => false,
                                    'PHP_TAGS'        => false,
                                    'MODIFIER_FUNCS'  => array('count'),
                                    'ALLOW_CONSTANTS'  => false
                                   );


define('CALENDAR_FIRST_DAY_OF_WEEK',0);

unset($db->user);
unset($db->password);
                                                                                
if (!isset($config['app_name'])) {
	$config['app_name'] = "celini";
}
                                                                                
$GLOBALS['frame']['adodb']['db'] =& $db;

$GLOBALS['frame']['config'] = $config;

// Turn assert()s off by default
assert_options(ASSERT_ACTIVE, 0);

require_once CELINI_ROOT.'/includes/clniConfig.class.php';
$GLOBALS['configObj'] = new clniConfig($GLOBALS['config']);
?>
