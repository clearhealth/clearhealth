<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {link} function plugin
 *
 * Creates a Celini url
 *
 * Type:     function<br>
 * Name:     link<br>
 * Input:<br>
 *           - action
 *           - controller
 *           - pageType
 *           - defaultArg
 *           - managerArg
 *           - basePath
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_link($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

    $action = true;
    $controller = true;
    $pageType = true;
    $defaultArg = false;
    $managerArg = false;
    $basePath = true;

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'action':
            case 'controller':
            case 'defaultArg':
            case 'managerArg':
            case 'basePath':
                $$_key = (string)$_val;
                break;
            case 'pageType':
                if(empty($_val)){
                        $pageType = false;
                }else{
                        $pageType = $_val;
                }
                break;
        }
    }


        return Celini::link($action,$controller,$pageType,$defaultArg,$managerArg,$basePath);
}
/* vim: set expandtab: */
?>
