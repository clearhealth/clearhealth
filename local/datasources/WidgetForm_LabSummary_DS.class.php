<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's self management goals
 *
 * @package com.clearhealth.base
 */
class WidgetForm_LabSummary_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'WidgetForm_LabSummary_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $_personId = '';
	
	function WidgetForm_LabSummary_DS($person_id,$widgetFormId) {
		$this->_personId = $person_id;
		$this->_testName = '';
		$wfId = (int)$widgetFormId;		

		$sql = "select name from summary_columns where widget_form_id = '{$wfId}'";
		$db = new clniDB();
                $results = $db->execute($sql);	
	
		$name = '';
		if ($results && !$results->EOF) {
			$name = $results->fields['name'];
		}
	
		$qPersonId = clniDB::quote($person_id);
		$this->setup(Celini::dbInstance(),
			array(	'cols' 	=> "
					CONCAT(DATE_FORMAT(lt.specimen_received_time,'%m/%d/%Y'), ' ',lr.description,':',lr.value) as lab	
						 ",
				'from' 	=> "
				lab_order lo
				INNER JOIN lab_test lt ON lo.lab_order_id = lt.lab_order_id
				INNER JOIN lab_result lr ON lr.lab_test_id = lt.lab_test_id
",
				'where'	=> "
			lo.patient_id = {$this->_personId} and lr.description LIKE '" . $name . "'",
				'orderby'=> "lab DESC")
			,
			array(
				'lab' => 'Lab'
			)
		);
		
	}
	
	function _lookup($value) {
		$em =& Celini::enumManagerInstance();
		return $em->lookup('self_mgmt_goals', $value);
	}
	
	
}
?>
