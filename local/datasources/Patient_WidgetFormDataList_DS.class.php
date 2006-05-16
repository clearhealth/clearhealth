<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

class Patient_WidgetFormDataList_DS extends Datasource_sql 
{
	/**
	 * Stores the case-sensative class name for this ds and should be considered
	 * read-only.
	 *
	 * This is being used so that the internal name matches the filesystem
	 * name.  Once BC for PHP 4 is no longer required, this can be dropped in
	 * favor of using get_class($ds) where ever this property is referenced.
	 *
	 * @var string
	 */
	var $_internalName = 'Patient_WidgetFormDataList_DS';

	/**
	 * The form type to show
	 *
	 * @var string
	 */
	var $patient_id = '';


	function Patient_WidgetFormDataList_DS($patient_id) {
		$this->patient_id = intval($patient_id);
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "type, last_edit, f.name, form_data_id, external_id",
				'from'    => "widget_form AS wf " .
							 "INNER JOIN form AS f USING(form_id)".
							 "INNER JOIN form_data AS fd using (form_id)",
				'orderby' => 'name, last_edit DESC',
				'where'   => "fd.external_id = " . $this->patient_id
			),
			array('name' => 'Form Name','last_edit'=>'Last Edit'));
	}
	
	function set_form_type($form_id) {
		$this->_query['where'] = "fd.external_id = " . EnforceType::int($this->patient_id) . " and wf.form_id = " . EnforceType::int($form_id);
		//echo $this->preview();
	}
}

