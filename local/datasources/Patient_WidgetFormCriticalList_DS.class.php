<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

class Patient_WidgetFormCriticalList_DS extends Datasource_sql  {
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
	var $_internalName = 'Patient_WidgetFormCriticalList_DS';

	/**
	 * The form type to show
	 *
	 * @var string
	 */
	var $patient_id = '';
	var $form_id = '';
	var $widget_form_id = '';
	var $encounterId = '';
	var $case_sql = '';
	var $dynamicLabels = array();
	var $dynamicFields = array();
	var $_active_only = false;

	function Patient_WidgetFormCriticalList_DS($patient_id,$form_id,$widget_form_id,$encounterId) {
		$this->patient_id = (int)$patient_id;
		$this->form_id = (int)$form_id;
		$this->widget_form_id = (int)$widget_form_id;
		$this->encounterId = (int)$encounterId;
		
		$this->set_form_type();
		$this->_build_case_sql();
		$this->_labels = $this->dynamicLabels;
		$this->_setupSql();
		$this->_labels = $this->dynamicLabels;
	}
	
	function _setupSql() {
		$where = "";

		if ($this->encounterId > 0) {
			$where .= " and fd.encounter_id = " . $this->encounterId;
		}
		$having = '';
		if ($this->_active_only) {
			$having = "HAVING active = 1";
		}
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "widget_form_id, f.form_id, form_data_id $this->case_sql ",
				'from'    => "widget_form AS wf " .
							 "INNER JOIN form AS f USING(form_id) ".
							 "INNER JOIN form_data AS fd using (form_id) ".
							 "LEFT JOIN storage_int ON storage_int.foreign_key = fd.form_data_id ".
							 "LEFT JOIN storage_string ON storage_string.foreign_key = fd.form_data_id ".
							 "LEFT JOIN storage_text ON storage_text.foreign_key = fd.form_data_id ".
							 "LEFT JOIN storage_date ON storage_date.foreign_key = fd.form_data_id ",
				'where'   => "fd.external_id = '" . $this->patient_id . "' and f.form_id = '" . $this->form_id . "' " . $where,
				'groupby'   => "fd.form_data_id, storage_date.array_index, storage_string.array_index, storage_string.array_index, storage_text.array_index $having"
			),
			false);
//echo $this->preview();
	}

	function set_form_type() {
		$widget_form_id = $this->widget_form_id;
		$labels = array();
		$fields = array();
		
		$db = Celini::dbInstance();
		
		$sql = "select name, pretty_name, table_name from summary_columns where widget_form_id = '$widget_form_id'";
		//echo $sql . "<br/>";
		//something resets fetch mode here, unknown bug, workaround to reset it here
		$db->SetFetchMode(ADODB_FETCH_ASSOC);
		$res = $db->execute($sql);
		while($res && !$res->EOF) {
			if (strpos($res->fields['name'],'_active') > 0) {
			$fields[$res->fields["name"]] = array('name'=>'active', 'table_name'=>$res->fields['table_name']);
			$this->_active_only = true;
			}
			else if (isset($res->fields['name'])) {
			
			$labels[$res->fields["name"]] = $res->fields["pretty_name"];
			$fields[$res->fields["name"]] = array('name'=>$res->fields['name'], 'table_name'=>$res->fields['table_name']);
}
			$res->MoveNext();	
		}
		//var_dump($fields);var_dump($labels);
			$this->dynamicLabels = $labels;
			$this->dynamicFields = $fields;

	}

	function _build_case_sql() {
		$form_id = $this->form_id;
	    	$case_sql = "";
	    	if (count($this->dynamicFields > 0)) $case_sql = " ,";
	        foreach ($this->dynamicFields as $field => $ar) {
	        	$table = $ar['table_name'];

	        	$value_name = $ar['name'];
			if ($table) {
			        $case_sql .= " MAX(CASE WHEN $table.value_key = '$field' THEN $table.value END)  as '$value_name', ";
			}
	        }
	        $case_sql = substr($case_sql,0,-2);             
	        $this->case_sql = $case_sql;
	}
}

