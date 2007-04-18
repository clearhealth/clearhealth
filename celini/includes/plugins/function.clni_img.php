<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {clni_img} function plugin
 *
 * Creates an image tag for a built in celini image
 *
 * Type:     function<br>
 * Name:     clni_image<br>
 * Input:<br>
 *           - name     image file
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_clni_img($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

    $extra = "";
    $urlOnly = false;

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'name':
                $$_key = (string)$_val;
                break;
            case 'urlOnly':
                    $urlOnly='true';
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
    $link = substr(Celini::link($name,"images",false),0,-1);
    if ($urlOnly) {
            return $link;
    }
    return "<img src=\"$link\" $extra>";
}
/* vim: set expandtab: */
?>
