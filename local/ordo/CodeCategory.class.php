<?php
/**
 * Object Relational Persistence Mapping Class for table: code_category
 *
 * @package	com.clear-health.celini
 * @author	ClearHealth Inc.
 */
class CodeCategory extends ORDataObject {

	/**#@+
	 * Fields of table: code_category mapped to class members
	 */
	var $code_category_id		= '';
	var $category_name		= '';
	var $category_id		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'code_category';

	/**
	 * Primary Key
	 */
	var $_key = 'code_category_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'CodeCategory';

	/**
	 * Handle instantiation
	 */
	function CodeCategory() {
		parent::ORDataObject();
	}

	function set_category_id($in) {
		$in = enforcetype::int($in);
		if ($in == $this->category_id) {
			return;
		}
		if (!$in) {
			$in = $this->findOpenId();
		}
		else {
			$in = $this->findOpenId($in);
		}
		$this->category_id = $in;
	}

	function findOpenId($check = false) {
		if ($check === false) {
			$check = $this->dbHelper->getOne('select count(*) from '.$this->tableName())+1;
		}
		if ($this->dbHelper->getOne('select count(*)  from '.$this->tableName()." where category_id = $check and code_category_id != ".$this->get('id'))) {
			return $this->findOpenId($check+1);
		}
		return $check;
	}
	
}
?>
