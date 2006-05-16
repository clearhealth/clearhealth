<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

/**
 * @package com.uversainc.celini
 */
class WidgetForm_DS extends Datasource_Sql {
	
	var $primaryKey = 'widget_form_id';
	var $_internalName = 'WidgetForm_List_DS';

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
				'orderby' => 'wf.widget_form_id'
			),
			array('name' => 'Name', 'widget_form_id' => 'Form ID'));
			$this->registerTemplate('widget_form_id','<a href="'.substr(Celini::link('edit','WidgetForm',true,false),0,-1).'/{$widget_form_id}?">{$widget_form_id}</a>');
	}
}
?>
