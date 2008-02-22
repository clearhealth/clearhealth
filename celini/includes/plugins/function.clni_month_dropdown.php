<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */

/**
 * Smarty {clni_month_dropdown} function plugin
 *
 * Type:	 function<br>
 * Name:	 clni_month_dropdown<br>
 * Purpose:  Display a drop down of all of the months
 *
 *
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_clni_month_dropdown($params, &$smarty)
{
	$selected = $params['selected'];
	
	$months = array(
		'January', 'February', 'March', 'April', 'May', 'June', 
		'July', 'August', 'September', 'October', 'November', 'December');
	
	$name = htmlspecialchars($params['name']);
	$html_string = '<select name="' . $name . '" ';
        if (isset($params['disabled'])) {
                $html_string .= ' disabled="disabled" ';
        }
        $html_string .= '>';
	foreach ($months as $month_number => $month) {
		$html_string .= '<option value="' . ($month_number + 1) . '"';
		if (($month_number + 1) == $selected) {
			$html_string .= ' selected="selected"';
		}
		$html_string .= '>' . $month . '</option>';
	}
	$html_string .= '</select>';
	return $html_string;
}
/* vim: set expandtab: */

?>
