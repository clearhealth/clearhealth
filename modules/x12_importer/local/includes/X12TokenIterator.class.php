<?php

class X12TokenIterator
{
	var $_tokens = array();
	
	function X12TokenIterator($tokenizedData) {
		assert('is_array($tokenizedData)');
		$this->_tokens = $tokenizedData;
	}
	
	function count() {
		return count($this->_tokens);
	}
	
	function rewind() {
		reset($this->_tokens);
	}

	function next() {
		next($this->_tokens);
	}

	function valid() {
		if (!is_null(key($this->_tokens))) {
			return true;
		}
		return false;
	}

	function current() {
		return current($this->_tokens);
	}

	function key() {
		return key($this->_tokens);
	}
}

?>
