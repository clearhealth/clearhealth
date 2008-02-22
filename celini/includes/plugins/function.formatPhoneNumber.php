<?php
// $Id: function.formatPhoneNumber.php 1019 2006-02-09 21:08:43Z cpowers $
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */


/**
 * Changes the format of a phone number from 1112223333 to (111) 222-3333 for
 * display purposes.
 *
 * This plug-in makes the assumption that if a number already has some sort of
 * format to it, that it shouldn't be messed with.
 *
 * @param array
 * @param {@link Smarty}
 * @return string
 */
function smarty_function_formatPhoneNumber($params, &$smarty)
{
    if (preg_match('/^([0-9]{3})([0-9]{3})([0-9]{4})$/', $params['number'], $matches)) {
		return sprintf('%s-%s-%s', $matches[1], $matches[2], $matches[3]);
	} else {
		return $params['number'];
	}
}

?>
