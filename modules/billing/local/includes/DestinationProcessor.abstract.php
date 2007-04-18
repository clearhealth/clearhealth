<?php
/**
 * Destination Processor interface
 *
 * Will just load all the classes for a system directory and a user directory to get the list
 * When a file is included it can register the name of hte processor and the class
 *
 * @package com.uversainc.celini
 * @abstract
 */
class DestinationProcessor 
{
	/**
	 * Maintains a reference to the {@link FBClaim} that this {@link DestinationProcessor} is
	 * working with.
	 *
	 * @var FBClaim
	 * @access private
	 */
	var $_claim = null;
	
	
	/**
	 * Handle instantiation
	 */
	function DestinationProcessor() {
		
	}
	
	
	/**
	 * Takes the claim package and performs whatever processing action this
	 * {@link DestinationProcessor} handles.
	 *
	 * @param  string   $package
	 * @param  FBClaim  $claim
	 * @param  string   $format
	 */
	function processPackage($package, &$claim, $format = '') {
		
	}
	
	
	/**
	 * Displays whatever output this {@link DestinationProcessor} generates.
	 */
	function outputResults() {
		
	}
}

?>
