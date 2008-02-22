<?php
/**
 * Object Relational Persistence Mapping Class for table: folders
 *
 * @package com.clear-health.docsmart
 *
 * @todo Change "webdavname" to something like "label" as it isn't WebDAV specific.
 * @todo prefix with DocSmart
 */
class Folder extends ORDataObject {

	/**#@+
	 * Fields of table: folders mapped to class members
	 */
	var $folder_id		= '';
	var $label		= '';
	var $create_date = '';
	var $modify_date = '';
	var $webdavname = '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'folders';

	/**
	 * Primary Key
	 */
	var $_key = 'folder_id';

	/**
	 * Handle instantiation
	 */
	function Folder() {
		parent::ORDataObject();
	}

	function persist() {
		$this->modify_date = date("Y-m-d H:i:s");
		if(!$this->create_date) {
			$this->create_date = $this->modify_date;
		}
		if(empty($this->webdavname)) {
			$this->webdavname = $this->label;
		}		
		parent::persist();
	}
}
?>
