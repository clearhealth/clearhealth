<?php
require_once CELLINI_ROOT ."/includes/Datasource_editable.class.php";

class FeeScheduleDatasource extends Datasource_editable {

	var $where = array('superbill'=>0);

	function FeeScheduleDatasource() {
		$this->setup(
			$GLOBALS['config']['adodb']['db'],
			array(
				'cols' => 'c.code_id, code, code_text, data, formula',
				'from' => 'codes c left join fee_schedule_data fsd using(code_id)'
			),
			array( 'code' => 'Code', 'code_text' => 'Code name', 'data' => 'Fee')
		);
	}

	function prepare() {
		$where = "";
		$first = true;
		foreach($this->where as $col => $value) {
			if (!$first) {
				$where .= " and ";
			}
			$first = false;

			$where .= " $col = ".$this->_db->qstr($value);
		}
		$this->_query['where'] = $where;
		parent::prepare();
	}
}
?>
