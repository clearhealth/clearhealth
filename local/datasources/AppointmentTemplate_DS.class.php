<?php
$loader->requireOnce('includes/Datasource_sql.class.php');
class AppointmentTemplate_DS extends Datasource_Sql {

	var $_internalName = 'AppointmentTemplate_DS';
	var $_type = 'html';

	function AppointmentTemplate_DS() {
		$labels = array( 'name' => 'Name', 'slots' => '# of Slots', 'length' => 'Length (Min)');

		$this->setup(Celini::dbInstance(),
			array(
				'cols' => 'appointment_template_id, name, count(occurence_breakdown_id) slots, round(sum(length)/60) length',
				'from' => 'appointment_template at left join occurence_breakdown ob on at.appointment_template_id = ob.occurence_id',
				'groupby' => 'appointment_template_id'
			),
			$labels);

		$this->registerTemplate('name','<a href="'.Celini::link('edit','AppointmentTemplate').'?id={$appointment_template_id}">{$name}</a>');
	}
}
?>
