<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {clni_form} function plugin
 *
 * Type:     function<br>
 * Name:     clni_form<br>
 * Input:<br>
 *           - action   
 *           - id       (optional) default h3sForm
 *           - class    (optional) default  
 *           - method   (optional) default post
 *           - enctype  (optional) default multipart/form-data 
 *           - onsubmit (optional) default 'return clni_validate()'
 * Purpose:  
 *           Creates a form tag
 *
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_clni_form($params, &$smarty)
{
        require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

    $action = false;
    $id = 'h3sform';
    $class = 'generalForm';
    $method = 'post';
    $enctype = 'multipart/form-data';
    $onsubmit = 'return clni_validate(this)';
    $process = false;
    
    $extra = "";

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'action':
                $$_key = (string)$_val;
                break;

            case 'id':
                $$_key = (string)$_val;
                break;

            case 'class':
                $$_key = (string)$_val;
                break;

            case 'method':
                $$_key = (string)$_val;
                break;

            case 'enctype':
                $$_key = (string)$_val;
                break;

            case 'onsubmit':
                $$_key = (string)$_val;
                break;

            case 'process':
                $$_key = (string)$_val;
                break;


            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("html_options: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;

        }
    }

    if ($action === false) {
            if(isset($smarty->_tpl_vars['FORM_ACTION'])) {
                    $action = $smarty->_tpl_vars['FORM_ACTION'];
            }
    }
    if ($process === false) {
            if(isset($smarty->_tpl_vars['PROCESS'])) {
                    $process = $smarty->_tpl_vars['PROCESS'];
            }
    }

    $_html_result .= "<form id='$id' class='$class' method='$method' action='$action' enctype='$enctype' onsubmit='$onsubmit'$extra>\n";
    if ($process) {
            $_html_result .= "<input type='hidden' name='process' value='$process'>\n";
    }
    
    return $_html_result;

}
/* vim: set expandtab: */

?>
