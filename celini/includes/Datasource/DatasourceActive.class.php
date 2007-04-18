<?php
/**
* A scrollable datasource
*
* @author	Joshua Eichorn	<jeichorn@mail.com>
*/

/**
 * Its based off the sql datasource
 */
require_once CELINI_ROOT ."/includes/Datasource_sql.class.php";

class DatasourceActive extends Datasource_sql {
	var $primaryKey;
	var $id = "DatasourceActive";
	var $sort = array();
	var $widths = array();

	function DatasourceActive($id = "DatasourceActive") {
		if (isset($_SESSION['DatasourceActive'][$id]['sort'])) {
			$this->sort = $_SESSION['DatasourceActive'][$id]['sort'];
		}
		if (isset($_SESSION['DatasourceActive'][$this->id]['widths'])) {
			$this->widths = $_SESSION['DatasourceActive'][$this->id]['widths'];
		}
	}

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
		$ret['start'] = $start;
		$ret['length'] = $numRows;
		return $ret;
	}

	/**
	 * update a row
	 */
	function updateRow($data) {
	}

	/**
	 * Set a sort for the rows
	 */
	function setSort($column,$direction,$pos = false) {
		if ($pos === false) {
			$pos = 0;
			if (isset($this->_labels[$column])) {
				$labels = array_keys($this->_labels);
				$pos = array_search($column,$labels);
			}
		}
		$this->sort[$column] = array($column,$direction,$pos);
		$_SESSION['DatasourceActive'][$this->id]['sort'] = $this->sort;
	}

	/**
	 * Set the widths for a column
	 *
	 * @todo: need to store this in a persistant way tied to the users account
	 */
	function setColumnWidths($widths) {
		$_SESSION['DatasourceActive'][$this->id]['widths'] = $widths;
	}


	/**
	 * Preload js
	 */
	function setupJs($varName,$preloadRows = 50) {
		$GLOBALS['loader']->requireOnce('lib/PEAR/HTML/AJAX/Serializer/JSON.php');
		$serializer = new HTML_AJAX_Serializer_JSON();
		return 'var '.$varName.' = '.$serializer->serialize($this->setupData($preloadRows));
	}

	/**
	 * Get preload data
	 */
	function setupData($preloadRows = 50) {
		$ret = array('primaryKey'=>$this->primaryKey,'numRows'=>$this->numRows(),'map'=>$this->getRenderMap(),'sort'=>$this->sort,'widths'=>$this->widths,'data'=>$this->fetchBulk(0,$preloadRows));
		return $ret;
	}

	/**
	 * Return an array of the default ajax methods
	 */
	function ajaxMethods() {
		return array('fetchBulk','fetchRow','updateRow','setSort','setColumnWidths','preview');
	}

	/**
	 * Load the sort info into the query
	 */
	function prepare() {
		foreach($this->sort as $info) {
			$this->addOrderRule($info[0],$info[1],$info[2]);
		}
	}
}
?>
