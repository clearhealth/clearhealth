<?php
$loader->requireOnce('includes/Datasource_sql.class.php');
class MiscCharge_Encounter_DS extends Datasource_sql {

	var $_internalName = 'MiscCharge_Encounter_DS';
	var $_type = 'html';

	function MiscCharge_Encounter_DS($id = false) {
		$labels = array( 'amount' => 'Amount', 'title' => 'Title');

		$id = enforceType::int($id);

		$cols = 'amount, title, note';
		$from = 'misc_charge';

		$this->setup(Celini::dbInstance(),
			array(
				'cols'		=> $cols,
				'from'		=> $from,
				'where'		=> "encounter_id = $id",
				'orderby'	=> "charge_date"
			),
			$labels);
	}
}
?>
