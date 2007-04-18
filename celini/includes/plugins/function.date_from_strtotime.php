<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {date_from_strtotime} function plugin
 *
 * Creates a date based on whatever was passed in as <i>string</i>
 *
 * Type:	 function<br>
 * Name:	 date_from_strtotime<br>
 * Input:<br>
 *		   - string  The string you want to pass to strtotime()
 *		   - format  The format string you want to pass to date()
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_date_from_strtotime($params, &$smarty)
{
	foreach($params as $_key => $_val) {
		switch($_key) {
			case 'string':
			case 'format':
				$$_key = (string)$_val;
				break;
		}
	}

	return date($format, strtotime($string));
}
/* vim: set expandtab: */
?>
