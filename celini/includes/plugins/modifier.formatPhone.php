<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Changes the format of a phone number from 1112223333 to (111) 222-3333 for
 * display purposes.
 * Removes formatting if present on supplied number, makes no changes if 000-000-0000 or 000-0000 number cannot be constructed
 *
 * @param array
 * @return string
 */
function smarty_modifier_formatPhone($number)
{
	$number = preg_replace('/[^0-9]/','',$number);
	if (preg_match('/^([0-9]{3})([0-9]{3})([0-9]{4})([0-9]+)$/', $number, $matches)) {
		return sprintf('(%s) %s-%s x%s', $matches[1], $matches[2], $matches[3], $matches[4]);
	}
	if (preg_match('/^([0-9]{3})([0-9]{3})([0-9]{4})$/', $number, $matches)) {
		return sprintf('(%s) %s-%s', $matches[1], $matches[2], $matches[3]);
	}
	elseif (preg_match('/^([0-9]{3})([0-9]{4})$/', $number, $matches)) {
		return sprintf('%s-%s', $matches[2], $matches[3]);
	} else {
		return $params['number'];
	}
}

?>
