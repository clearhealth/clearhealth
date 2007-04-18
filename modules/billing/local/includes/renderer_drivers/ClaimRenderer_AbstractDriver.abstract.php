<?php
$GLOBALS['loader']->requireOnce('includes/plugins/prefilter.stripedi.php');

/**
 * Used by the claim renderer to handle specific rendering issues
 *
 * @author Travis Swicegood <tswicegood@uversainc.com> 
 * @abstract
 */
class ClaimRenderer_AbstractDriver
{
	var $_claim = null;
	var $_view = null;
	var $_helper = null;
	
	var $_format = null;
	var $_batch = null;
	
	
	/**
	 * Handle instantiation
	 *
	 * @param ClaimRenderer
	 */
	function ClaimRenderer_AbstractDriver(&$renderer) {
		$this->_claim =& $renderer->getClaim();
		$this->_format =& $renderer->getFormat();
		$this->_batch =& $renderer->getBatch();
		
		$this->_helper = new EDIHelper();
		$this->_view =& $this->_newView();
	}
	
	
	/**
	 * Processes the meat of the rendering this driver executes
	 *
	 * @return   string
	 * @abstract
	 */
	function render() {
		die(get_class($this) . ' did not implement render()');
	}
	
	function &_newView() {
		$view =& new clniView();
		$view->register_prefilter("smarty_prefilter_stripedi");
		return $view;
	}
	
	
	
	/**
	 * Determine if this claim can be rendered
	 *
	 * @return boolean
	 * @access protected
	 * @todo this error needs to bubble back up to C_FreeBGateway
	 */
	function _hasCorrectStatus() {
		$config =& Celini::configInstance();
		if ($this->_claim->get("status") != "pending" && $config->get('generatePendingClaimsOnly', false)) {
			$this->last_error = array("130","Could not return result, claim must have pending status to get result."); 
			return false;	
		}
		
		return true;
	}
}

