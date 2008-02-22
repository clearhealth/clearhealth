<?php
$loader->requireOnce('includes/X12Reader.abstract.php');

/**
 * Supplies a basic api to read the contents of a file
 *
 * @package com.clear-health.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 * @todo	Create a base reader class to define the api when other reader types are added
 */
class X12FileReader extends X12Reader {
	var $file;

	function X12FileReader($file) {
		$this->file = $file;
	}

	function readContents() {
		$contents = trim(file_get_contents($this->file));
		return $contents;
	}
}
?>
