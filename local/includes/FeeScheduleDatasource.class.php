<?php
require_once CELLINI_ROOT ."/includes/Datasource_editable.class.php";

class FeeScheduleDatasource extends Datasource_editable {

	var $where = array('code_type'=>3);
	var $whereFilter = array();

	var $primaryKeyField = 'code_id';
	var $extra = array('revision_id');
	var $revision_id = 1;
	var $feeSessions = array();

	var $meta = array('editableMap' => array(),'passAlong'=>array('code_id'=>'code_id'));

	function FeeScheduleDatasource($session_array = "fsd") {
		$this->session = $session_array;

		if (isset($_SESSION[$this->session]['revision_id'])) {
			$this->revision_id = $_SESSION[$this->session]['revision_id'];
		} 

		if (isset($_SESSION[$this->session]['feeSessions'])) {
			$this->feeSessions = $_SESSION[$this->session]['feeSessions'];
		}

		if (isset($_SESSION[$this->session]['whereFilter'])) {
			$this->whereFilter = $_SESSION[$this->session]['whereFilter'];
		}


		$this->object =& ORDataObject::factory('FeeScheduleData');

		$cols = 'c.code_id, code, code_text';
		$labels = array( 'code' => 'Code', 'code_text' => 'Code name');
		$from = 'codes c ';
		foreach($this->feeSessions as $field => $data) {
			$cols .= ", fsd_$field.data as $field";
			$labels[$field] = $data['label'];
			$from .= " left join fee_schedule_data fsd_$field using(code_id)";
			$this->meta['editableMap'][$field] = $field;
			$this->where["fsd_$field.fee_schedule_id"] = $data['id'];
		}

		$this->setup(
			$GLOBALS['config']['adodb']['db'],
			array(
				'cols' => $cols,
				'from' => $from
			),
			$labels
		);
	}

	/**
	 * Each editable column is a fee schedule, we embed the id of the fee schedule in name of the field
	 */
	function updateField($primaryKey,$field,$value,$passAlong) {
		$this->object->set('fee_schedule_id',$this->feeSessions[$field]['id']);
		$field = 'data';
		return parent::updateField($primaryKey,$field,$value,$passAlong);
	}

	function prepare() {
		$where = "";
		$first = true;
		foreach($this->where as $col => $value) {
			if (!$first) {
				$where .= " and ";
			}
			$first = false;

			if (strstr($col,"fee_schedule_id")) {
				$where .= " ($col is null or $col = ".$this->_db->qstr($value).") ";
			}
			else {
				$where .= " $col = ".$this->_db->qstr($value);
			}
		}

		foreach($this->whereFilter as $col => $value) {
			$where .= " and $col like ".$this->_db->qstr("%$value%");
		}
		$this->_query['where'] = "$where";
		parent::prepare();
	}

	function setRevision($id) {
		$this->revision_id = $id;
		$_SESSION[$this->session]['revision_id'] = $id;
	}

	function addFeeSchedule($name,$label,$id) {
		$this->feeSessions[$name] = array('id'=>$id,'label'=>$label);
		$_SESSION[$this->session]['feeSessions'] = $this->feeSessions;
	}

	function reset() {
		$this->feeSessions = array();
		$this->whereFilter = array();

		$_SESSION[$this->session]['whereFilter'] = $this->whereFilter;
		$_SESSION[$this->session]['feeSessions'] = $this->feeSessions;
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
