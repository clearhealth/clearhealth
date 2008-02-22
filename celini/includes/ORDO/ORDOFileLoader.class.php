<?php

$loader->requireOnce('/includes/ORDO/ORDOHelper.class.php');

/**
 * Manage loading ORDO files
 *
 * @author	Travis Swicegood <tswicegood@uversainc.com>
 * @package com.clear-health.celini
 */
class ORDOFileLoader extends FileLoader 
{
	/**
	 * A reference to the {@link ORDOHelper} object
	 *
	 * @var    ORDOHelper
	 * @access private
	 */
	var $_helper = null;
	
	
	/**
	 * Init search paths
	 */
	function ORDOFileLoader() {
		$finder =& new FileFinder();
		$finder->addPath(APP_ROOT.'/local/ordo/');
		$finder->addModulePaths('/local/ordo/');
		$finder->addPath(CELINI_ROOT.'/ordo/');
		parent::FileLoader($finder);
		
		$this->_helper =& new ORDOHelper();
	}
	
	
	/**
	 * Load a controller, checking if the class exists first
	 *
	 */
	function loadORDO($name) {
		$realName = $this->_helper->getName($name);
		if (class_exists($realName)) {
			return true;
		}
		if ($this->requireOnce($realName.'.class.php')) {
			return true;
		}
		return false;
	}
}

?>
