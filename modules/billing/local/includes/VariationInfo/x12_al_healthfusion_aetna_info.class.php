<?php
$loader->requireOnce('includes/VariationInformation.class.php');

class x12_al_healthfusion_aetna_info extends VariationInformation
{
	/**
	 * {@inheritdoc}
	 */
	function filename() {
		return parent::filename() . '.txt';
	}
}

?>
