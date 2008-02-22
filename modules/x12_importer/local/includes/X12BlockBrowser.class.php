<?php

/**
 * A 'browser' for a given block of an X12 document
 *
 * @package com.clear-health.x12
 */
class X12BlockBrowser
{
	var $_block = null;
	
	function X12BlockBrowser($block) {
		$this->_block = $block;
	}
	
	
	/**
	 * Returns the regular X12 formatted string that this block represents
	 *
	 * @return string
	 */
	function toString() {
		$array = array($this->_block->code);
		foreach ($this->_block->fields as $field) {
			$array[] = $field->value;
		}
		
		return implode('*', $array);
	}
	
	
	/**
	 * Return the {@link X12Field} at a given position.
	 *
	 * <i>$pos</i> should match the code number (i.e., SV103 would be requested as 3)
	 *
	 * @param  int $pos
	 * @return X12Field|false
	 */
	function getFieldByPosition($pos) {
		$keys = array_keys($this->_block->fields);
		$posKey = $keys[($pos - 1)];
		if (!isset($this->_block->fields[$posKey])) {
			return false;
		}
		
		return $this->_block->fields[$posKey];
	}
	
	function getFieldValueByPosition($pos) {
		$field = $this->getFieldByPosition($pos);
		return $field->value();
	}
}

?>
