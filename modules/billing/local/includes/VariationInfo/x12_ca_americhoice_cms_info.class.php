<?php
$loader->requireOnce('includes/VariationInformation.class.php');

class x12_ca_americhoice_cms_info extends VariationInformation
{
	var $_counterResults = 0;
	
	function init($options) {
		$practice =& $options['claim']->childEntity('FBPractice', null, 0);
		$prefix = strtoupper($practice->get('sender_id'));
		
		$date = str_pad(date('z'), 3, 0, STR_PAD_LEFT);
		
		/**
		 * @internal
		 * 1 = County Medical Services (CMS), 2 = PES, 3 = Ryan White,
		 * 4 = Primary Care Services, 5 = Sherrif's
		 */
		$programType = '1';
		
		$db =& new clniDB();
		$counterResults = $db->customNextId('DailyNamed', array('ca_americhoice'));
		
		$this->_filename = $prefix . $date . $programType . $counterResults . '.837';
	}
	
	/**
	 * {@inheritdoc}
	 *
	 * @todo Remove hard-coded 'LON' so this will generate files for other clinics as well.
	 */
	function filename() {
		return $this->_filename;
	}
}

?>
