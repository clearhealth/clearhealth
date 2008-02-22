<?php
/**
 * Object Relational Persistence Mapping Class for table: splash
 *
 * @package	com.clear-health.celini
 * @author	Uversa Inc.
 */
class Splash extends ORDataObject {

	/**#@+
	 * Fields of table: splash mapped to class members
	 */
	var $splash_id		= '';
	var $name		= '';
	var $message		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'splash';

	/**
	 * Primary Key
	 */
	var $_key = 'splash_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'Splash';

	/**
	 * Handle instantiation
	 */
	function Splash() {
		parent::ORDataObject();
	}
	function setup() {
		$sql = "select splash_id from splash limit 1";
		$db = new clniDB();
		$res = $db->execute($sql);
		if ($res && !$res->EOF) {
			parent::setup($res->fields['splash_id']);
		}
		else {
			parent::setup(0);
		}
	}

	
}
?>
