<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {submit} function plugin
 *
 * Creates a submit box
 *
 * Type:     function<br>
 * Name:     submit<br>
 * Input:<br>
 *           - label    optional
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_submit($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

    $extra = "";
    $label = "Update";

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'label':
                $$_key = (string)$_val;
                break;

            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("html_options: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }


        return "<input type='submit' value=\"".$label."\"$extra>";
}
/* vim: set expandtab: */
?>
