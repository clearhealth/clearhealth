<?php
$loader->requireOnce('includes/EnumType/PerPractice.class.php');
/**
 * Class the defines the default enumeration type
 */
class EnumType_FPL extends EnumType_PerPractice {

	/**
	 * Field info map, array of field names and types to use when editing
	 */
	var $definition = array(
                'enumeration_value_id' => array('type'=>'hidden'),
                'key'     => array('type' => 'hidden'), 
                'value' => array('label'=>'FPL','size'=>5),
                'extra1' => array(
                    'label' => 'Visit Fee&nbsp;',
                    'size'  => 5
                ),
                'extra2' => array(
                    'label' => 'Proc. Fee&nbsp;',
                    'size'  =>5
                ),
                'sort' => array('label'=>'Order&nbsp;','type'=>'order'),
                'status' => array('label'=>'Enabled','type'=>'boolean')
            );

}
?>
