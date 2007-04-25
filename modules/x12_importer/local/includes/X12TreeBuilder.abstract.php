<?php

/**
 * An abstract providing the interface for all tree builders.
 *
 * @abstract
 */
class X12TreeBuilder
{
	/**##@+
	 * @access private
	 */
	var $_builtTree = array();
	var $_rawObjectTree = array();
	/**#@-*/
	
	/**
	 * Passes in a raw array of {@link X12Object} objects
	 *
	 * @param array
	 */
	function setRawObjectTree($rawObjectTree) {
		assert('is_array($rawObjectTree)');
		$this->_rawObjectTree = $rawObjectTree;
	}
	
	/**
	 * Returns the built tree once it has been parsed
	 *
	 * @return array
	 */
	function getBuiltTree() {
		$this->buildTree();
		return $this->_builtTree;
	}
	
	
	/**
	 * Build the tree based on the raw object tree set in {@link setRawObjectTree()}
	 *
	 * @abstract
	 */
	function buildTree() {
		
	}
}

?>
