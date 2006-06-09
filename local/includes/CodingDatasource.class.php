<?php

$loader->requireOnce('includes/Datasource_sql.class.php');

class CodingDatasource extends Datasource_sql {

	var $where = array('code_type'=>3);

	var $primaryKeyField = 'coding_data_id';

	var $superbill_id = 1;

	var $extra = array('superbill_id');

	function CodingDatasource() {
		$this->setup(
			$GLOBALS['config']['adodb']['db'],
			array(
				'cols' => 'coding_data_id, c.code_id, code, code_text, coding_data_id as relate',
				'from' => 'codes c left join coding_data cd using(code_id)',
			),
			array( 'code' => 'Code', 'code_text' => 'Code name', 'relate' => 'Related')
		);
	}
}

class CptCodingDatasource extends CodingDatasource {
	var $where = array('code_type'=>3);

	function CptCodingDatasource() {
		parent::CodingDatasource();
	}
}
class IcdCodingDatasource extends CodingDatasource {
	var $where = array('code_type'=>2);

	function IcdCodingDatasource() {
		parent::CodingDatasource();
	}
}
?>
