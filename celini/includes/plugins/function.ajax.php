<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {ajax} function plugin
 *
 * Super generic wrapper around HTML_AJAX_Helper class
 *
 * Your always dealing with the same Helper class, it will be created if needed, but if AJAX_HELPER is already set that instance will be used instead
 *
 * Type:     function<br>
 * Name:     ajax<br>
 * Input:<br>
 *           - function
 *           - the rest of the params in order name them whaterver you want
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_ajax($params, &$smarty)
{
    $function = false;
    $args = array();
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'action':
            case 'function':
                $function = (string)$_val;
                break;
            default:
                $args[] = $_val;
                break;
        }
    }

    if ($function != false) {
            $helper =& Celini::ajaxInstance();
            return call_user_func_array(array(&$helper,$function),$args);
    }
}
/* vim: set expandtab: */
?>
