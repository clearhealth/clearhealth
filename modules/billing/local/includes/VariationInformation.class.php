<?php

/**
 * @abstract
 */
class VariationInformation 
{
	/**##@+
	 * @access protected
	 */
	var $_package = '';
	var $_options = '';
	/**##@-*/
	
	
	/**
	 * Initializes this object
	 *
	 * @param  array  $options
	 */
	function init($options) {
		$this->_options = $options;
	}
	
	/**
	 * Returns a filename for the given variation.
	 *
	 * @param array
	 */
	function filename() {
		return 'claim.' . $this->_options['claim']->get('audit_number')  . '.' . $this->_options['format'];
	}
	
	/**
	 * Returns the package of this given variation
	 *
	 * @return string
	 */
	function getPackage() {
		return $this->_package;
	}
	
	/**
	 * Sets the raw package for this given variation
	 *
	 * @param string
	 */
	function setRawPackage($package) {
		$this->_package = $package;
	}
	
	/**
	 * Returns the content-type this given variation uses
	 *
	 * @return string
	 */
	function contentType() {
		return 'application/edi-x12';
	}
}

?>
