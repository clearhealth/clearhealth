<?php
$loader->requireOnce('includes/clni/clniDataSqlCollection.class.php');

class clniDataFakeFinder {
	var $_query = '';
	var $_criteria = array();

	function setQuery($q) {
		$this->_query = $q;
	}

	function getQuery() {
		$w = '';
		if (count($this->_criteria) > 0) {
			$w = " WHERE ".implode("\n",$this->_criteria);
		}
		return $this->_query.$w;
	}

	function find() {
		$col =& new clniDataSqlCollection();
		$col->populate($this->getQuery());
		return $col;
	}

	function setParent() {
	}
	function setCriteria($sqlChunk) {
		$this->_criteria[] = $sqlChunk;
	}
}
?>
