<?php
/**
 * Class holding a single row of enumeration values
 *
 * This mainly exists to gives us an extension point for custom types
 *
 * @package com.clear-health.celini
 */
class EnumValue {

	/**
	 * Key of the enum
	 */
	var $key;

	/**
	 * Value of the enum
	 */
	var $value;

	function EnumValue($data) {
		foreach($data as $key => $val) {
			$this->$key = $val;
		}
	}
}
?>
