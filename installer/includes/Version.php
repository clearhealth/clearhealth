<?php
/*
 * Version class
 * 
 */

require_once realpath(dirname(__FILE__)).'/ActionSet.php';
require_once realpath(dirname(__FILE__)).'/TestSet.php';

class Version{

	var $version;
	
	var $actions;
	
	var $tests;
		
	function Version($version){
		$this->version = $version;
		$this->actions = new ActionSet();
		$this->tests = new TestSet();
	}
	
	function addTest($class_name, $params){
		require_once Installer::getTestPath($class_name);
		$this->tests->add(new $class_name($params));
	}
	
	function addAction($class_name, $params){
		require_once Installer::getActionPath($class_name);
		$this->actions->add(new $class_name($params));		
	}

	function getVersion(){
		return $this->version;
	}
}
?>
