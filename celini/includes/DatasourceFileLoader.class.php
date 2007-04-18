<?php
/**
 * @package com.uversainc.celini
 */

require_once CELINI_ROOT . '/includes/FileLoader.class.php';


/**
 * This extends FileLoader to provide a specific means for locating and loading
 * datasource class files.
 *
 * To see this in use, see {@link ORDataObject::loadDatasource()}
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */

class DatasourceFileLoader extends FileLoader
{
	/**
	 * Handles initializing this object by adding its main directory for files.
	 *
	 * @todo: need to get these paths from config
	 */
	function DatasourceFileLoader() {
		$finder =& new FileFinder();
		$finder->addPath(APP_ROOT . '/local/datasources');
		$finder->addModulePaths('/local/datasources');
		$finder->addPath(CELINI_ROOT . '/datasources');
		parent::FileLoader($finder);
	}
	
	
	/**
	 * Attempts to load a datasource class file.
	 *
	 * Unlike it's parent load() this won't return true upon successfully 
	 * finding and loading a file.  Instead it will return a string with the
	 * name of the datasource it found.  It does still return false if it fails
	 * to locate the file.
	 *
	 * @param	string	Basic name of the datasource to find
	 * @param	array	An array of possible ordo names that this might
	 *					belong to.
	 * @return	string|false
	 */
	function loadFromUnknownParents($file, $parents) {
		$fileMask = "%s_%s_DS";
		
		foreach ($parents as $parent) {
			$realName = sprintf($fileMask, ucwords($parent), $file);
			if ($this->load($realName) !== false) {
				return $realName;
			}
		}
		return false;
	}
	
	
	function load($name) {
		$result = parent::includeOnce($name . '.class.php');
		if ($result === true) {
			return $name;
		}
		return false;
	}
}

