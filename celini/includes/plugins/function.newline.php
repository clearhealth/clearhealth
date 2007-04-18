<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {newline} function plugin
 *
 * Type:     function<br>
 * Name:     newline<br>
 * 
 * Input:<br>
 *           - number of newlines   
 *           
 * Purpose:  
 *           Return one or more newlines
 *
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_newline($params, &$smarty)
{
    $string = "";
    $lines = 1;
    
    if (isset($params['lines'])) {
    	$lines = $params['lines'];	
    }
    
    for($i=0;$i<$lines;$i++) {
    	$string .= "\n";	
    }
    
    return $string;

}
/* vim: set expandtab: */

?>
