<?php
/**
 * Object Relational Persistence Mapping Class for table: audit_log
 *
 * @package	com.clear-health.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class AuditLog extends ORDataObject {

	/**#@+
	 * Fields of table: audit_log mapped to class members
	 */
	var $audit_log_id	= '';
	var $ordo		= '';
	var $ordo_id		= '';
	var $user_id		= '';
	var $type		= '';
	var $message		= '';
	var $log_date		= '';
	/**#@-*/

	/**
	 * DB Table
	 */
	var $_table = 'audit_log';

	/**
	 * Primary Key
	 */
	var $_key = 'audit_log_id';

	/**
	 * Handle instantiation
	 */
	function AuditLog() {
		parent::ORDataObject();
		$this->auditChanges = false;
		$this->_createRegistry = false;
	}

	function set_type($type) {
		if (is_int($type)) {
			$this->type = $type;
		}
		else {
			$em =& Celini::enumManagerInstance();
			$this->type = $em->lookupKey('audit_type',$type);
		}
	}

	function get_log_date() {
		return $this->_getTimestamp('log_date');
	}

	function set_log_date($date) {
		return $this->_setDate('log_date',$date);
	}
}
?>
