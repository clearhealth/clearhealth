<?php
/**
 * Class the defines the default enumeration type
 */
class EnumType_MappedValue extends EnumType_Default{

	var $assocKey = 'extra1';

	/**
	 * Field info map, array of field names and types to use when editing
	 */
	var $definition = array(
				'enumeration_value_id' => array('type'=>'hidden'),
				'extra1' => array('label' => 'Key','size'=>5),
				'value' => array('label'=>'Value','size'=>25),
				'key' 	=> array('label' => 'Numeric Key','size'=>5),
				'extra2' => false,
				'sort' => array('label'=>'Order&nbsp;','type'=>'order'),
				'status' => array('label'=>'Enabled','type'=>'boolean')
			);
}
?>
