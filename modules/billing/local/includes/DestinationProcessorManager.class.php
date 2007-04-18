<?php
/**#@+
 * Required file
 */
$loader->requireOnce('includes/clniFileCollector.class.php');
$loader->requireOnce('includes/DestinationProcessor.abstract.php');
/**#@-*/

/**
 * The manager class for all of the various {@link DestinationProcessor}s.
 *
 * @package com.uversainc.celini
 */
class DestinationProcessorManager 
{
	/**
	 * Contains an array of all of the registered {@link DestinationProcessor}s
	 *
	 * @see registerDestinationProcessor()
	 * @var array
	 * @access private
	 */
	var $processors_list = array();
	
	/**
	 * @var boolean
	 * @access private
	 */
	var $_inited = false;

	
	/**
	 * Handles instantiation
	 *
	 * @see init()
	 */
	function DestinationProcessorManager() {
		
	}
	
	
	/**
	 * Handle initialization
	 *
	 * Will only run once.
	 *
	 * @see loadSystemProcessors(), loadUserProcessors()
	 */
	function init() {
		if ($this->_inited) {
			return;
		}

		$this->loadSystemProcessors();
		$this->loadUserProcessors();

		$this->_inited = true;
	}
	
	
	/**
	 * Register a new {@link DestinationProcessor}
	 *
	 * @param  string  $label  The readable label for a {@link DestinationProcessor}
	 * @param  string  $class  The class name for the {@link DestinationProcessor}
	 */
	function registerDestinationProcessor($label, $class) {
		$this->processors_list[$label] = $class;
	}
	
	/**
	 * Includes all system {@link DestinationProcessor}s from within the billing module.
	 *
	 * @see init()
	 */
	function loadSystemProcessors() {
		$collecter =& new clniFileCollector();
		$config =& Celini::configInstance();
		$mpaths = $config->get('module_paths');
		$collecter->collect($mpaths['billing'].'/local/includes/DestinationProcessors');
	}
	
	
	/**
	 * Include all of the {@link DestinationProcessor}s from with in the user/DestinationProcessors
	 * directory.
	 *
	 * @see init()
	 */
	function loadUserProcessors() {
		$collecter =& new clniFileCollector();
		$collecter->collect(APP_ROOT.DIRECTORY_SEPARATOR.'user/DestinationProcessors');
	}
	
	
	/**
	 * Return an array of all of the {@link DestinationProcessor}s that have been registered
	 *
	 * @see registerDestinationProcessor()
	 * @return array
	 */
	function getProcessorList() {
 		$ret = array();
		if (isset($GLOBALS['config']['claimDestination'])) {
			$ret = array_merge($ret,$GLOBALS['config']['claimDestination']);
		}

		if (isset($GLOBALS['config']['clearinghouse'])) {
			$ret['clearinghouse'] = 'ClearingHouse';
		}

		return array_merge(array_flip($this->processors_list), $ret);
	}

	/**
	 * Return an instance of a given {@link DestinationProcessor}.
	 *
	 * @param  string  $processor_name
	 * @return DestinationProcessor
	 */
	function &processorInstance($processor_name) {
		$processor =& new $processor_name;
		return $processor;
	}

}
?>
