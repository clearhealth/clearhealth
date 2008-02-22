<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {clni_section_links} function plugin
 *
 * Type:     function<br>
 * Name:     clni_section_links<br>
 * Input:<br>
 *           - name       
 *           - value      (optional)
 * Purpose:  Prints a link block to all other sections on the page
 *           Put a name and value in the call to register a section, leave out value to print a block
 *
 *
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_clni_section_links($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    $name = false;
    $value = false;
    
    $extra = "";

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'name':
                $$_key = (string)$_val;
                break;

            case 'value':
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

    if (!isset($name) && !isset($value))
        return ''; /* raise error here? */

    if ($name !== false && $value !== false) {
            $GLOBALS['clni_section_links'][$name] = $value;
            return "";
    }

    $links = array();
    if (isset($GLOBALS['clni_section_links'])) {
            $links = $GLOBALS['clni_section_links'];
    }

    $_html_result = '<span class="sectionLinks">';
    foreach($links as $n => $v) {
            if ($n !== $name) {
                    $_html_result .= " [<a href='#$n'>$v</a>] ";
            }
    }
    $_html_result .= "</span>\n";

    return $_html_result;

}
/* vim: set expandtab: */

?>
