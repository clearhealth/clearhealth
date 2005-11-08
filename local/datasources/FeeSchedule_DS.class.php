<?php
$loader->requireOnce('includes/Datasource/DatasourceActive.class.php');
class FeeSchedule_DS extends DatasourceActive {

	var $where = array('code_type'=>3);
	var $whereFilter = array();

	var $primaryKey = 'code_id';

	var $_internalName = 'FeeSchedule_DS';
	var $_type = 'html';

	function FeeSchedule_DS() {
		$labels = array( 'code' => 'Code', 'code_text' => 'Code name');

		$this->setup(Celini::dbInstance(),
			array(
				'cols' => 'c.code_id, code, code_text',
				'from' => 'codes c'
			),
			$labels);
	}
}
?>
