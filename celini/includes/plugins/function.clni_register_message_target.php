<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {clni_register_message_target} function plugin
 *
 * Type:     function<br>
 * Name:     clni_register_message_target<br>
 * Input:<br>
 *           - formId       
 *           - targetId
 * Purpose:  
 *           
 *
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_clni_register_message_target($params, &$smarty)
{
    $extra = "";

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'targetId':
            case 'formId':
                $$_key = (string)$_val;
                break;

        }
    }

    if (!isset($targetId) || !isset($formId)) {
        return ''; /* raise error here? */
    }

    $_html_result .= "<script type='text/javascript'>clni_register_message_target('$formId','$targetId');</script>";

    return $_html_result;

}
/* vim: set expandtab: */

?>
