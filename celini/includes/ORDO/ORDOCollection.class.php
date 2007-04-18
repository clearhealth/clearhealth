<?php
/**
 * Contains the results of a {@link ORDOFinder}.
 *
 * This will generally be returned by {@link ORDOFinder::find()}.  To use this, you would use one 
 * of the following two syntaxes:
 *
 * <code>for ($ordo =& $results->current(); $results->valid(); $ordo =& $results->next()) { ... }</code>
 *
 * @todo look into caching 
 */
class ORDOCollection 
{
	var $_results = null;
	var $_ordoName = '';
	var $_current = null;
	
	/**
	 * Handle instantiation
	 *
	 * This should almost always be called by {@link ORDOFinder::find()}.
	 *
	 * @param string
	 * @param object
	 * @param string
	 */
	function ORDOCollection($ordoName, &$results) {
		$this->_results =& $results;
		$this->_ordoName = $ordoName;
		$this->_current =& Celini::newORDO($ordoName);
	}
	
	
	/**
	 * Returns the next ordo in this result set, or false if no more exist
	 *
	 * This differs from {@link next()} in that it will return the first result instead of skipping it.
	 * Note that current() and next() should be used unless you don't mind hogging up memory.
	 *
	 * @return ORDataObject|false
	 */
	function &nextORDO() {
		if (!$this->valid()) {
			$return = false;
			return $return;
		}
		$ordo = $this->current();
		$this->next();
		return $ordo;
	}
	
	
	/**
	 * Returns the total number of results in this result set
	 *
	 * @return int
	 */
	function count() {
		return (!$this->_results) ? 0 : $this->_results->recordCount();
	}
	
	
	/**
	 * Returns the current ORDO
	 *
	 * @return ORDataObject
	 */
	function &current() {
		$this->_current->helper->populateFromResults($this->_current,$this->_results);
		return $this->_current;
	}
	
	
	/**
	 * Returns the current row key
	 *
	 * @return int
	 */
	function key() {
		return $this->_results->_currentRow;
	}
	
	
	/**
	 * Moves to the next result object and returns it
	 *
	 * This differs from {@link nextORDO()} in that it will skip the first result if called the 
	 * first time.
	 *
	 * @return ORDataObject|false
	 */
	function &next() {
		$this->_results->moveNext();
		if (!$this->valid()) {
			$return = false;
			return $return;
		}
		$ordo =& $this->current();
		return $ordo;
	}
	
	
	/**
	 * Resets this to its original state
	 */
	function rewind() {
		$this->_results->moveFirst();
	}
	
	
	/**
	 * Returns a TRUE if this result object is still valid.
	 *
	 *
	 * @internal
	 *    The first if() statement looks for an issue where ADOdb continues to
	 *    return results past the end of the recordset.  It appears to be a 
	 *    MySQL issue and is present on PHP 4.4.0-4 (on Debian) running MySQL
	 *    4.1.14
	 * @return boolean
	 */
	function valid() {
		// check for ADOdb/MySQL/PHP (something) bug that keeps the recordset
		// open past its end.
		if ($this->_results->_currentRow >= $this->count()) {
			return false;
		}
		
		return ($this->_results && !$this->_results->EOF);
	}
	
	
	/**
	 * Creates an array of this results' values
	 *
	 * @return array
	 *
	 * @todo consider caching mechanisms if needed
	 */
	function toArray() {
		$array = array();
		for($this->rewind();$this->valid();$this->next()) {
			if (phpversion() > 5) {
				eval('$array[] = clone $this->current();');
			}
			else {
				$array[] = $this->current();
			}
		}
		return $array;
	}
	
	/**
	 * Creates an associative array containing the ORDO id => ORDO field
	 *
	 * @param string $field
	 * @return array
	 */
	function valueList($field) {
		$return = array();
		while($ordo =& $this->current() && $this->valid()){
			$return[$ordo->get('id')] = $ordo->get($field);
			$this->next();
		}
		$this->rewind();
		return $return;
	}
}

