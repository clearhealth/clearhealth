<?php

/**
 * An abstract custom finder that all other custom finder objects will use.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @abstract
 */
class CustomFinder
{
	/**#@+
	 * @access private
	 */
	var $_caller = null;
	var $_parameters = array();
	var $_finder = null;
	/**#@-*/
	
	
	/**
	 * Handle instantiation
	 *
	 * @param  ORDataObject
	 * @param  array
	 */
	function CustomFinder(&$caller, $parameters) {
		assert('is_array($parameters)');
		
		$this->_caller =& $caller;
		$this->_parameters = $parameters;
		$this->_finder =& new ORDOFinder();
	}
}

