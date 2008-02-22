<?php
/**
 * Manager loading controller files
 *
 * @author	Joshua Eichorn <jeichorn@mail.com>
 * @package com.clear-health.celini
 */
class ControllerFileLoader extends FileLoader {

	/**
	 * Init search paths
	 */
	function ControllerFileLoader() {
		$finder =& new FileFinder();
		$finder->addPath(APP_ROOT.'/local/controllers/');
		$finder->addModulePaths('/local/controllers/');
		$finder->addPath(CELINI_ROOT.'/controllers/');
		parent::FileLoader($finder);
	}
	
	/**
	 * Load a controller, checking if the class exists first
	 *
	 */
	function loadController($controller) {
		$class = 'C_' . ucfirst($controller);
		if (class_exists($class)) {
			return true;
		}
		if ($this->includeOnce($class.'.class.php')) {
			return true;
		}
		return false;
	}
}
?>
