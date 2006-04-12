<?php
/**
 * Object Relational Persistence Mapping Class for table: hl7_message
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class HL7Message extends ORDataObject {

	/**#@+
	 * Fields of table: hl7_message mapped to class members
	 */
	var $hl7_message_id		= '';
	var $control_id		= '';
	var $message		= '';
	var $type		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'hl7_message';

	/**
	 * Primary Key
	 */
	var $_key = 'hl7_message_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'Hl7Message';
	

	/**
	 * Handle instantiation
	 */
	function HL7Message() {
		parent::ORDataObject();
	}
	
	/**
	 * Make sure that duplicate messages having the same control number
	 * and type effect the existing record instead of creating a new one
	 */	
	function setup($id ="", $control_id, $type) {
		$sql = "select hl7_message_id from " . $this->_table . " where type = " . $this->_quote($type) . " and control_id = " . $this->_quote($control_id);
		$res = $this->_db->_execute($sql);
		
		if ($res && !$res->EOF) {
			$this->set("id", $res->fields["hl7_message_id"]);
			$this->populate();
		}
			
	}
	
	
}
?>
