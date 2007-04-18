<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {query_options} function plugin
 *
 * Creates a select options from an sql query
 *
 * Type:     function<br>
 * Name:     query_options<br>
 * Input:<br>
 *           - name
 *           - selected
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_query_options($params, &$smarty)
{
        require_once $smarty->_get_plugin_filepath('function','html_options');
    $args = array();
    $lookup = false;

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'query':
                    $$_key = (string)$_val;
                    break;
            case 'selected':
                    $args[$_key] = $_val;
                    $$_key = (string)$_val;
                break;
            case 'lookup':
                    $$_key = (bool)$_val;
                break;
        }
    }

    $enum =& ORDataOBject::factory('Enumeration');

    $res = $enum->_execute($query,ADODB_FETCH_NUM);
    $options = array();
    while($res && !$res->EOF) {
            $options[$res->fields[0]] = $res->fields[1];
            $res->MoveNext();
    }
    $enum->_db->setFetchMode($mode);

    $args['options'] = $options;

    if ($lookup) {
            if (isset($options[$selected])) {
                    return $options[$selected];
            }
    }
    else {
            return smarty_function_html_options($args,$smarty);
    }

}
/* vim: set expandtab: */
?>
