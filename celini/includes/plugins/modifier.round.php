<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty round modifier plugin
 *
 * Type:     modifier<br>
 * Name:     round<br>
 * Purpose:  round a numeric string to a given decimal point
 * @param string
 * @param integer
 * @param boolean
 * @return string
 */
function smarty_modifier_round($float, $precision = 0, $trueRound = false)
{
	$roundedFloat = round((float)$float, $precision);
	return $trueRound ? $roundedFloat : number_format($roundedFloat, $precision);
}

?>
