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
 *           - name
 *           - value
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_enum_lookup($params, &$smarty)
{
    $args = array();

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'name':
                    $$_key = (string)$_val;
                    break;
            case 'value':
                    $$_key = (string)$_val;
                break;
        }
    }

    if (!isset($GLOBALS['_enum_lookup'])) {
            $GLOBALS['_enum_lookup'] =& ORDataOBject::factory('Enumeration');
    }
    $lookup = $GLOBALS['_enum_lookup']->get_enum_list($name);
    if (isset($lookup[$value])) {
            return $lookup[$value];
    }
    return $value;

}
/* vim: set expandtab: */
?>
