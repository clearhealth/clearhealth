<?php
$GLOBALS['loader']->requireOnce('includes/VariationManager.class.php');

class BrowserDestination extends DestinationProcessor{


	var $_internalName = "BrowserDestination";
	var $_label = 'Download';
	var $_package = null;
	var $_format = '';
	
	var $_vm = null;

	function BrowserDestination(){
	}

	function processPackage($package, &$claim, $format) {
		$this->_format = $format;
		$options = array(
			'claim' => &$claim,
			'format' => $this->_format
		);
		
		$this->_vm = new VariationManager();
		$this->_vm->init($this->_format, $options);

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-type: ' . $this->_vm->contentType($this->_format));
		//header('Content-Disposition: attachment; filename="claim.' . $c->get("audit_number")  . '.' . $format . '"');
		
		
		$filename = $this->_vm->filename($this->_format);
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		$this->_package = $package;
	}


	function outputResults() {
		if (is_null($this->_vm)) {
			trigger_error('outputResults() cannot be called prior to processPackage()');
		}
		$this->_vm->setRawPackage($this->_format, $this->_package);
		return $this->_vm->getPackage($this->_format);
	}

}

$dpm =& Celini::dpmInstance();
$dpm->registerDestinationProcessor('Download', 'BrowserDestination');

?>
