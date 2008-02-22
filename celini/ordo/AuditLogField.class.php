<?php
/**
 * Object Relational Persistence Mapping Class for table: audit_log_field
 *
 * @package	com.clear-health.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class AuditLogField extends ORDataObject {

	/**#@+
	 * Fields of table: audit_log_field mapped to class members
	 */
	var $audit_log_field_id		= '';
	var $audit_log_id		= '';
	var $field		= '';
	var $old_value		= '';
	var $new_value		= '';
	/**#@-*/

	var $auditChanges = false;

	/**
	 * DB Table
	 */
	var $_table = 'audit_log_field';

	/**
	 * Primary Key
	 */
	var $_key = 'audit_log_field_id';

	/**
	 * Handle instantiation
	 */
	function AuditLogField() {
		parent::ORDataObject();
		$this->auditChanges = false;
	}

	
	function setupByLogAndField($auditLogId,$field) {
		$ali = EnforceType::int($auditLogId);
		$f = $this->dbHelper->quote($field);

		$sql = "select * from ".$this->tableName()." where audit_log_id = $ali and field = $f";
		$res = $this->dbHelper->execute($sql);
		$this->helper->populateFromResults($this,$res);
	}
}
?>