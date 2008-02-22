<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
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
function smarty_function_sqlenum_lookup($params, &$smarty)
{
	require_once $smarty->_get_plugin_filepath('function','html_options');
    $args = array();
    $lookup = false;

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'table':
                    $$_key = (string)$_val;
                    break;
            case 'column':
                    $args[$_key] = $_val;
                    $$_key = (string)$_val;
                break;
            case 'lookup':
                    $$_key = (bool)$_val;
                break;
			case 'hide' :
				$$_key = explode(',', $_val);
				break;
        }
    }
	
	$db =& new clniDB();
	$result = $db->execute('SHOW COLUMNS FROM ' . $table . ' LIKE ' . $db->quote($column), ADODB_FETCH_NUM);
	$options = explode("','", preg_replace("/(enum|set)\('(.+?)'\)/","\\2", $result->fields[1]));

    if ($lookup) {
		if (isset($options[$selected])) {
			return $options[$selected];
		}
    }
    else {
		if (count($hide) > 0) {
			foreach ($hide as $valueToHide) {
				if (($keyToHide = in_array($valueToHide, $options)) !== false) {
					unset($options[$keyToHide]);
				}
			}
		}
		return smarty_function_html_options(array('output' => $options, 'values' => $options), $smarty);
    }

}
/* vim: set expandtab: */
?>
