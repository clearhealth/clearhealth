<?php
$loader->requireOnce('includes/EnumType/PerPractice.class.php');
/**
 * Class the defines the default enumeration type
 */
class EnumType_PayerType extends EnumType_PerPractice {

	/**
	 * Field info map, array of field names and types to use when editing
	 */
	var $definition = array(
                'enumeration_value_id' => array('type'=>'hidden'),
                'key'     => array('type' => 'hidden'), 
                'value' => array('label'=>'Payer Name','size'=>25),
                'extra1' => array(
                    'label' => 'Filing Code',
                    'size'  => 10
                ),
                'extra2' => array(
                    'label' => 'Value 2',
                    'size'  =>5
                ),
                'sort' => array('label'=>'Order&nbsp;','type'=>'order'),
                'status' => array('label'=>'Enabled','type'=>'boolean')
            );

}
?>
