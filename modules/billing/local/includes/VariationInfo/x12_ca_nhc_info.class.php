<?php
$loader->requireOnce('includes/VariationInformation.class.php');

class x12_ca_nhc_info extends VariationInformation
{
	var $_counterResults = 0;
	
	function init($options) {
		$db =& new clniDB();
		$this->_counterResults = $db->customNextId('Named', array('ca_nhc'));
	}
	
	/**
	 * {@inheritdoc}
	 *
	 * @todo Remove hard-coded 'LON' so this will generate files for other clinics as well.
	 */
	function filename() {
		$paddedCounter = str_pad($this->_counterResults, 9, '0', STR_PAD_LEFT);
		return date('Ydm') . "_{$paddedCounter}.837P.LON";
	}
}

?>
