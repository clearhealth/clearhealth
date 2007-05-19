<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {clni_grid} function plugin
 *
 * Type:     function<br>
 * Name:     clni_grid<br>
 * Purpose:  displays a widget form box
 *
 *
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_clni_widget_form($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    $name = false;
    $value = false;
    
    $extra = "";

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'patientId':
                $$_key = (int)$_val;
                break;
            case 'name':
                $$_key = (string)$_val;
                break;
            case 'externalId':
                $$_key = (int)$_val;
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
        
    if (!isset($patientId)) $patientId = 0;
    if (!isset($name)) $name = '';
    if (!isset($externalId)) $externalId = '';
    $GLOBALS['loader']->requireOnce('controllers/C_WidgetForm.class.php');
    $cwf = new C_WidgetForm();
    $display = $cwf->actionShowSingle_view($patientId,$name,$externalId);
    $_html_result = $display;

    return $_html_result;

}
/* vim: set expandtab: */

?>
