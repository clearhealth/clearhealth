<?php

$config = array();
$config['debug'] = false;
$config['db_type'] = "mysql";
$config['db_host'] = "localhost";
$config['db_user'] = "root";
$config['db_password'] = "";
$config['db_name'] = "`clearhealth`";
$config['db_prefix'] = "";
$config['db_table_prefix'] = "";
$config['openemr_db'] = "openemr";

$gacl_options = array(
						'debug' => $config['debug'],
						'items_per_page' => 100,
						'max_select_box_items' => 100,
						'max_search_return_items' => 200,
						'db_type' => $config['db_type'],
						'db_host' => $config['db_host'],
						'db_user' => $config['db_user'],
						'db_password' => $config['db_password'],
						'db_name' => $config['db_name'],
						'db_table_prefix' => $config['db_table_prefix'],
						'caching' => FALSE,
						'force_cache_expire' => TRUE,
						'cache_dir' => '/tmp/phpgacl_cache',
						'cache_expire_time' => 600
					);							

$config['gacl'] = $gacl_options;
$config['smarty']['smarty_dir'] = dirname(__FILE__) . "/../tmp";

require_once(dirname(__FILE__) . "/adodb/adodb.inc.php");

$db = NewADOConnection("mysql");
$db->PConnect($config['db_host'], $config['db_user'], $config['db_password'], str_replace("`","",$config['db_name']));

$config['adodb']['db'] = $db;
$config['adodb']['dbh'] = $db->_connectionID;
$GLOBALS['frame']['adodb']['link'] = $db->_connectionID;

$config['template_dir'] = realpath(dirname(__FILE__) . "/../templates")."/";
$template_dir = $config['template_dir'];
$config['template_c_dir'] = realpath(dirname(__FILE__) . "/../tmp");

define('CALENDAR_FIRST_DAY_OF_WEEK',0);

require_once("Security.class.php");
$security = new Security($gacl_options);
                                                                                
//Setup global config options, unset db setting for security
unset($config['db_user']);
unset($config['db_password']);
unset($security->_db_user);
unset($security->_db_password);
//unset($security->db->user);
//unset($security->db->password);
unset($db->user);
unset($db->password);
                                                                                
$GLOBALS['frame']['adodb']['db'] = $db;

$GLOBALS['frame']['security'] = &$security;

$GLOBALS['config'] = $config;
$GLOBALS['frame']['config'] = $config;

?>
