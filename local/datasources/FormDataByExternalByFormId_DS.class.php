<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's related person's addresses
 *
 * @package com.clear-health.clearhealth
 */
class FormDataByExternalByFormId_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'FormDataByExternalByFormId_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	
	function FormDataByExternalByFormId_DS($externalId, $formId) {
		$externalId = (int)$externalId;
		$formId = (int)$formId;
		
		$this->setup(Celini::dbInstance(),
			array(
                                'cols'    => "last_edit, f.name, form_data_id, external_id",
                                'from'    => " form_data AS d
                                             INNER JOIN form AS f USING(form_id)
                                             ",
                                'orderby' => 'last_edit DESC',
                                'where'   => "external_id = {$externalId} and form_id = {$formId}"
                        ),
                        array('name' => 'Form Name','last_edit'=>'Last Edit'
			));
		
		//var_dump($this->preview());
	}
	
	
}
?>
