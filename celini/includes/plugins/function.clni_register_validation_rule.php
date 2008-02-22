<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {clni_register_validation_rule} function plugin
 *
 * Type:     function<br>
 * Name:     clni_register_validation_rule<br>
 * Input:<br>
 *           - id       
 *           - rule             (optional) default required
 *           - notification     (optional) default alert
 *           - message          (optional) 
 * Purpose:  
 *           
 *
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_clni_register_validation_rule($params, &$smarty)
{
    $id = false;
    $rule = 'required';
    $notification = false;
    $message = false;
    
    $extra = "";

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'id':
                $$_key = (string)$_val;
                break;

            case 'rule':
                $$_key = (string)$_val;
                break;

            case 'notification':
            case 'validation':
                $$_notification = (string)$_val;
                break;

            case 'message':
                $$_key = addslashes((string)$_val);
                break;

        }
    }

    if (!isset($id)) {
        return ''; /* raise error here? */
    }

    if ($message !== false) {
        if ($notification == false) {
                $notification = "messageAlert";
        }
        $_html_result .= "<script type='text/javascript'>clni_register_validation_rule('$id','$rule','$notification','$message');</script>";
    }
    else {
        if ($notification == false) {
                $notification = "alert";
        }
        $_html_result .= "<script type='text/javascript'>clni_register_validation_rule('$id','$rule','$notification');</script>";
    }

    return $_html_result;

}
/* vim: set expandtab: */

?>
