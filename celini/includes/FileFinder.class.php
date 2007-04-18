<?php
/**
 * @package com.uversainc.celini
 */

/**
 * Serves as means of locating a file within a given set of paths.
 *
 * @see FileLoader
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class FileFinder
{
	/**#@+
	 * @access private
	 */
	var $_paths = array();
	var $_debug = false;
	var $_inited = false;
	/**#@-*/
	
	
	/**
	 * An array of known module paths
	 *
	 * @var    array
	 * @access private
	 * @see    addModulePaths()
	 */
	var $_modulePaths = array();
	
	
	/**
	 * Handle initialization
	 */
	function FileFinder() {	
		// try to load module paths
		if (isset($GLOBALS['configObj'])) {
			$module_paths = $GLOBALS['configObj']->get('module_paths');
			$this->_modulePaths = (array)$module_paths;
		}
	}
	
	
	/**
	 * Add a path to search in
	 *
	 * @param	string
	 */
	function addPath($path) {
		$this->_paths[] = $path;
	}
	
	
	/**
	 * Allows the registration of a given <i>$path</i> within all the available
	 * module directories
	 *
	 * @param	string
	 * @access	protected
	 */
	function addModulePaths($path) {
		foreach ($this->_modulePaths as $modulePath) {
			$this->addPath($modulePath . $path);
		}
	}
	
	
	/**
	 * Handle initialization the basic Celini directories
	 *
	 * If <i>$path</i> is specified, that will be appended to the end of all of the various
	 * Celini paths.  <i>$path</i> should be prefixed with a slash.
	 *
	 * <code>$finder->initCeliniPaths('/includes')</code>
	 *
	 * @param  string
	 */
	function initCeliniPaths($path = '') {
		if ($this->_inited) {
			return;
		}
		$this->addPath(APP_ROOT . $path);
		$this->addModulePaths($path);
		$this->addPath(CELINI_ROOT);
	}
	
	
	/**
	 * Attempts to find a file, and returns its path if its found, false if it
	 * is not.
	 *
	 * @param	string
	 * @return	string
	 * @access	string|false
	 */
	function find($file) {
		foreach ($this->_paths as $path) {
			$filePath = preg_replace('/\/\//', '/', $path . '/' . $file);
			if ($this->_debug) {
				echo 'checking: '.$filePath;
			}
			if (file_exists($filePath)) {
				if ($this->_debug) {
					echo ' ... YES <br>';
				}
				return $filePath;
			}
			if ($this->_debug) {
				echo ' ... NO<br>';
			}
		}
		return false;
	}	
}
