<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

/**
 * @package com.uversainc.celini
 */
class WidgetForm_DS extends Datasource_Sql {
	
	var $primaryKey = 'widget_form_id';
	var $_internalName = 'WidgetForm_List_DS';
	var $_widgetTypes = 0;

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function WidgetForm_DS($type = '',$widgetFormName = '') {
		$type = preg_replace('/[^0-9,*]*/','',$type); 
		$db = Celini::dbInstance();
		$where = '1 ';
		if (!empty($type) && $type !="*") {
			$where .= " and wf.type in (".$type.")";
		}
		if ($widgetFormName !='') {
			$where .= " and wf.name = " . $db->quote($widgetFormName) . " and wf.type!=10"; //10 is disabled
		}
		$this->setup($db,
			array(
				'cols'    => "*, widget_form_id as link, type as pretty_type",
				'from'    => "widget_form wf",
				'orderby' => 'wf.widget_form_id',
				'where'   => $where
			),
			array('name' => 'Name', 'link' => 'Link', 'pretty_type' => 'Type'));
			$this->registerTemplate('link','<a href="'.substr(Celini::link('edit','WidgetForm',true,false),0,-1).'/{$widget_form_id}?">edit&nbsp;</a>');

			$this->registerFilter('pretty_type', array(&$this, '_lookup'));
        }



        
        function _lookup($value) {
                $em =& Celini::enumManagerInstance();
                return $em->lookup('widget_type', $value);
        }

}
?>
