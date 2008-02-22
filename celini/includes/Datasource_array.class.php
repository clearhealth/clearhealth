<?php
/**
 * @package	com.clear-health.celini
 */
 
/**
 * Include the datasource class because were an implementation of it
 */
$loader->requireOnce("includes/Datasource.class.php");

/**
 * Datasource that can be used with a cGrid, just a wrapper around an array
 * @package	com.clear-health.celini
 */
class Datasource_array extends Datasource {
	var $_limit = false;
	var $data = array();
	var $_currentRow = array();
	var $_sorting = false;

	/**
	* @param	array	$labels	array('colname' => 'label')
	* @param	array	$data
	*/
	function setup($labels,$data = array()) {
		$this->_labels = $labels;
		$this->data = $data;
	}

	function setLimit($start,$rows) {
		$this->_limit = array($start,$rows);
	}

	function numRows() {
		return count($this->data);
	}

	/**
	 * Sort data
	 */
	function _sort() {
		foreach($this->_orderRules as $index => $payload) {
			$this->_sorting = $index;
			switch($payload[1]) {
				case 'ASC':
					usort($this->data,array(&$this,'_asort'));
					break;
				case 'DESC':
					usort($this->data,array(&$this,'_dsort'));
					break;
			}
		}
	}

	function _asort($a,$b) {
		if ($a[$this->_sorting] == $b[$this->_sorting]) {
			return 0;
		}
		return ($a[$this->_sorting] < $b[$this->_sorting]) ? -1 : 1;
	}
	function _dsort($a,$b) {
		if ($a[$this->_sorting] == $b[$this->_sorting]) {
			return 0;
		}
		return ($a[$this->_sorting] < $b[$this->_sorting]) ? 1 : -1;
	}

	/**
	 * limit data
	 *
	 * @todo: test and see if this limit implementation is to slow, this could also be done in the iteration methods
	 */
	function _limit() {
		if ($this->_limit === false) {
			return false;
		}
		$this->origData = $this->data;
		$this->data = array();

		for($i = $this->_limit[0]; $i < $this->_limit[1]; $i++) {
			if (isset($this->origData[$i])) {
				$this->data[] = $this->origData[$i];
			}
		}
	}

	/**
	* Run filters on the current row
	*/
	function _filter() {
		parent::_filter($this->_currentRow);
	}

	function rewind() {
		reset($this->data);

		// call a helper function to sort things
		$this->_sort();

		// setup limit
		if ($this->_limit !== false) {
			$this->_limit();
		}


		reset($this->data);
		
		$this->_currentRow = current($this->data);
		$this->_filter();
	}	

	/**
	* Is the current row valid
	*/
	function valid() {
		if ($this->_currentRow === false) {
			return false;
		}
		return true;
	}	
	
	/**
	* Move to the next row
	*/
	function next() { 
		$this->_currentRow = next($this->data);
		$this->_filter();
	}

	/**
	* Get all the fields for this row
	*/
	function get() {
		return $this->_currentRow;
	}
}
?>
