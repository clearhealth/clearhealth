<?php
/**
 * Abstract datasource implementation
 *
 * Methods that must be implemented in subclasses are located at the bottom and marked
 *
 * @package	com.uversainc.celini
 *
 * @todo The order rules are getting complex enough that they should be stored
 *    in their own object and allow that object to handle the interactions.
 * @todo It might be wise to move all of the render map code into a 
 *    DatasourceRenderMap to handle just that portion of this code.
 */
class Datasource {
	var $template = false;
	var $filter = false;
	var $filterExtra = array();
	var $emptyVal = "not specified";

	var $_orderRules = array();
	var $_orderCurrent = false;
	var $_orderCurrentDirection = false;
	var $_defaultOrderRules = array();
	var $_labels = array();
	var $_renderMap = false;
	
	
	/**
	 * Stores the type, if known, of output being generated
	 *
	 * @var string|null
	 */
	var $_type = null;
	
	
	/**
	 * Stores an array of alternate labels that are dependent on a certain type
	 * of output.
	 *
	 * @var array
	 * @access protected
	 */
	var $_typeDependentLabels = array();
	
	
	/**
	 * Stores any custom column name mapping that is happening
	 *
	 * @var array
	 * @see addColumnMapping(), getColumnMapping()
	 * @access private
	 */
	var $_columnMap = array();
	
	
	/**
	 * Stores a list of column names that should always be hidden
	 *
	 * @var array
	 * @see hideColumn()
	 * @access private
	 */
	var $_hiddenColumns = array();
	

	/**
	 * Number of columns in the datasource, this is not the number that will display and is based off of labels
	 *
	 * @todo: base this off of rendermap instead
	 */
	function numCols() {
		return count($this->getColumnLabels());
	}

	/**
	 * Add an order by rule
	 *
	 * @param  string  The name of the column
	 * @param  string  The direction of sorting (ASC, DESC, or OFF)
	 * @param  string  The order in which the column should be displayed (also the order the columns are sorted in)
	 */
	function addOrderRule($column,$direction='ASC',$order=0) {
		$columnName = $this->_getColumnMapping($column);
		$this->_orderRules[$columnName] = array($columnName, $direction,$order);
	}
	
	/**
	 * Add a custom column to order by mapping
	 *
	 * There are instances where the name of a column and the actual row it 
	 * needs to order are different.  The most obvious is formatted date 
	 * columns' needing to be mapped to their raw ISO-date column.
	 *
	 * @param  string  The name of the column Datasource column
	 * @param  string  The name of the hidden column
	 */
	function addColumnMapping($visible, $hidden) {
		$this->_columnMap[$visible] = $hidden;
	}
	
	
	/**
	 * Returns the raw data column that this visible column is mapped to.
	 *
	 * @param  string  The name of the viewable column
	 * @return string
	 * @access protected
	 */
	function _getColumnMapping($column) {
		return isset($this->_columnMap[$column]) ? 
			$this->_columnMap[$column] : 
			$column;
	}
	
	/**
	 * Returns the visible data column that this column name is mapped to.
	 *
	 * This performs the reverse of {@link _getColumnMapping()}
	 *
	 * @param  string  The name of the viewable/raw column
	 * @return string
	 * @access protected
	 */
	function _getColumnMappingReverse($column) {
		return ($key = array_search($column, $this->_columnMap)) !== false ?
			$key : $column;
	}
	
	
	/**
	 * Explicitly say this column should never be displayed.
	 *
	 * @param  string  The name of the column to hide
	 */
	function hideColumn($column) {
		$this->_hiddenColumns[] = $column;
	}
	
	
	/**
	 * Returns <i>true</i> if an order rule exists for a given <i>$column</i>,
	 * whether or not it is mapped.
	 *
	 * @return boolean
	 */
	function orderRuleExists($column) {
		return isset($this->_orderRules[$this->_getColumnMapping($column)]);
	}
	
	
	/**
	 * Returns an order rule for a given column
	 *
	 * @return array
	 */
	function getOrderRule($column) {
		return $this->_orderRules[$this->_getColumnMapping($column)];
	}
	
	
	/**
	 * Return all of the order rules with mapped columns reversed out to their
	 * visible name
	 *
	 * @return array
	 */
	function getAllOrderRules() {
		$return = array();
		foreach ($this->_orderRules as $orderRule) {
			$orderRule[0] = $this->_getColumnMappingReverse($orderRule[0]);
			$return[$orderRule[0]] = $orderRule;
		}
		return $return;
	}

	/**
	 * Update order rules
	 */
	function updateAllOrderRules($rules) {
		$this->_orderRules = $rules;
	}

	/**
	 * Add an default order by rule
	 */
	function addDefaultOrderRule($column,$direction='ASC',$order=false) {
		$this->_defaultOrderRules[$column] = array($column, $direction,$order);
	}

	/**
	 * Load the default order by rules onto the ds
	 */
	function loadDefaultOrderRules() {
		foreach($this->_defaultOrderRules as $col => $rule) {
			$this->addOrderRule($rule[0],$rule[1],$rule[2]);
		}
	}

	/**
	 * Util method user by getRenderMap to order the _orderRules arfray
	 */
	function orderSort($ia,$ib) {
		$a = $ia[2];
		$b = $ib[2];
		if ($a == $b) {
			return 0;
		}
		return ($a > $b) ? 1 : -1;
	}

	/**
	 * Get the order that columns should be rendered in
	 */
	function getRenderMap() {
		if ($this->_renderMap === false) {

			// what our render map will be if have no order rules
			$this->_renderMap = array_keys($this->getColumnLabels());

			// Don't access the _orderRules array directly, or the code won't
			// take column mapping taken into account. 
			$orderRules = $this->getAllOrderRules();

			// if the position is false for a rule look it up from the starting column position
			// get the new position of the current rule
			$currentPos = false;
			$startingLookup = array_flip($this->_renderMap);
			foreach($orderRules as $key => $rule) {
				if ($rule[2] === false) {
					$orderRules[$key][2] = $startingLookup[$key];
				}
				$field = $rule[0];
				$newPos = $rule[2];
				if ($field === $this->_getColumnMappingReverse($this->_orderCurrent)) {
					$currentPos = $newPos;
				}
			}

			// move any order rules that have the same position as the current one out of the way
			if ($currentPos) {
				foreach($orderRules as $key => $rule) {
					$field = $rule[0];
					$pos = $rule[2];

					if ($pos == $currentPos && $field != $this->_getColumnMappingReverse($this->_orderCurrent)) {
						// pull out the current field
						if ($this->_orderCurrentDirection == 'left') {
							$newPos = $pos + 1;
						}
						else {
							$newPos = $pos - 1;
						}
						$orderRules[$key][2] = $newPos;
					}
				}
			}

			// put the order rules in position order
			uasort($orderRules,array(&$this,'_orderRulesSorter'));

			$this->updateAllOrderRules($orderRules);

			// pull fields to reorder starting with the highest positions
			$data = array();
			foreach(array_reverse($orderRules) as $rule) {
				$field = $rule[0];

				// get the fields position
				$curPos = array_search($field,$this->_renderMap);

				// pull out the current field
				$data[$field] = array_splice($this->_renderMap,$curPos,1);
			}

			// insert our pulled fields back in place starting at pos 0
			foreach($orderRules as $rule) {
				$field = $rule[0];
				$newPos = $rule[2];

				// insert it in its new position
				array_splice($this->_renderMap,$newPos,0,$data[$field]);
			}

			// remove any hidden columns
			if (count($this->_hiddenColumns) > 0) {
 			 	foreach ($this->_hiddenColumns as $columnName) {
					if (($columnKey = array_search($columnName, $this->_renderMap)) !== false) {
						array_splice($this->_renderMap,$columnKey,1);
					}
				}
			}
		}
		return $this->_renderMap;
	}

	/**
	 * Sort worker method for order rules
	 */
	function _orderRulesSorter($a,$b) {
		if ($a[2] == $b[2]) {
			return 0;
		}
		return ($a[2] < $b[2]) ? -1 : 1;
	}

	/**
	 * Get column labels. 
	 *
	 * Subclasses might want to implement some sort of automatic label 
	 * generation from source data, but once generated, should always refer
	 * back to parent::getColumnLabels() to insure that all typeDependentLabels
	 * are applied prior displaying.
	 *
	 * @return array
	 */
	function getColumnLabels() {
		if (isset($this->_typeDependentLabels[$this->_type])) {
			return array_merge($this->_labels, $this->_typeDependentLabels[$this->_type]);
		} 
		else {
			return $this->_labels;
		}
	}
	
	
	/**
	 * This adds a type dependent label
	 *
	 * @param string	Type of display this label should apply to
	 * @param string	The column that this label should be displayed at
	 * @param mixed	The actual label that should be displayed.
	 */
	function setTypeDependentLabel($type, $column, $label) {
		if (!isset($this->_typeDependentLabels[$type])) {
			$this->_typeDependentLabels[$type] = array();
		}
		$this->_typeDependentLabels[$type][$column] = $label;
	}

	/**
	 * hook point for subclasses
	 */
	function prepare() {
	}

	/**
	 * Register a filter for a column
	 *
	 * @param string
	 * @param string
	 * @param string|false
	 * @param string
	 *	The type of display that this filter should be applied in.  If this is
	 *	not specified, it is assumed that this filter should be applied every
	 *	time a column is requested.
	 */
	function registerFilter($column,$callback,$extra=false,$type='universal') {
		$this->filter[$column][$type] = $callback;
		$this->filterExtra[$column] = $extra;
	}
	
	/**
	 * Clear current column filters
	 */
	function clearFilters() {
		$this->filter = false;
	}
	
	/**
	 * Clear filters and templates
	 */
	function clearAll() {
		$this->clearFilters();
		$this->template = false;
	}

	

	/**
	 * Register a template for a column
	 */
	function registerTemplate($column,$template) {
		$this->template[$column] = $template;
	}

	/**
	 * Set a label
	 */
	function setLabel($column,$label) {
		$this->_labels[$column] = $label;
	}

	/**
	 * Return the datasource as an array
	 */
	function toArray($assoc_key = false, $assoc_val = false) {
		if ($assoc_key !== false && $assoc_val !== false) {
			$ret = array();
			$this->rewind();
			while($this->valid()) {
				$row = $this->get();
				$ret[$row[$assoc_key]] = $row[$assoc_val];
				$this->next();
			}
			return $ret;
		}
		else {
			$ret = array();
			$this->rewind();
			while($this->valid()) {
				$ret[] = $this->get();
				$this->next();
			}
			return $ret;
		}
	}

	/**
	 * basic filter function
	 * @todo move somewhere else
	 */
	function emptyFill($val) {
		if (empty($val)) {
			return $this->emptyVal;
		}
		return $val;
	}

	/**
	 * basic enum lookup filter
	 * @todo move somewhere else
	 */
	function enumLookup($val,$row,$extra) {
		if ($extra) {
			$enum = $extra[0];
			if (!isset($this->_enumManager)) {
				$this->_enumManager =& EnumManager::getInstance();
			}
			return $this->_enumManager->lookup($enum,$val);
		}
	}


	/**
	 * Run filters on the current row
	 * Must be implemented in subclasses
	 * You can use this implementation in subclasses by passing it the current row
	 */
	function _filter(&$currentRow) {
		if (is_array($this->filter)) {
			if (is_array($currentRow)) {
				foreach(array_keys($this->filter) as $key) {
					if (!isset($currentRow[$key])) {
						$currentRow[$key] = "";
					}
					$extra = false;
					if (isset($this->filterExtra[$key])) {
						$extra = $this->filterExtra[$key];
					}
					
					$currentRow[$key] = $this->_getFilteredOutput('universal',$key,$currentRow[$key],$currentRow,$extra);
					if (!is_null($this->_type)) {
						$currentRow[$key] = $this->_getFilteredOutput($this->_type,$key,$currentRow[$key],$currentRow,$extra);
					}
				}
			}
		}
		if (is_array($this->template)) {
			$replace_search = array();
			$replace_val = array();
			if (is_array($currentRow)) {
				foreach($this->template as $key => $template) {
					if (!isset($currentRow[$key])) {
						$currentRow[$key] = "";
					}
					foreach($currentRow as $k => $v) {
						$replace_search[] = "{\$$k}";
						$replace_val[] = $v;
					}
					$currentRow[$key] = str_replace($replace_search,$replace_val,$template);
				}
			}
		}
	}
	
	
	function _getFilteredOutput($type, $key, $value, $row, $extra) {
		if (isset($this->filter[$key][$type])) {
			return call_user_func($this->filter[$key][$type],$row[$key],$row,$extra);
		}
		else {
			return $row[$key];
		}
	}

	/**
	 * Setup labels and other basic params
	 * Must be implemented in subclasses
	 */
	function setup() {
	}

	/**
	 * Datasource specific limiting
	 * Must be implemented in subclass
	 */
	function setLimit($start,$rows) {
	}

	/**
	 * Number of total rows in the datasource
	 * Must be implemented in subclass
	 */
	function numRows() {
	}


	/**
	 * Setup the datasource for iterating
	 * Must be implemented in subclass
	 */
	function rewind() {
	}	

	
	/**
	 * Is the current row valid
	 * Must be implemented in subclass
	 */
	function valid() {
		return false;
	}	
	
	/**
	 * Move to the next row
	 * Must be implemented in subclass
	 */
	function next() { 
		$this->_filter();
	}

	/**
	 * Get all the fields for this row
	 * Must be implemented in subclass
	 */
	function get() {
		return array();
	}
}
?>
