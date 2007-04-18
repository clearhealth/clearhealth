<?php

class GenericFinder
{
	var $_collectionName = '';
	
	
	/**
	 * Change the name of the collection object
	 *
	 * @param  string
	 */
	function collectionName($name = null) {
		assert('class_exists($name) || is_null($name)');
		if (!is_null($name)) {
			$this->_collectionName = $name;
		}
		
		return $this->_collectionName;
	}
		
}
