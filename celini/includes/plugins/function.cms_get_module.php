<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {cms_get_module} function plugin
 *
 * Type:     function<br>
 * Name:     cms_get_module<br>
 * Input:<br>
 *           - name       
 * Purpose:  Returns output of another named module inline
 *
 *
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_cms_get_module($params, &$smarty){

	if(!isset($GLOBALS['cms']['current_page_id']) || $GLOBALS['cms']['current_page_id'] <= 0){
		$smarty->trigger_error("CMS Current Page ID Not Found", E_USER_ERROR);
	}
    $name = false;
    
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'name':
                $$_key = (string)$_val;
                break;

            default:
                break;
        }
    }

	$page_id = $GLOBALS['cms']['current_page_id'];
	if(!$name){
		$smarty->trigger_error("name is a required parameter", E_USER_ERROR);
	}

	$module =& ORDataObject::factory('Module');
	$module = $module->module_by_name($name);
	
	if ($module === false) {
		return "Error: Module could not be found: $name";	
	}
	$controller = new Controller();
	$_html_result = "";
	/*if (!$GLOBALS['config']['cms']['view_mode'] && $controller->_me->get_id() > 0) {
			$_editor_divs = "<div id='editdiv'>";			
		
			$_editor_divs .= "<a href='javascript:cmsContainerOpenWin(\"" . Celini::link("edit","Container",true,$module->get('id')) . "in_win=true\")'><img src='{$base_dir}index.php/images/edit.png'></a>";
			if($counter == 0 && $module_list->numRows() > 1){
				$_editor_divs .= "<a href='" . Celini::link("move","Container",true,$ml_array['link_id']) . "dir=down&process=true&'><img src='{$base_dir}index.php/images/down.png'></a>";
			}
			elseif ($counter > 0 && $counter != ($module_list->numRows() -1)) {
				$_editor_divs .= "<a href='" . Celini::link("move","Container",true,$ml_array['link_id']) . "dir=up&process=true&'><img src='{$base_dir}index.php/images/up.png'></a>";
				$_editor_divs .= "<a href='" . Celini::link("move","Container",true,$ml_array['link_id']) . "dir=down&process=true&'><img src='{$base_dir}index.php/images/down.png'></a>";
			}
			if($counter == ($module_list->numRows() -1) && $module_list->numRows() > 1){
				$_editor_divs .= "<a href='" . Celini::link("move","Container",true,$ml_array['link_id']) . "dir=up&process=true&'><img src='{$base_dir}index.php/images/up.png'></a>";
	
			}
			$_editor_divs .= "<a href='" . Celini::link("delete","Container",true,$ml_array['link_id']) . "&process=true'><img src='{$base_dir}index.php/images/deletemodule.png'></a></div>";
			
	    	$_html_result .= $_editor_divs;
	}*/
	
	
    return $_html_result . $module->render();

}
/* vim: set expandtab: */

?>
