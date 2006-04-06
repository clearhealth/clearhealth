<?php

$loader->requireOnce('includes/SuperbillDatasource.class.php');

class CodingDatasource extends SuperbillDatasource {

	var $where = array('code_type'=>3);

	var $primaryKeyField = 'coding_data_id';

	var $meta = array('editableMap' => array('relate'=>'relate'), 'editFunc' => array('relate'=>'makeRelate'), 'passAlong' => array('code_id'=>'code_id','code'=>'code','code_text'=>'code_text'), 'filterFunc' => array('relate'=>'relateFilter'));

	var $superbill_id = 1;

	var $extra = array('superbill_id');

	function CodingDatasource($session_array = "sbd") {
		$this->session = $session_array;

		if (isset($_SESSION[$this->session]['whereFilter'])) {
			$this->whereFilter = $_SESSION[$this->session]['whereFilter'];
		}

		$this->setup(
			$GLOBALS['config']['adodb']['db'],
			array(
				'cols' => 'coding_data_id, c.code_id, code, code_text, coding_data_id as relate',
				'from' => 'codes c left join coding_data cd using(code_id)',
			),
			array( 'code' => 'Code', 'code_text' => 'Code name', 'relate' => 'Related')
		);
		
		$this->object =& ORDataObject::factory('CodingData');
	}
}

class CptCodingDatasource extends CodingDatasource {
	var $where = array('code_type'=>3);

	function CptCodingDatasource() {
		parent::CodingDatasource('cpt');
	}
}
class IcdCodingDatasource extends CodingDatasource {
	var $where = array('code_type'=>2);

	function IcdCodingDatasource() {
		parent::CodingDatasource('icd');
	}
}
?>
