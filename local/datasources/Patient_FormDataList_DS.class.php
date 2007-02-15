<?php

$loader->requireOnce('/includes/Datasource_sql.class.php');

class Patient_FormDataList_DS extends Datasource_sql 
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
	var $_internalName = 'Patient_FormDataList_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function Patient_FormDataList_DS($external_id) {
		$external_id = intval($external_id);
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "last_edit, f.name, form_data_id, external_id",
				'from'    => "form_data AS d
				             INNER JOIN form AS f USING(form_id)
							LEFT JOIN encounter AS e ON(d.external_id = e.encounter_id)",
				'orderby' => 'name, last_edit DESC',
				'where'   => "external_id = {$external_id} || e.patient_id = {$external_id}"
			),
			array('name' => 'Form Named','last_edit'=>'Last Edit'));
	}
}

