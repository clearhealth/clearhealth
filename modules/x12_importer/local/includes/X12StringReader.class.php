<?php
/**
 * Supplies a basic api to read the contents of a file
 *
 * @package com.clear-health.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class X12StringReader {
	var $string;

	function X12StringReader($s) {
		$this->string = $s;
	}

	function readContents() {
		//strip weird line endings
		$string = str_replace("\x0D\x0A","",$this->string);
		$string = preg_replace("/\r\n/","",$string);
		//add line endings
		$string = preg_replace('/~/',"~\r\n",$string);
		return $string;
	}
}
?>
