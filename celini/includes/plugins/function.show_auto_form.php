<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {show_auto_form} function plugin
 *
 * shows an auto form created by new_auto_form
 *
 * Type:     function<br>
 * Name:     show_auto_form<br>
 * Input:<br>
 *           - id       
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_show_auto_form($params, &$smarty)
{

    $id = false;
    
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'id':
                $$_key = (string)$_val;
                break;
        }
    }

	return $GLOBALS['autoform'][$id]->toHtml();

}
/* vim: set expandtab: */
?>
