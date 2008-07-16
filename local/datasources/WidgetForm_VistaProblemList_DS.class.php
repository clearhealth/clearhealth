<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's self management goals
 *
 * @package com.clearhealth.base
 */
class WidgetForm_VistaProblemList_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'WidgetForm_VistaProblemList_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $_personId = '';
	var $_widgetFormId = '';
	
	function WidgetForm_VistaProblemList_DS($person_id,$widgetFormId) {
		$this->_personId = $person_id;
		$this->_widgetFormId = $widgetFormId;
		
		$qPersonId = clniDB::quote($person_id);
		$qWidgetFormId = clniDB::quote($widgetFormId);
		$this->setup(Celini::dbInstance(),
			array(	'cols' 	=> "
						value
						 ",
				'from' 	=> "widget_form wf
					    inner join form_data fd on fd.form_id = wf.form_id
					    inner join storage_string ss on ss.foreign_key = fd.form_data_id",

				'where'	=> "wf.widget_form_id = {$qWidgetFormId} and fd.external_id = {$qPersonId} ",
				'orderby'=> "ss.value DESC")
			,
			array(
				'value' => 'Problem'
			)
		);
		
		//echo $this->preview();exit;
	}
	
}
?>
