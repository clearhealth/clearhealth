<?php
/**
 * Smarty plugin
 * @package com.uversainc.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {clni_grid} function plugin
 *
 * Type:     function<br>
 * Name:     clni_grid<br>
 * Purpose:  Builds a grid given a datasource name and options
 *
 *
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_clni_prescriber_list($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');    require_once $smarty->_get_plugin_filepath('function','html_options');
    
    $name = false;
    $value = false;
    
    $extra = "";

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'name':
            case 'value':
            case 'extra':
            case 'id':
            case 'blank':
            case 'onchange':
                $$_key = (string)$_val;
                break;
            case 'disabled':
                                $$_key = (string)$_val;
            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("clni_grid: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
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
    $db =         
    $sql = "select per.person_id,
CONCAT(per.last_name,', ',per.first_name,' ', per.middle_name) as prescriberName 
from provider prov
inner join person per on per.person_id = prov.person_id
where ePrescribeEnabled =1
order by per.last_name, per.first_name";
	$db =& Celini::dbInstance();
	$res = $db->execute($sql);
	$prescribers = array();
	if ($blank == true) {
		$prescribers[0] = " ";
	}
	while ($res && !$res->EOF) {
		$prescribers[$res->fields['person_id']] = $res->fields['prescriberName'];
		$res->MoveNext();
	}
    $ret = smarty_function_html_options(array('name'=>"string[$name]",'selected'=>$value,'options'=>$prescribers,'id'=>$id,'extra'=>$extra,'onchange'=>$onchange,'disabled'=>$disabled),$smarty);

    return $ret;

}
/* vim: set expandtab: */

?>
