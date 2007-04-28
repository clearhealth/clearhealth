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


	function WidgetForm_DS($type = '') {
		if (empty($type) || $type =="*") {
			$type = "";
		}
		else {
			$type = "type in (".$type.")";
		}
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "*, widget_form_id as link",
				'from'    => "widget_form wf",
				'orderby' => 'wf.widget_form_id',
				'where'   => $type
			),
			array('name' => 'Name', 'link' => 'Link', 'type' => 'Type'));
			$this->registerTemplate('link','<a href="'.substr(Celini::link('edit','WidgetForm',true,false),0,-1).'/{$widget_form_id}?">edit&nbsp;</a>');

			$this->registerFilter('type', array(&$this, '_lookup'));
        }



        
        function _lookup($value) {
                $em =& Celini::enumManagerInstance();
                return $em->lookup('widget_type', $value);
        }

}
?>
