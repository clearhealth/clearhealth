<?php
$loader->requireOnce('includes/clni/clniDataCollection.class.php');
class clniDataSqlCollection extends clniDataCollection {
	var $_query;
	var $_res;
	var $_current;

	function setQuery($q) {
		$this->_query = $q;
	}	

	function populate($q = false) {
		if ($q !== false) {
			$this->setQuery($q);
		}
		$db = new clniDb();
		$this->_res = $db->execute($this->_query);
	}

	/**
	 * Reset the iterator
	 */
	function rewind() {
		$this->populate();
	}

	/**
	 * Move next
	 */
	function &next() {
		$this->_res->moveNext();
		return $this->current();
	}

	/**
	 * Is the current row valid
	 */
	function valid() {
		return (!$this->_res->EOF);
	}

	/**
	 * Return an clniData object for the current row
	 */
	function &current() {
		$r = $this->newDataInstance();
		if($this->valid()){
			$r->populate($this->_res->fields);
			return $r;
		}
		
		return $r;
	}
}
