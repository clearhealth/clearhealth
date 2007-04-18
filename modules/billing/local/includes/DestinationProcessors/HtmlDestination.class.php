<?php
class HtmlDestination extends DestinationProcessor{

	var $_internalName = "HtmlDestination";
	var $_label = 'Web Page';
	var $_package = null;
	var $_format = null;

	function BrowserDestination(){
	}

	function processPackage($package, &$claim, $format) {
		$this->_format = $format;
		$this->_package =  substr($this->_format, 0, 3) == 'x12' ? str_replace('~', "~\n", $package) : $package;
		$this->_claim = $claim;
		
	}

	function outputResults() {
		return '<pre>'.$this->_package.'</pre>';
	}

}

	$dpm =& Celini::dpmInstance();
	$dpm->registerDestinationProcessor('Web Page', 'HtmlDestination');
?>
