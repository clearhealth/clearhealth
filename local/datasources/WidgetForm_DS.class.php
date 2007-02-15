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


	function WidgetForm_DS() {
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "*",
				'from'    => "widget_form wf",
				'orderby' => 'wf.widget_form_id',
				'where'   => 'type = 2'
			),
			array('name' => 'Name', 'widget_form_id' => 'Form ID'));
			$this->registerTemplate('widget_form_id','<a href="'.substr(Celini::link('edit','WidgetForm',true,false),0,-1).'/{$widget_form_id}?">{$widget_form_id}</a>');

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
