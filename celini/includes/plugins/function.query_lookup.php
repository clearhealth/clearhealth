<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {enum_lookup} function plugin
 *
 * Replace a system enum key with a value
 *
 * Type:     function<br>
 * Name:     enum_lookup<br>
 * Input:<br>
 *           - value
 *           - query
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_query_lookup($params, &$smarty)
{
    $args = array();

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'query':
                    $$_key = (string)$_val;
                    break;
            case 'value':
                    $$_key = (string)$_val;
                break;
        }
    }

    $cacheKey = md5($query);

    if (!isset($GLOBALS['smarty_function_query_lookup'])) {
            $GLOBALS['smarty_function_query_lookup'] = array();
    }
    if (!isset($GLOBALS['smarty_function_query_lookup'][$cacheKey])) {
            $db = Celini::DbInstance();
            $GLOBALS['smarty_function_query_lookup'][$cacheKey] = $db->GetAssoc($query);
    }



    if (isset($GLOBALS['smarty_function_query_lookup'][$cacheKey][$value])) {
            return $GLOBALS['smarty_function_query_lookup'][$cacheKey][$value];
    }
    return $value;

}
/* vim: set expandtab: */
?>
