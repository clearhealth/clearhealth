<?php

/**
 * @abstract
 */
class altAlertList
{
	/**
	 * Stores the result object from querying the database
	 *
	 * @var    object
	 * @access protected
	 */
	var $_results = null;
	
	
	/**
	 * Returns the next {@link altNotice} in this list, or false if it has
	 * reached the end.
	 *
	 * @return altNotice|false
	 */
	function &nextAlert() {
		if (!$this->_results || $this->_results->EOF) {
			$return = false;
			return $return;
		}
		
		$alert =& Celini::newORDO('altNotice', $this->_results->fields['altnotice_id']);
		$this->_results->moveNext();
		return $alert;
	}
}
