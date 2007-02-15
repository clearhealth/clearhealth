<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

class Patient_CriticalList_DS extends Datasource_sql  {
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
	var $_internalName = 'Patient_CriticalList_DS';

	/**
	 * The form type to show
	 *
	 * @var string
	 */
	var $patient_id = '';
	var $fields = '';
	var $case_sql = '';
	var $widget_form_id = '';


	function Patient_CriticalList_DS($patient_id) {
		$this->patient_id = intval($patient_id);
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "distinct f.name ",
				'from'    => "widget_form AS wf " .
							 "INNER JOIN form AS f USING(form_id) ".
							 "INNER JOIN form_data AS fd using (form_id) ".
							 "LEFT JOIN storage_int ON storage_int.foreign_key = fd.form_data_id ".
							 "LEFT JOIN storage_string ON storage_string.foreign_key = fd.form_data_id ".
							 "LEFT JOIN storage_text ON storage_text.foreign_key = fd.form_data_id ".
							 "LEFT JOIN storage_date ON storage_date.foreign_key = fd.form_data_id ",
				'orderby' => 'name, last_edit DESC',
				'where'   => "fd.external_id = " . $this->patient_id ." and type = '2'",
				'groupby'   => "fd.form_data_id"
			),
			false);
	}

	function getCriticalList($patient_id) {
		$db = new clniDB();

		$sql = "select distinct f.name, wf.widget_form_id from widget_form as wf
			inner join form as f using (form_id)
			inner join form_data as fd using (form_id)
			left join storage_int on storage_int.foreign_key = fd.form_data_id
			left join storage_string on storage_string.foreign_key = fd.form_data_id
			left join storage_text on storage_text.foreign_key = fd.form_data_id
			left join storage_date on storage_date.foreign_key = fd.form_data_id
			where fd.external_id = ". $this->patient_id ."
			and type = '2'
			group by fd.form_data_id
			order by name, last_edit desc";

		$results = $db->execute($sql);
		while ($results && !$results->EOF) {
			$criticalList[] = array("name"=>$results->fields["name"], "widget_form_id"=>$results->fields["widget_form_id"]);
			$results->MoveNext();
		}

		return $criticalList;
	}

	function getCriticalData($patient_id, $widget_form_id) {
		$db = new clniDB();

		$sql = "select distinct f.name, wf.widget_form_id from widget_form as wf
			inner join form as f using (form_id)
			inner join form_data as fd using (form_id)
			left join storage_int on storage_int.foreign_key = fd.form_data_id
			left join storage_string on storage_string.foreign_key = fd.form_data_id
			left join storage_text on storage_text.foreign_key = fd.form_data_id
			left join storage_date on storage_date.foreign_key = fd.form_data_id
			where fd.external_id = ". $this->patient_id ."
			and wf.widget_form_id = '". $this->widget_form_id ."'
			and type = '2'
			group by fd.form_data_id
			order by name, last_edit desc";
		
		$results = $db->execute($sql);

		return $results;
	}
}

?>
