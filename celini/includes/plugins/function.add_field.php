<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {add_field} function plugin
 *
 * Adds and extra field to an auto form
 *
 * Type:     function<br>
 * Name:     add_field<br>
 * Input:<br>
 *           - id   form to add to    
 *           - type     type of field to add (input,textarea,file)
 *           - lable    field label
 *           - value    default value
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_add_field($params, &$smarty)
{

    $id = false;
    
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'id':
                $$_key = (string)$_val;
                break;

            case 'type':
                $$_key = (string)$_val;
                break;

            case 'label':
                $$_key = (string)$_val;
                break;

            case 'value':
                $$_key = (string)$_val;
                break;
        }
    }

	return $GLOBALS['autoform'][$id]->addField($type,$label,$value);

}
/* vim: set expandtab: */
?>
