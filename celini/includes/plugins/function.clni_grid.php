<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {clni_grid} function plugin
 *
 * Type:     function<br>
 * Name:     clni_grid<br>
 * Purpose:  Builds a grid given a datasource name and options
 *
 *
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_clni_grid($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    $name = false;
    $value = false;
    
    $extra = "";

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'datasource':
                $$_key = (string)$_val;
                break;

            case 'arg1':
                $$_key = (string)$_val;
                break;
            case 'arg2':
                $$_key = (string)$_val;
                break;
            case 'arg3':
                $$_key = (string)$_val;
                break;

            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("clni_grid: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    $_html_result = '';
        
    if (!isset($arg1)) $arg1 = '';
    if (!isset($arg2)) $arg2 = '';
    if (!isset($arg3)) $arg3 = '';
    
    $GLOBALS['loader']->requireOnce('datasources/' . $datasource . '.class.php');
    $anonDS = new $datasource($arg1,$arg2,$arg3);
    $anonGrid = new cGrid($anonDS);
    $anonGrid->name = $datasource . "Grid";
    $_html_result = $anonGrid->render();

    return $_html_result;

}
/* vim: set expandtab: */

?>
