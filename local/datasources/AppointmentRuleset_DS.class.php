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
						name
						",
				'from'    => "appointment_ruleset",
			),
			array('name' => 'Name'));
		$this->registerTemplate('name','<a href="'.Celini::link('edit','AppointmentRuleset').'ruleset_id={$appointment_ruleset_id}">{$name}</a>');
	}
}

