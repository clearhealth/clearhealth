<?php
/**
* A scrollable datasource
*
* @author	Joshua Eichorn	<jeichorn@mail.com>
*/

/**
 * Its based off the sql datasource
 */
$loader->requireOnce("includes/Datasource_sql.class.php");

class Datasource_scrollable extends Datasource_sql {

	/**
	 * Use numRows to get max # of rows, you'll need to calculate your window in the renderer
	 */
	function fetchRow($rowNum) {
		if ($rowNum < 0) {
			$rowNum = 0;
		}

		if (!$this->_res) {
			$this->prepare();
			$this->rewind();
		}
		$this->_res->move($rowNum);
		$this->_filter();

		$o = new stdClass();

		foreach($this->_res->fields as $key => $val) {
			$o->$key = $val;
		}

		return $o;
	}

	/**
	 * Fetch rows in bulk
	 */
	function fetchBulk($start,$numRows) {
		$this->prepare();
		if ($start < 0) {
			$start = 0;
		}
		if ($start + $numRows > $this->numRows()) {
			$numRows = $this->numRows() - $start;
		}
		$ret = array();
		for($i = $start; $i < ($start+$numRows); $i++) {
			$ret[] = $this->fetchRow($i);
		}
		return $ret;
	}
}
?>
