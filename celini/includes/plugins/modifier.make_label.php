<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty make_label modifier plugin
 *
 * Type:     modifier<br>
 * Name:     make_label<br>
 * Purpose:  turns a string with _ and all lowercase letters into a formated label
 * @param string
 * @param integer
 * @return string
 */
function smarty_modifier_make_label($string)
{
	return ucfirst(str_replace('_',' ',$string));
}

?>
