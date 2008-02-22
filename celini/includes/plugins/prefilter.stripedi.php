<?php
/**
 * Smarty prefilter plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */
                                                                                
                                                                                
/**
 * Strip edi prefilter
 *
 * Type:     prefilter
 * Name:     strip_edi<br>
 * @author   David Uhlman <duhlman@uversainc.com>
 * @version  1.0
 * @param string
 * @return string
 */
 
 function smarty_prefilter_stripedi($text = "", &$smarty) {
 	
 	//find whitespace after tildes and also remove comments if applicable, smarty normally removes comments but would leave the trailing whitespace on those lines
 	
 	$text = preg_replace('/((\s+)?)\n/','',$text);
 	$text = preg_replace('/((\s+)?){\*(.*?)\*\}((\s+)?)/','',$text);
 	//$text = preg_replace('/\{\*(.*)((\n)?)(.*)\*\}((\s+)?)/i','',$text);
 	
 	//$text = preg_replace('/((((?<=\~)|(?<=\*))\s+)?)(({\*.*\*}\s+)?)/i', '', $text);
 	return $text;
 	
 }
 
/* vim: set expandtab: */


?>
