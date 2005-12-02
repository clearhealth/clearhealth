<?php
$loader->requireOnce('includes/EnumType/PerPractice.class.php');
/**
 * Class the defines the default enumeration type
 */
class EnumType_AppointmentReason extends EnumType_PerPractice {

	/**
	 * Field info map, array of field names and types to use when editing
	 */
	var $definition = array(
				'enumeration_value_id' => array('type'=>'hidden'),
				'key' 	=> array('label'=>'Key','size'=>5), 
				'value' => array('label'=>'Value','size'=>15),
				'extra1' => array('label'=>'Template','type'=>'AppointmentTemplate'),
				'extra2' => false,
				'sort' => array('label'=>'Order','type'=>'order'),
				'status' => array('label'=>'Enabled?','type'=>'boolean')
			);

}
?>
