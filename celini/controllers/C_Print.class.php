<?php
$loader->requireOnce('controllers/C_PageType.abstract.php');

/**
 * Provides a basic "print" view for any action by removing all navigation and loading a custom
 * print.css file.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @package com.uversainc.celini
 */
class C_Print extends C_PageType
{
	function C_Print() {
		parent::Controller();
	}
}

