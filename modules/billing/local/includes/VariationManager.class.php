<?php

class VariationManager
{
	var $_variationPath = 'includes/VariationInfo';
	var $_info = array();
	
	
	/**
	 * Handle initializing the {@link VariationInformation} file object for $variation
	 *
	 * @param string
	 */
	function init($variation, $options = array()) {
		settype($options, 'array');
		if (isset($this->_info[$variation])) {
			return;
		}
		
		if (!$GLOBALS['loader']->includeOnce($this->_variationPath . '/' . $variation . '_info.class.php')) {
			$GLOBALS['loader']->requireOnce('includes/VariationInformation.class.php');
			$this->_info[$variation] =& new VariationInformation();
		}
		else {
			$infoObjName = $variation . '_info';
			$this->_info[$variation] =& new $infoObjName();
		}
		
		if (count($options) > 0) {
			$this->_info[$variation]->init($options);
		}
	}
	
	
	/**
	 * Return the filename for the 
	 *
	 * @param  string  $variation
	 * @param  string  $options
	 * @return string
	 */
	function filename($variation) {
		return $this->_info[$variation]->filename();
	}
	
	
	/**
	 * Set the raw package that's to be returned
	 *
	 * @param  string  $variation
	 * @param  string  $package
	 */
	function setRawPackage($variation, $package) {
		$this->_info[$variation]->setRawPackage($package);
	}
	
	
	/**
	 * Return the processed package for a given variation
	 *
	 * @param  string  $variation
	 * @return string
	 */
	function getPackage($variation) {
		return $this->_info[$variation]->getPackage();
	}
	
	
	/**
	 * Return the content type this variation should be using
	 *
	 * @param  string  $variation
	 * @return string
	 */
	function contentType($variation) {
		return $this->_info[$variation]->contentType();
	}
}

?>
