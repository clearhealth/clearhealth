<?php
$loader->requireOnce('includes/X12TokenIterator.class.php');
/**
 * Tokenizer for X12 data, implements an SPL iterator interface
 *
 * @package com.clear-health.x12
 * @author Joshua Eichorn <jeichorn@mail.com>
 */
class X12Tokenizer {
	/**
	 * The {@link X12Reader} that we're tokenizing
	 *
	 * @var X12Reader
	 * @see setReader(), X12Reader
	 */
	var $reader;
	
	
	/**
	 * Sets the {@link X12Reader} for this
	 *
	 * @param X12Reader
	 * @see X12Reader
	 */
	function setReader($r) {
		//assert('$r instanceof X12Reader');
		$this->reader = $r;
	}
	
	
	/**
	 * Tokenizes the file passed in via {@link setReader()} and returns an 
	 * {@link X12TokenIterator}
	 *
	 * @return X12TokenIterator
	 */
	function parse() {
		$content = trim($this->reader->readContents());

		$tokens = preg_split('/([*~])[\n\r]*/',$content,0,PREG_SPLIT_DELIM_CAPTURE);

		if (!is_array($tokens) || count($tokens) == 0) {
			trigger_error("No tokens creates from input");
		}

		$token = array_pop($tokens);
		if (!empty($token)) {
			trigger_error("X12 format error, trailing data after the last line: '$token'");
		}
		
		return new X12TokenIterator($tokens);
	}
}
?>
