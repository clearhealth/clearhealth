<?php
/**
 * Class the defines the default enumeration type
 */
class EnumType_Immunization extends EnumType_Default {

	var $definition = array(
				'enumeration_value_id' => array('type'=>'hidden'),
				'key' 	=> array('label'=>'Key','size'=>5), 
				'value' => array('label'=>'Value','size'=>100),
				'extra1' => array('label' => '*VX Code','size'=>'5'),
				'extra2' => false,
				'sort' => array('label'=>'Order&nbsp;','type'=>'order'),
				'status' => array('label'=>'Enabled','type'=>'boolean')
			);

}
?>
