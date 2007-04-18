<?php
$loader->requireOnce('includes/VariationInformation.class.php');
$loader->requireOnce('lib/PEAR/Archive/Zip.php');

class x12_al_bcbs_info extends VariationInformation
{
	/**
	 * Contains the file name of the claim to return once initialized
	 *
	 * @var string
	 * @access private
	 */
	var $_filename = null;
	
	/**##@+
	 * {@inheritdoc}
	 */
	function init($options) {
		assert('isset($options["claim"]');
		$prefix = strtolower($options['claim']->get('claim_mode'));
		$db =& new clniDB();
		$counter = $db->customNextId('Daily');
		$this->_filename = $prefix . 'bcp' . str_pad($counter, 4, '0', STR_PAD_LEFT);
	}
	
	
	function filename() {
		return $this->_filename . '.zip';
		
	}
	
	
	/**
	 * Returns the package of this given variation
	 *
	 * @return string
	 */
	function getPackage() {
		$tmpFileName = $this->_filename . '.clm';
		chdir($tmpPath = APP_ROOT . '/tmp');
		$fp = fopen($tmpFileName, 'w');
		fwrite($fp, $this->_package);
		fclose($fp);
		
		$zip = new Archive_Zip($this->_filename . '.zip');
		$zip->create(array($tmpFileName));
		$return = file_get_contents(APP_ROOT . '/tmp/' . $this->_filename . '.zip');
		unlink(APP_ROOT . '/tmp/' . $this->_filename . '.zip');
		unlink(APP_ROOT . '/tmp/' . $this->_filename . '.clm');
		
		return $return;
	}
	
	
	/**
	 * Sets the raw package for this given variation
	 *
	 * @param string
	 */
	function setRawPackage($package) {
		$this->_package = $package;
	}
	
	function contentType() {
		return 'application/zip';
	}
	/**#@-*/
}

?>
