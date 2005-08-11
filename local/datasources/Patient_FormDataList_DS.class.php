<?php

class Patient_FormDataList_DS extends Datasource_sql 
{
	function Patient_FormDataList_DS($external_id) {
		$external_id = intval($external_id);
		
		$this->setup(Cellini::dbInstance(),
			array(
				'cols'    => "last_edit, f.name, form_data_id, external_id",
				'from'    => "form_data AS d
				             INNER JOIN form AS f USING(form_id)
							LEFT JOIN encounter AS e ON(d.external_id = e.encounter_id)",
				'orderby' => 'name, last_edit DESC',
				'where'   => "external_id = {$external_id} || e.patient_id = {$external_id}"
			),
			array('name' => 'Form Name','last_edit'=>'Last Edit'));
	}
}

