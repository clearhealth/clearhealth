<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {input} function plugin
 *
 * Creates an input box of specified type
 *
 * Type:     function<br>
 * Name:     input<br>
 * Input:<br>
 *           - name     name of field
 *           - type     type of field to add (string,date,integer,number)
 *           - value    default value
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_input($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    require_once $smarty->_get_plugin_filepath('function','html_options');

    $extra = "";
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'validation':
            case 'message':
            case 'type':
            case 'value':
            case 'name':
            case 'display':
            case 'separator':
            case 'addempty':
            case 'store_id':
            case 'id':
                $$_key = (string)$_val;
                break;

            case 'system_list':
            case 'enumeration':
                $system_list = (string)$_val;

            case 'options':
                $$_key = $_val;
                break;

            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("input: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    if (isset($smarty->_tpl_vars['data'])) {
            $data = $smarty->_tpl_vars['data']->allData();
            if (isset($data[$name])) {
                    $value = $data[$name]['value'];
            }
    }

           if (!isset($id)) {
                   $id = $name;
           }
        switch($type) {
                case "integer":
                        $ret = "<input type='text' name=\"int[$name]\" value=\"$value\" id=\"$id\"$extra>";
                        if (!isset($validation)) {
                                $validation = "number";
                        }
                break;
                case "checkbox":
                        $checked = "";
                        if ($value == 1) {
                                $checked = " CHECKED";
                        }
                        $ret = "<input type='hidden' name=\"int[$name]\" value=\"0\"><input type='checkbox' name=\"int[$name]\" value=\"1\" id=\"$id\"$checked$extra>";
                break;
                case "checkbox-table":
                        $checked = "";
                        if ($value == 1) {
                                $checked = " checked=\"checked\"";
                        }
                        $ret = "<input type='hidden' name=\"$name\" value=\"0\"><input type='checkbox' name=\"$name\" value=\"1\" id=\"$id\"$checked$extra>";
                break;
                case "string":
                        $ret = "<input type='text' name=\"string[$name]\" value=\"$value\" id=\"$id\"$extra>";
                break;
                case "hidden":
                        $ret = "<input type='hidden' name=\"string[$name]\" value=\"$value\" id=\"$id\"$extra>";
                break;
                case "text":
                        $ret = "<textarea name=\"text[$name]\" id=\"$id\"$extra>$value</textarea>";
                break;
                case "submit":
                       $ret = "<input type='submit' name=\"$name\" value=\"".$value."\" id=\"$id\"$extra>";
                break;
                case "date":
                        $ret = smarty_function_clni_input_date(array('name'=>"date[$name]",'value'=>$value,'extra'=>$extra,'id'=>$id),$smarty);
                break;
                case "date-table":
                        $ret = smarty_function_clni_input_date(array('name'=>$name,'value'=>$value,'extra'=>$extra,'id'=>$id),$smarty);
                break;
                case "select":
                case "select-table":
                case "radio":
                case "multiselect":
                        if (!is_array($options)) {
                                $tmp = explode(',',$options);
                                $options = array();
                                if(isset($addempty)) {
                                	$options[''] = $addempty;
                                }
                                foreach($tmp as $o) {
                                        $t = explode("=",$o);
                                        $options[$t[0]] = $t[1];
                                }
                        }
                        $sep = isset($separator) ? $separator : "<br>";
                        if (isset($display) && $display == 'horizontal') {
                                $sep = isset($separator) ? $separator : '&nbsp;';
                        }
                        if (isset($system_list)) {
                                $e =& ORDataObject::factory('Enumeration');
                                $tmp = $e->get_enum_list($system_list);
                                $options = array();
                                if ($store_id) {
                                $options = $tmp;
                                }
                                else {
                                foreach($tmp as $val) {
                                        $options[$val] = $val;
                                }
                                }
                        }
                        if ($type == 'radio') {
                                $ret = "<div>";
                                foreach($options as $key => $val) {
                                        $checked = '';
                                        if ($key == $value) {
                                                $checked = "checked='checked'";
                                        }
                                        $ret .= "<input type='radio' name='string[$name]' value='$key' id='{$id}_{$key}' $checked><label for='{$id}_{$key}' class='inline'>$key</label>$sep";

                                }
                                $ret .= "</div>";
                        }
                        else if ($type == 'multiselect') {
                                $ret = "<div>";
                                foreach($options as $key => $val) {
                                        $ret .= "<input type='hidden' value='' name='string[{$name}_$key]'><input type='checkbox' name='string[{$name}_$key]' value='$key' id='{$id}_{$key}'><label for='{$id}_{$key}' class='inline'>$val</label>$sep";

                                }
                                $ret .= "</div>";
                        }
                        else if ($type == "select-table") {
                                $ret = smarty_function_html_options(array('name'=>$name,'selected'=>$value,'options'=>$options,'id'=>$id,'extra'=>$extra),$smarty);
                        }
                        else {
                                $ret = smarty_function_html_options(array('name'=>"string[$name]",'selected'=>$value,'options'=>$options,'id'=>$id,'extra'=>$extra),$smarty);
                        }
                break;
        }
        if (isset($validation)) {
                $tmp = array('rules'=>$validation,'id'=>$id);
                if (isset($message)) {
                        $tmp['message'] = $message;
                }
                $ret .= smarty_function_clni_register_validation($tmp,$smarty);
        }

        return $ret;
}
/* vim: set expandtab: */
?>
