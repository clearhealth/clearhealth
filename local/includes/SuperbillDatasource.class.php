<?php
require_once APP_ROOT ."/local/includes/FeeScheduleDatasource.class.php";

class SuperbillDatasource extends Datasource_editable {

	var $where = array('code_type'=>3);

	var $primaryKeyField = 'superbill_data_id';

	var $meta = array('editableMap' => array('status'=>'status'), 'editFunc' => array('status'=>'makeToggle'), 'passAlong' => array('code_id'=>'code_id'), 'filterFunc' => array('status'=>'toggleFilter'));

	var $superbill_id = 1;

	var $extra = array('superbill_id');

	function SuperbillDatasource($session_array = "sbd") {
		$this->session = $session_array;

		if (isset($_SESSION[$this->session]['whereFilter'])) {
			$this->whereFilter = $_SESSION[$this->session]['whereFilter'];
		}

		$this->setup(
			$GLOBALS['config']['adodb']['db'],
			array(
				'cols' => 'superbill_data_id, c.code_id, code, code_text, `status`',
				'from' => 'codes c left join superbill_data sbd using(code_id)',
			),
			array( 'code' => 'Code', 'code_text' => 'Code name', 'status' => 'Status')
		);
		
		$this->object =& ORDataObject::factory('SuperbillData');
	}
}
?>
