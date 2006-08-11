<?php
$loader->requireOnce('includes/Datasource_sql.class.php');
class FormRule_DS extends Datasource_Sql {

	var $_internalName = 'FormRule_DS';
	var $_type = 'html';

	function FormRule_DS() {

		$cols = array('field_name' => 'Form Name', 'rule_name' => 'Rule Name', 'operator' => 'Operator');
		
		$this->setup (Celini::dbInstance(),
			array(
			'cols' 	=> "form_rule_id, field_name, rule_name, operator ",
			'from' 	=> "form_rule",
			'orderby' => 'form_rule_id'
			),
			$cols);
	}
}
?>