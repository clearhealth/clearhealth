<?php
require_once CELLINI_ROOT ."/includes/Datasource_editable.class.php";

class SuperbillDatasource extends Datasource_editable {

	var $where = array("code_type"=> array(2,3));

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
	
	function prepare() {
		$where = "";
		$first = true;
		foreach($this->where as $col => $value) {
			
			if (!$first) {
				$where .= " and ";
			}
			if (is_array($value)) {
				$parts = array();
				
				foreach($value as $val) {
					$parts[] .= " $col = ".$this->_db->qstr("$val");
				}
				$where .= " (" . implode(" OR ", $parts) . ") ";
			}
			else {
				$where .= " $col = ".$this->_db->qstr($value);
			}
			$first = false;
		}

		foreach($this->whereFilter as $col => $value) {
			if ($col == "code") {
				$where .= " and $col like ".$this->_db->qstr("$value%");
			}
			else {
				$where .= " and $col like ".$this->_db->qstr("%$value%");
			}
		}
		$this->_query['where'] = "$where";
		parent::prepare();
	}

	function reset() {
		$this->feeSessions = array();
		$this->whereFilter = array();

		$_SESSION[$this->session]['whereFilter'] = $this->whereFilter;
		$this->meta = array('editableMap' => array());
	}

	function addFilter($field,$value) {
		$this->whereFilter[$field] = $value;
		$_SESSION[$this->session]['whereFilter'] = $this->whereFilter;
	}
	function dropFilter($field) {
		unset($this->whereFilter[$field]);
		$_SESSION[$this->session]['whereFilter'] = $this->whereFilter;
	}
	
}
?>
