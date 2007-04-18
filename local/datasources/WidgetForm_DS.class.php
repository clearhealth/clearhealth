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
		if (strlen($type) > 0) {
			if ($type =="*") {
				$type = "";
			}
			else {
				$type = "type in (".$type.")";
			}
		}
		else {
			$type = "type in (1,2,3)";
		}
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "*, widget_form_id as link",
				'from'    => "widget_form wf",
				'orderby' => 'wf.widget_form_id',
				'where'   => $type
			),
			array('name' => 'Name', 'link' => 'Link', 'type' => 'Type'));
			$this->registerTemplate('link','<a href="'.substr(Celini::link('edit','WidgetForm',true,false),0,-1).'/{$widget_form_id}?">{$link}</a>');

			$this->registerFilter('widgetType',array(&$this,'_widgetType'));
	}

	function _widgetType($value) {
		if (count($this->_widgetTypes) <= 0) {
			$enum = ORDataObject::factory('Enumeration');
			$this->_widgetTypes = $enum->get_enum_list('widget_type');
		}
		return $this->_widgetTypes[$value];
	}

}
?>
