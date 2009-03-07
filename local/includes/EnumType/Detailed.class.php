<?php
$loader->requireOnce('includes/EnumType/PerPractice.class.php');
/**
 * Class the defines the default enumeration type
 */
class EnumType_Detailed extends EnumType_Default {

	/**
	 * Field info map, array of field names and types to use when editing
	 */
	var $definition = array(
				'enumeration_value_id' => array('type'=>'hidden'),
				'key' 	=> array('label'=>'Key','size'=>5), 
				'value' => array('label'=>'Value','size'=>15),
				'extra1' => array('label'=>'Extra 1','size'=>30),
				'extra2' => array('label'=>'Extra 2','size'=>30),
				'sort' => array('label'=>'Order&nbsp;','type'=>'order'),
				'status' => array('label'=>'Enabled','type'=>'boolean')
			);

}
?>
