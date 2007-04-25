<?php

class X12ImportedData extends ORDataObject
{
	var $x12imported_data_id = '';
	var $data = '';
	var $created_date = '';
	var $filename = '';
	
	var $_table = 'x12imported_data';
	var $_key = 'x12imported_data_id';
	
	function X12ImportedData() {
		parent::ORDataObject();
		$this->set('created_date', date('Y-m-d'));
	}
	
	function setupByDataHash($hash) {
		$qHash = $this->dbHelper->quote($hash);
		$tableName = $this->tableName();
		$sql = "SELECT * FROM {$tableName} WHERE MD5(data) = {$qHash}";
		$this->helper->populateFromQuery($this, $sql);
	}
	
	function set_created_date($value) {
		$this->_setDate('created_date', $value);
	}
	
	function get_created_date() {
		return $this->_getDate('created_date');
	}
}

?>
