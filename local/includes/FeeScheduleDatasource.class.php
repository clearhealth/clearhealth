<?php
require_once CELLINI_ROOT ."/includes/Datasource_editable.class.php";

class FeeScheduleDatasource extends Datasource_editable {

	var $where = array('superbill'=>0);

	var $primaryKeyField = 'code_id';
	var $extra = array('revision_id');
	var $revision_id = 1;
	var $feeSessionId = array('test'=>711);

	var $meta = array('editableMap' => array('test'=>'test','formula'=>'formula'));

	function FeeScheduleDatasource($session_array = "fsd") {
		$this->session = $session_array;
		$this->setup(
			$GLOBALS['config']['adodb']['db'],
			array(
				'cols' => 'c.code_id, code, code_text, data as test, formula',
				'from' => 'codes c left join fee_schedule_data fsd using(code_id)',
			),
			array( 'code' => 'Code', 'code_text' => 'Code name', 'test' => 'Test Fee')
		);

		if (isset($_SESSION[$this->session]['revision_id'])) {
			$this->revision_id = $_SESSION[$this->session]['revision_id'];
		} 
		$this->object =& ORDataObject::factory('FeeScheduleData');
	}

	/**
	 * Each editable column is a fee schedule, we embed the id of the fee schedule in name of the field
	 */
	function updateField($primaryKey,$field,$value) {
		$this->object->set('fee_schedule_id',$this->feeSessionId[$field]);
		$field = 'data';
		return parent::updateField($primaryKey,$field,$value);
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
