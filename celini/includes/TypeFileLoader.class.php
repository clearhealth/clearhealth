<?php
/**
 * Manager loading clniType classes
 *
 * @author	Joshua Eichorn <jeichorn@mail.com>
 * @package com.clear-health.celini
 */
class TypeFileLoader extends FileLoader {

	/**
	 * Init search paths
	 */
	function TypeFileLoader() {
		$finder =& new FileFinder();
		$finder->addPath(APP_ROOT.'/local/includes/clniType/');
		$finder->addModulePaths('/local/includes/clniType/');
		$finder->addPath(CELINI_ROOT.'/includes/clniType/');
		parent::FileLoader($finder);
	}
	
	/**
	 * Load a type
	 */
	function loadType($type) {
		$class = 'clniType' . $type;
		if (class_exists($class)) {
			return true;
		}
		if ($this->requireOnce($type.'.class.php')) {
			return true;
		}
		return false;
	}
}
?>
