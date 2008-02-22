<?php
/**
 * Smarty plugin
 * @package com.clear-health.celini
 * @subpackage smarty_plugins
 */


/**
 * Smarty {cms_container} function plugin
 *
 * Type:     function<br>
 * Name:     cms_container<br>
 * Input:<br>
 *           - name       
 * Purpose:  Sets up a named container on a CMS managed page
 *
 *
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_cms_container($params, &$smarty){
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

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

	$container =& ORDataObject::factory('Container',"",$name);
	if (!$container->_populated) {
		$container->set("name",$name);
		$container->persist();	
	}
	
	$module_list =& $container->module_list($page_id);
	
	$base_dir = $smarty->get_template_vars('base_dir');
	$_html_result = "";
	$controller = new Controller();
//	print("Looking for page id $page_id with container named $name\n");
	if (!$GLOBALS['config']['cms']['view_mode'] && $controller->_me->get_id() > 0) {
		if(!isset($GLOBALS['cms']['container_js']) || $GLOBALS['cms']['container_js'] = false){
				$_container_div = '<SCRIPT>function cmsContainerOpenWin(url){ window.open(url, \'editor_win\', \'height=720,width=850\'); }</SCRIPT>'.$_container_div;
				$GLOBALS['cms']['container_js'] = true;
				$_html_result .= $_container_div;	
		}
		$_container_div = "<div id='containerdiv'><a href='javascript:cmsContainerOpenWin(\"" . Celini::link("add","Container",true,$page_id) . "cid=" . $container->get("container_id") . "&in_win=true\")'><img src='{$base_dir}{$entry_file}/images/add.png'></a></div>";
		$_html_result .= $_container_div;
		
	}
	$counter = 0;
	while($module_list->valid()){
		$ml_array = $module_list->get();
		$module = ORDataObject::factory("Module");
		$module->populate_array($ml_array);
//		print("Drawing container on page id $page_id with name $name at index ".$container->get('container_index')."\n");
		if (!$GLOBALS['config']['cms']['view_mode'] && $controller->_me->get_id() > 0) {
			$_editor_divs = "<div id='editdiv'>";			
		
			$_editor_divs .= "<a href='javascript:cmsContainerOpenWin(\"" . Celini::link("edit","Container",true,$module->get('id')) . "in_win=true\")'><img src='{$base_dir}{$entry_file}/images/edit.png'></a>";
			if($counter == 0 && $module_list->numRows() > 1){
				$_editor_divs .= "<a href='" . Celini::link("move","Container",true,$ml_array['link_id']) . "dir=down&process=true&'><img src='{$base_dir}{$entry_file}/images/down.png'></a>";
			}
			elseif ($counter > 0 && $counter != ($module_list->numRows() -1)) {
				$_editor_divs .= "<a href='" . Celini::link("move","Container",true,$ml_array['link_id']) . "dir=up&process=true&'><img src='{$base_dir}{$entry_file}/images/up.png'></a>";
				$_editor_divs .= "<a href='" . Celini::link("move","Container",true,$ml_array['link_id']) . "dir=down&process=true&'><img src='{$base_dir}{$entry_file}/images/down.png'></a>";
			}
			if($counter == ($module_list->numRows() -1) && $module_list->numRows() > 1){
				$_editor_divs .= "<a href='" . Celini::link("move","Container",true,$ml_array['link_id']) . "dir=up&process=true&'><img src='{$base_dir}{$entry_file}/images/up.png'></a>";
	
			}
			$_editor_divs .= "<a href='" . Celini::link("delete","Container",true,$ml_array['link_id']) . "&process=true'><img src='{$base_dir}{$entry_file}/images/deletemodule.png'></a></div>";
			
	    	$_html_result .= $_editor_divs;
		}
		$_html_result .= $module->render();
		$counter++;
		$module_list->next();
	}
	
	
    return $_html_result;

}
/* vim: set expandtab: */

?>
