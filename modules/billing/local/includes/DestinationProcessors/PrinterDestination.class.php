<?php

$loader->requireOnce('includes/DestinationProcessor.abstract.php');

/**
 * The abstract for handling LPR printer destination processing.
 *
 * @abstract
 * @package com.uversainc.celini
 */
class PrinterDestinationProcessor extends DestinationProcessor
{
	var $_readableName = '';
	var $_systemName = '';
	var $_lprOptions = array();
	
	
	
	/**
	 * Handle instantiation
	 */
	function PrinterDestinationProcessor() {
		parent::DestinationProcessor();
	}
	
	
	/**
	 * Initializes this based on a configuration array that is set up from the user.
	 *
	 * @access protected
	 */
	function initFromConfig($configArray) {
		assert('is_array($configArray)');
		
		$this->_readableName = $configArray['readableName'];
		$this->_systemName = $configArray['systemName'];
		settype($configArray['lprOptions'], 'array');;
		$this->_lprOptions = $configArray['lprOptions'];
	}
	
	
	/**
	 * Process the claim package
	 *
	 * @param  string   $package
	 * @param  FBClaim  $claim
	 */
	function processPackage($package, &$claim) {
		$lprCommand = 'lpr -P ' . escapeshellarg($this->_systemName);
		foreach ($this->_lprOptions as $lprOption) {
			$lprCommand .= ' -o ' . escapeshellarg($lprOption);
		}
		
		$printCommand = 'echo "' . str_replace('"', '\"', $package) . '" | ' . $lprCommand;
		$this->_result = exec($printCommand);
	}
	
	
	/**
	 * Return output saying what happened
	 */
	function outputResults() {
		if ($this->_result) {
			return 'Claim successfully sent to ' . $this->_readableName;
		}
		else {
			return 'Claim unsuccessfully sent to ' . $this->_readableName;
		}
	}
}

$dpm =& Celini::dpmInstance();
$config =& Celini::configInstance();
$printerConfig = $config->get('printers');
if (is_array($printerConfig)) {
	foreach ($config->get('printers') as $printerKey => $printerInfo) {
		$cleanedPrinterKey = str_replace(array('-'), '_', $printerKey);
		$printerClassName = 'PrinterDestination_' . $cleanedPrinterKey;
		eval("
	class {$printerClassName} extends PrinterDestinationProcessor 
	{
		function {$printerClassName}() {
			\$conf =& Celini::configInstance();
			\$allPrinterConfigs = \$conf->get('printers');
			\$printerConfig = \$allPrinterConfigs[{$printerKey}];
			\$this->initFromConfig(\$printerConfig);
			parent::PrinterDestinationProcessor();
		}
	}
	");
		$dpm->registerDestinationProcessor('Printer: ' . $printerInfo['readableName'], $printerClassName);
	}
}

?>
