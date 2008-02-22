<?php
/**
 * Tokenizer for X12 data, implements an SPL iterator interface
 *
 * @package com.clear-health.x12
 * @author Joshua Eichorn <jeichorn@mail.com>
 */
class X12Tokenizer {

	var $reader;

	var $tokens;

	function setReader($r) {
		$this->reader = $r;
	}

	function parse() {
		$content = $this->reader->readContents();

		$this->tokens = preg_split('/([*~])[\n\r]*/',$content,0,PREG_SPLIT_DELIM_CAPTURE);

		if (!is_array($this->tokens) || count($this->tokens) == 0) {
			trigger_error("No tokens creates from input");
		}

		$token = array_pop($this->tokens);
		if (!empty($token)) {
			trigger_error("X12 format error, trailing data after the last line: '$token'");
		}
	}

	function rewind() {
		reset($this->tokens);
	}

	function next() {
		next($this->tokens);
	}

	function valid() {
		if (!is_null(key($this->tokens))) {
			return true;
		}
		return false;
	}

	function current() {
		return current($this->tokens);
	}

	function key() {
		return key($this->tokens);
	}
}
?>
