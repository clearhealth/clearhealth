<?php
/**
 * Object Relational Persistence Mapping Class for table: coding_data
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */


/**
 * Object Relational Persistence Mapping Class for table: coding_data
 *
 * @package	com.uversainc.clearhealth
 */
class Code extends ORDataObject {

	/**#@+
	 * Fields of table: coding_data mapped to class members
	 */
	var $code_id		= '';
	var $code_text		= '';
	var $code_text_short	= '';
	var $code		= '';
	var $code_type		= '';
	/**#@-*/

	var $_parentCode 	= null;
	var $_table 		= 'codes';
	var $_internalName	='Code';
	var $_key		= 'code_id';

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Code($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Coding_data with this
	 */
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('code_id');
	}

	/**
	 * create a record from a code
	 */
	function &fromCode($code) {
		$c =& ORDataObject::Factory('Code');
		$res = $c->_execute("select code_id from $c->_table where code =".$c->_quote($code));
		if ($res && !$res->EOF) {
			$c->setup($res->fields['code_id']);
		}
		return $c;
	}

	/**#@+
	 * Getters and Setters for Table: coding_data
	 */
	function value_name() {
		if($this->code != '') {
			return $this->code . " : " . $this->code_text;	
		}
	}

	
	/**#@-*/	
	function getCodeDesc(){
		if($this->code != '')
			return $this->code . " : " . $this->code_text;	
	}
}
?>
