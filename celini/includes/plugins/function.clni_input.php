<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {clni_input} function plugin
 *
 * Creates an input box of specified type
 *
 * Type:     function<br>
 * Name:     clni_input<br>
 * Input:<br>
 *           - name     name of field
 *           - type     type of field to add (string,date,integer,number)
 *           - value    default value
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_clni_input($params, &$smarty){
	require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

	$extra = "";

	foreach($params as $_key => $_val) {
		switch($_key) {
			case 'type':
				$$_key = (string)$_val;
				break;

			case 'value':
				$$_key = (string)$_val;
				break;

			case 'name':
				$$_key = (string)$_val;
				break;

			default:
				if(!is_array($_val)) {
					$extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
				}else{
					$smarty->trigger_error("html_options: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
				}
				break;
		}
	}


	switch($type) {
		case "string":
			return "<input type='text' name=\"$name\" value=\"$value\"$extra>";
			break;
		case "submit":
			return "<input type='submit' name=\"$name\" value=\"$value\"$extra>";
			break;
		case "text":
			return "<textarea name=\"$name\" id=\"$id\"$extra>$value</textarea>";
			break;
		case "date":
			return smarty_function_clni_input_date(array('name'=>"$name",'value'=>$value,'extra'=>$extra),$smarty);
			break;
	}
}
/* vim: set expandtab: */
?>