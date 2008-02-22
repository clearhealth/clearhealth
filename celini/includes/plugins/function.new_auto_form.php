<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {auto_form} function plugin
 *
 * Creates a form from an Celini ORDataObject
 *
 * Type:     function<br>
 * Name:     auto_form<br>
 * Input:<br>
 *           - action   
 *           - id       
 *           - object       
 *           - class    (optional) default  
 *           - method   (optional) default post
 *           - enctype  (optional) default multipart/form-data 
 *           - onsubmit (optional) default 'return clni_validate()'
 *           - submit   (optional) default true
 *           - form     (optional) default true
 * Purpose:  
 *           Creates a form tag
 *
 *
 * @param array
 * @param Smarty
 * @return string
 */
$GLOBALS['loader']->requireOnce('includes/AutoForm.class.php');
function smarty_function_new_auto_form($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

    $action = false;
    $id = false;
    $extra = "";
    $submit = true;
    $form = true;

    $call = array();
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'id':
                $$_key = (string)$_val;
                $call[$_key] = (string)$_val;
                break;

            case 'array':
                $$_key = (string)$_val;
                break;

            case 'object':
                $$_key = $_val;
                break;

            case 'submit':
                $$_key = (boolean)$_val;
                break;

            case 'form':
                $$_key = (boolean)$_val;
                break;

            default:
                $call[$_key] = (string)$_val;
                break;
        }
    }

    $GLOBALS['autoform'][$id] = new AutoForm();
    $GLOBALS['autoform'][$id]->object = $object;
    $GLOBALS['autoform'][$id]->id = $params["id"];
    $GLOBALS['autoform'][$id]->smarty = $smarty;
    $GLOBALS['autoform'][$id]->extra = $extra;
    if ($form) {
	    $GLOBALS['autoform'][$id]->header = smarty_function_clni_form($call,$smarty);
    }
    else {
	    $GLOBALS['autoform'][$id]->footer = "";
    }
    $GLOBALS['autoform'][$id]->addSubmit = $submit;
    return '';

}
/* vim: set expandtab: */

?>
