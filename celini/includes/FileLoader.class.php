<?php
/**
 * @package com.clear-health.celini
 */

require_once dirname(__FILE__) . '/FileFinder.class.php';
/**
 * A simple class for loading and finding files.
 *
 * This serves as the basis for all sub-classes which specialize in finding one
 * type of file.
 *
 * @see FileFinder
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class FileLoader
{
	/**
	 * Stores a reference to a {@link FileFinder} object.
	 
	 * @var    object
	 * @access private
	 * @see    _initFinder(), FileFinder
	 */
	var $_finder = null;
	
	/**
	 * Handle initialization
	 *
	 * @param  FileFinder
	 */
	function FileLoader(&$finder) {
		$this->_finder =& $finder;
	}
	
	
	/**
	 * An alias to {@link FileFinder::addPath()}
	 *
	 * @see   FileFinder::addPath()
	 * @param string
	 * @deprecated
	 */
	function addPath($path) {
		$this->_finder->addPath($path);
	}
	
	/**
	 * An alias to {@link FileFinder::addModulePaths()}
	 *
	 * @see    FileFinder::addModulePaths()
	 * @param  string
	 * @access protected
	 * @deprecated
	 */
	function addModulePaths($path) {
		$this->_finder->addModulePaths($path);
	}
	
	
	/**
	 * Deprecated in favor of {@link includeOnce()}
	 *
	 * @see    includeOnce(), requireOnce()
	 * @param  string
	 * @return boolean
	 */
	function load($file) {
		return $this->includeOnce($file);
	}

	/**
	 * Attempt to find and load a given $file
	 *
	 * @see    requireOnce()
	 * @param  string
	 * @return boolean
	 */
	function includeOnce($file) { 
		$filePath = $this->_finder->find($file);
		if ($filePath === false) {
			return false;
		}
		global $loader;

		if (isset($GLOBALS['___REQUIRED'][$filePath])) {
			return true;
		}
		if (isset($this->debugFiles)) {
			echo "require_once ".$filePath ."<br>\n";
		}
		$GLOBALS['___REQUIRED'][$filePath] = true;
		require_once $filePath;
		return true;
	}
	
	/**
	 * Require a file a single time.
	 *
	 * @todo trigger an error on a failure once code has been updated to use {@link includeOnce()}
	 *
	 * @see   includeOnce()
	 * @param string
	 */
	function requireOnce($file) {
		if (isset($this->debug)) {
			$this->_finder->_debug = $this->debug;
		}
		$status = $this->includeOnce($file);
		if ($status === false) {
			Celini::raiseError("Missing File: $file");
		}
		return $status;
	}
	
	/**
	 * Initializes the {@link FileFinder} object.
	 *
	 * This can't be done outside this class as the FileLoader global will not have been 
	 * initialized yet.
	 *
	 * @access private
	 */
	function _initFinder() {
		if (!is_null($this->_finder)) {
			return;
		}
		$GLOBALS['loader']->requireOnce('includes/FileFinder.class.php');		
	}
}

?>
