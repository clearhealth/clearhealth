<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {clni_register_validation} function plugin
 * Allows for mass registration within one javascript block
 *
 * basic syntax is rule|rule
 * extended syntax is rule(notification:'alert',message:'hello world)|rule(message:'my message)
 *
 * Type:     function<br>
 * Name:     clni_register_validation_rule<br>
 * Input:<br>
 *           - id       
 *           - rules             (optional) default required
 * Purpose:  
 *           
 *
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_clni_register_validation($params, &$smarty)
{
    $id = false;
    $notification = false;
    $message = false;
    
    $extra = "";

	foreach($params as $_key => $_val) {
		switch($_key) {
			case 'message':
			case 'id':
				$$_key = (string)$_val;
				break;
			case 'rule':
			case 'rules':
				$rules = (string)$_val;
				break;
        }
    }

    if (!isset($id)) {
        return ''; /* raise error here? */
    }

    $_html_result .= "<script type='text/javascript'>\n";
    $tmp = explode('|',$rules);
    foreach($tmp as $rule) {
        if (preg_match('/(.+)\((.+)\)/',$rule,$match)) {
                $_html_result .= "clni_register_validation_rule_hash({id:'$id',rule:'$match[1]',".stripslashes($match[2])."});\n";
        }
        else {
                if ($message) {
                        $msg = ",message:'$message'";
                }
                $_html_result .= "clni_register_validation_rule_hash({id:'$id',rule:'$rule'$msg});\n";
        }
    }

    return $_html_result."</script>\n";

}
/* vim: set expandtab: */

?>
