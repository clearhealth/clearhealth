<?php
$GLOBALS['loader']->requireOnce('/includes/EnumManager.class.php');
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {enum_options} function plugin
 *
 * Creates a select options from a system enum
 *
 * Type:     function<br>
 * Name:     enum_options<br>
 * Input:<br>
 *           - name
 *           - selected
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_enum_options($params, &$smarty){
	require_once $smarty->_get_plugin_filepath('function','html_options');
	$args = array();
	$name = '';

	foreach($params as $_key => $_val) {
		switch($_key) {
			case 'name':
				$$_key = (string)$_val;
				break;
			case 'selected':
				$args[$_key] = $_val;
				break;
		}
	}

	$enumManager =& EnumManager::getInstance();
	$args['options'] = $enumManager->enumArray($name);
	return smarty_function_html_options($args,$smarty);
}
/* vim: set expandtab: */
?>
