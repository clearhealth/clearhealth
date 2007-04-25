<?php
/**
 * Supplies a basic api to read the contents of a file
 *
 * @package com.uversainc.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class X12StringReader {
	var $string;

	function X12StringReader($s) {
		$this->string = $s;
	}

	function readContents() {
		return $this->string;
	}
}
?>
