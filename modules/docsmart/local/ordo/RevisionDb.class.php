<?php
/**
 * Object Relational Persistence Mapping Class for table: revisions_db
 *
 * @package com.clear-health.docsmart
 *
 * @todo prefix with DocSmart
 */
class RevisionDb extends ORDataObject {

	/**#@+
	 * Fields of table: revisions_db mapped to class members
	 */
	var $revision_id		= '';
	var $filedata		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'revisions_db';

	/**
	 * Primary Key
	 */
	var $_key = 'revision_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'RevisionDb';

	/**
	 * Handle instantiation
	 */
	function RevisionDb() {
		parent::ORDataObject();
	}

	/**
	 * redefined method which does not use relationship table
	 *
	 */
	function drop() {
		if ($this->get('id') > 0) {
			//$this->removeRelationship();
			$pkeys = $this->dbHelper->PrimaryKeys($this->tableName());
			return $this->dbHelper->execute("delete from ".$this->tableName()." where ".$this->dbHelper->genSqlPrimaryKeyWhere($this));
		}
		return false;		
	}
	
}
?>
