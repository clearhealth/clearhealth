<?php
$loader->requireOnce('includes/Datasource/Datasource_sql.class.php');
class Superbill_DS extends Datasource_Sql {

	var $_internalName = 'Superbill_DS';
	var $_type = 'html';

	function Superbill_DS($id) {
		$labels = array( 'code' => 'Code', 'code_text' => 'Code name');

		$id = enforceType::int($id);

		$this->setup(Celini::dbInstance(),
			array(
				'cols' => 'c.code, code, code_text',
				'from' => 'codes c inner join superbill_data s using(code_id)',
				'where' => "s.status = 1 and s.superbill_id = $id"
			),
			$labels);
	}
}
?>
