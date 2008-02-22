<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

/**
 * @package com.clear-health.celini
 */
class WidgetForm_MedicalHistory_DS extends Datasource_Sql {
	
	var $primaryKey = 'widget_form_id';
	var $_internalName = 'WidgetForm_List_DS';
	var $_widgetTypes = 0;

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function WidgetForm_MedicalHistory_DS() {
		$db = Celini::dbInstance();
		$this->setup($db,
			array(
				'cols'    => "*, widget_form_id as link, type as pretty_type",
				'from'    => "widget_form wf",
				'orderby' => 'wf.widget_form_id',
				'where'   => 'wf.show_on_medical_history = 1'
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
