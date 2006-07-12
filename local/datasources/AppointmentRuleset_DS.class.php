<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class AppointmentRuleset_DS extends Datasource_sql 
{
	var $_internalName = 'AppointmentRuleset_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function AppointmentRuleset_DS() {
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "appointment_ruleset_id,
						name,
						error_message,
						enabled
						",
				'from'    => "appointment_ruleset",
			),
			array('x'=>false,'name' => 'Name','enabled'=> 'Enabled','error_message'=>'Error Message'));
		$this->registerTemplate('name','<a href="'.Celini::link('edit','AppointmentRuleset').'ruleset_id={$appointment_ruleset_id}">{$name}</a>');
		$this->registerTemplate('x','<a href="#delete" onclick="deleteRuleset(this,{$appointment_ruleset_id})">X</a>');
		$this->registerFilter('enabled',array(&$this,'enabledFilter'));
	}

	function enabledFilter($in) {
		if ($in) {
			return 'Yes';
		}
		return 'No';
	}
}

