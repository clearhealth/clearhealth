<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty array/string length
 *
 * Type:     modifier<br>
 * Name:     length<br>
 * Purpose:  returns number of items in an array (or zero if an array is not passed)
 * @param array
 * @return string
 */
function smarty_modifier_count_array($array='')
{
	if(is_array($array)) {
		return count($array);
	}
	return 0;
}

?>