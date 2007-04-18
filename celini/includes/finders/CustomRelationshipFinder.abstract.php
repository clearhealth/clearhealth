<?php

/**
 * An abstract custom finder that all other custom finder objects will use.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @abstract
 */
class CustomRelationshipFinder extends CustomFinder
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
	function CustomRelationshipFinder(&$caller, $parameters) {
		parent::CustomFinder($caller, $parameters);
		$this->_finder =& new RelationshipFinder();
	}
}

