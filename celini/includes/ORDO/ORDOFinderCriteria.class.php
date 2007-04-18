<?php
/**
 * Provides an object to specifying criteria to be used for searching via 
 * {@link ORDOFinder}
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @todo implement this a bit more
 * @todo add a group container object for managing multiple criteria and their
 *    relationship to each other (AND, OR, etc.
 */
class ORDOFinderCriteria
{
	var $_name = '';
	var $_value = '';
	var $_comparison = '=';
		
	function ORDOFinderCriteria($columnName, $matchingValue, $comparison = '=') {
		$this->_name = $columnName;
		$this->_value = $matchingValue;
	}
	
	function toString() {
		$db =& new clniDB();
		return $this->_name . ' ' . $this->_comparison . ' ' . $db->quote($this->_value);
	}
}

