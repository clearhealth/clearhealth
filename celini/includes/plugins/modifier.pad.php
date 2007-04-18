<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty pad modifier plugin
 *
 * Type:     modifier<br>
 * Name:     pad<br>
 * Purpose:  pad string to specific length with whitespace
 * @param string
 * @param integer
 * @return string
 */
function smarty_modifier_pad($string,$length=10,$end=true,$char=" ")
{
	if (strlen($string) == $length) {
		return $string;	
	}
	else if (strlen($string) > $length) {
		if ($end) {
			return substr($string,0,$length);
		}
		//return right justified section
		return substr($string,strlen($string)-$length);
	}
	else {
		if ($end) {
    		return $string . str_repeat($char, $length - strlen($string));
		}
		//pad beginning of string
		return str_repeat($char, $length - strlen($string)) .$string;
	}	
}

?>
