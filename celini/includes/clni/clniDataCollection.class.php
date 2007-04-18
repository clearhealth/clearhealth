<?php
$loader->requireOnce('includes/clni/clniData.class.php');
class clniDataCollection {
	var $_data;
	var $_current;

	var $dataClass = 'clniData';

	function populate($data) {
		$this->_data = $data;
	}

	/**
	 * Reset the iterator
	 */
	function rewind() {
		$this->_current = 0;
	}

	/**
	 * Move next
	 */
	function next() {
		$this->_current++;
	}

	/**
	 * Is the current row valid
	 */
	function valid() {
		if (isset($this->_data[$this->_current])) {
			return true;
		}
		return false;
	}

	function newDataInstance() {
		$c = $this->dataClass;
		return new $c();
	}

	/**
	 * Return an clniData object for the current row
	 */
	function current() {
		if (isset($this->_data[$this->_current])) {
			$r = $this->newDataInstance();
			$r->populate($this->_data[$this->_current]);
			return $r;
		}
	}
}
