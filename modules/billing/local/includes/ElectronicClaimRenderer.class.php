<?php

$loader->requireOnce('includes/clniView.class.php');
$loader->requireOnce('includes/EDIHelper.class.php');
$loader->requireOnce('includes/renderer_drivers/ClaimRenderer_X12Driver.class.php');
$loader->requireOnce('includes/renderer_drivers/ClaimRenderer_NestedDriver.class.php');


/**
 * This class handles rendering an electronic claim report.
 *
 * This will employ a Strategy pattern to allow variations to employ the various
 * algorithms they need to on the data they are rendering.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class ElectronicClaimRenderer
{
	var $_view = null;
	var $_helper = null;
	var $_claim = null;
	
	var $_batch = null;
	var $_format = null;
	
	
	/**
	 * Handle instantiation
	 *
	 * @param array
	 * @param string
	 */
	function ElectronicClaimRenderer($batch, $format) {
		$this->_view = new clniView();
		
		$this->_batch = $batch;
		$this->_format = $format;
		
		// init claim
		$claim_id = key($this->_batch);//pull header defaults from the first claim in the batch
		$this->_claim =& Celini::newORDO('FBClaim',$claim_id); //newway
	}


	/**
	 * Returns the rendered results of a claim in a particular variation
	 *
	 * @return string
	 */
	function render() {
		$driver =& $this->_getDriver();
		return $driver->render();
	}
	
	/**
	 * Determines which internal driver to utilize for rendering
	 *
	 * @see     ClaimRenderer_AbstractDriver
	 * @return  ClaimRenderer_AbstractDriver
	 * @access  private
	 */
	function &_getDriver() { 
		switch ($this->_format) {
			case 'OnlySpecialCases' :
				$return =& new ClaimRenderer_X12Driver($this);
				return $return;
				break;
			
			default :
				$return =& new ClaimRenderer_NestedDriver($this);
				return $return;
				break;
		}
	}
	
	
	/**
	 * Returns the claim this is rendering
	 *
	 * @return FBClaim
	 */
	function &getClaim() {
		return $this->_claim;
	}
	
	
	/**
	 * Returns the format being rendered
	 *
	 * @return string
	 * @todo This should probably be renamed to variation
	 */
	function &getFormat() {
		return $this->_format;
	}
	
	
	/**
	 * Returns the batch that is being processed
	 *
	 * @return array
	 */
	function &getBatch() {
		return $this->_batch;
	}
}
