<?php
/*
 * Base Installer include file 
 *
 */
 
define('INSTALLER_PATH', realpath(dirname(__FILE__)));
define('INSTALLER_API', true);

require_once(INSTALLER_PATH.'/includes/InstallerConfig.php');
require_once(INSTALLER_PATH.'/includes/InstallerEngine.php');
require_once(INSTALLER_PATH.'/includes/InstallerSmarty.php');
require_once(INSTALLER_PATH.'/includes/DataField.php');
require_once(INSTALLER_PATH.'/includes/ErrorStack.php');
require_once(INSTALLER_PATH.'/actions/BaseAction.php');
require_once(INSTALLER_PATH.'/tests/BaseTest.php');
require_once(INSTALLER_PATH.'/includes/BaseSet.php');
require_once(INSTALLER_PATH.'/includes/VersionSet.php');
require_once(INSTALLER_PATH.'/includes/Version.php');
require_once(INSTALLER_PATH.'/includes/VersionCheck.php');

class Installer{

	function Installer(){}
	
	function getTemplatePath($template_name){
		if(isset($GLOBALS['INSTALLER']['TEMPLATE_DIR'])){
			if(file_exists($GLOBALS['INSTALLER']['TEMPLATE_DIR'].'/'.$template_name)){
				return 	$GLOBALS['INSTALLER']['TEMPLATE_DIR'].'/'.$template_name;
			}
		}
		
		if(file_exists(INSTALLER_PATH.'/templates/'.$template_name)){
			return INSTALLER_PATH.'/templates/'.$template_name;
		}
		
		ErrorStack::addError("Could not find template file $template_name!", ERRORSTACK_ERROR, 'Installer');
		return $template_name;
	}	

	function getTestPath($class_name){
		if(isset($GLOBALS['INSTALLER']['TEST_DIRS']) && is_array($GLOBALS['INSTALLER']['TEST_DIRS'])){
			foreach($GLOBALS['INSTALLER']['TEST_DIRS'] as $dir){
				if(file_exists($dir.'/'.$class_name.'.php')){
					return $dir.'/'.$class_name.'.php';
				}
			}
		}
		
		if(file_exists(INSTALLER_PATH.'/tests/'.$class_name.'.php')){
			return INSTALLER_PATH.'/tests/'.$class_name.'.php';
		}
		
		ErrorStack::addError("Could not find file for Test class $class_name", ERRORSTACK_FATAL, 'Installer');
		return FALSE;
	}

	function getActionPath($class_name){
		if(isset($GLOBALS['INSTALLER']['ACTION_DIRS']) && is_array($GLOBALS['INSTALLER']['ACTION_DIRS'])){
			foreach($GLOBALS['INSTALLER']['ACTION_DIRS'] as $dir){
				if(file_exists($dir.'/'.$class_name.'.php')){
					return $dir.'/'.$class_name.'.php';
				}
			}
		}
		
		if(file_exists(INSTALLER_PATH.'/actions/'.$class_name.'.php')){
			return INSTALLER_PATH.'/actions/'.$class_name.'.php';
		}
		
		ErrorStack::addError("Could not find file for Action class $class_name", ERRORSTACK_FATAL, 'Installer');
		return FALSE;
	}
	
}

// Bootstrapping tests
// Setup PHP Version numbers
$version_components = split('\.', phpversion());
$GLOBALS['INSTALLER']['PHP_VERSION_MAJOR'] = $version_components[0];
$GLOBALS['INSTALLER']['PHP_VERSION_MINOR'] = $version_components[1];
$GLOBALS['INSTALLER']['PHP_VERSION_REMAINING'] = implode('.', array_splice($version_components, 2));

// A basic version check we need at least PHP 4.2 to run
if($GLOBALS['INSTALLER']['PHP_VERSION_MAJOR'] <= 3 || 
	($GLOBALS['INSTALLER']['PHP_VERSION_MAJOR'] == 4 && $GLOBALS['INSTALLER']['PHP_VERSION_MINOR'] < 2)) {
	print("Installer Error: PHP version 4.2 or greater is required to run the installer!");
	die();	
}

$GLOBALS['INSTALLER']['CONFIG_FILE'] = INSTALLER_PATH.'/config.php'; 
if(!file_exists($GLOBALS['INSTALLER']['CONFIG_FILE'])){
	print("Installer Error: Cound not find config file at ".$GLOBALS['INSTALLER']['CONFIG_FILE']);
	die();	
}

$GLOBALS['INSTALLER']['INSTALLER_CONFIG'] =& new InstallerConfig($GLOBALS['INSTALLER']['CONFIG_FILE']);
if($GLOBALS['INSTALLER']['INSTALLER_CONFIG']->parse() === FALSE){
	print("Installer Error: Error parsing config file {$GLOBALS['INSTALLER']['CONFIG_FILE']}<BR>\n");
	print($GLOBALS['INSTALLER']['INSTALLER_CONFIG']->getErrorsHTML());
	die();
}

// No timeout
set_time_limit(0);

//TODO Detect if session is already started, if so display error 
session_start();
if(isset($_REQUEST['restart_installer']) || !isset($_SESSION['INSTALLER']['ENGINE']) || !is_a($_SESSION['INSTALLER']['ENGINE'], 'InstallerEngine')){
	$_SESSION['INSTALLER']['ENGINE'] =& new InstallerEngine($GLOBALS['INSTALLER']['INSTALLER_CONFIG']); 	
}
$GLOBALS['INSTALLER']['ENGINE'] =& $_SESSION['INSTALLER']['ENGINE'];

if(isset($_REQUEST['previous_step'])){
	$GLOBALS['INSTALLER']['ENGINE']->previousStep();
}elseif(isset($_REQUEST['next_step'])){
	$GLOBALS['INSTALLER']['ENGINE']->nextStep();
}
?>
