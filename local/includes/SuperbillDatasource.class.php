<?php
require_once CELLINI_ROOT ."/includes/Datasource_editable.class.php";

class SuperbillDatasource extends Datasource_editable {

	var $where = array('code_type'=>3);

	var $primaryKeyField = 'superbill_data_id';

	var $meta = array('editableMap' => array('status'=>'status'), 'editFunc' => array('status'=>'makeToggle'), 'passAlong' => array('code_id'=>'code_id'), 'filterFunc' => array('status'=>'toggleFilter'));

	var $superbill_id = 1;

	var $extra = array('superbill_id');

	function SuperbillDatasource($session_array = "sbd") {
		$this->session = $session_array;
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

	function setRevision($id) {
		$this->revision_id = $id;
		$_SESSION[$this->session]['revision_id'] = $id;
	}
}
?>
