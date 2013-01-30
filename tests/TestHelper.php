<?php
/*****************************************************************************
*       TestHelper.php
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

/*
 * Start output buffering
 */
ob_start();

if (!function_exists('__')) {
	function __($str) {
		return $str;
	}
}

/*
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

/*
 * Set error reporting to the level to which Zend Framework code must comply.
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$chRoot = realpath(dirname(__FILE__) . '/../');
$chAppPath = "$chRoot/application";
$chLibraryPath = "$chAppPath/library";
$chModelsPath = "$chAppPath/models";
$chTestsPath = "$chRoot/tests";

$path = array($chAppPath,$chLibraryPath,$chModelsPath,$chTestsPath,get_include_path());
set_include_path(implode(PATH_SEPARATOR, $path));

if (is_readable($chTestsPath . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
	require_once $chTestsPath . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
}
else {
	require_once $chTestsPath . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
}

/**
 * WebVista
 */
require_once 'WebVista/App.php';

/**
 * Zend_Loader
 */
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoLoad();

$frontendOptions = array('lifetime' => 3600, 'automatic_serialization' => true);
$backendOptions = array('file_name_prefix' => 'clearhealth', 'hashed_directory_level' => 1, 'cache_dir' => '/tmp/', 'hashed_directory_umask' => 0700);
$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
Zend_Registry::set('cache', $cache);

$cache = new Memcache();
$cache->connect('127.0.0.1',11211);
$status = $cache->getServerStatus('127.0.0.1',11211);
if ($status === 0) {
	// memcache server failed, do error trapping?
}
Zend_Registry::set('memcache', $cache);

$config = new Zend_Config_Ini($chAppPath . "/config/app.ini", TESTS_APPLICATION_ENVIRONMENT);
Zend_Registry::set('config', $config);

try {
	$dbAdapter = Zend_Db::factory(Zend_Registry::get('config')->database);
	$dbAdapter->query("SET NAMES 'utf8'");
}
catch (Zend_Exception $e) {
	die ($e->getMessage());
}
Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
Zend_Registry::set('dbAdapter',$dbAdapter);


unset($chRoot,$chAppPath,$chLibraryPath,$chModelsPath,$chTestsPath,$path);
