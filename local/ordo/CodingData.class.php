<?php
/**
 * Object Relational Persistence Mapping Class for table: coding_data
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: coding_data
 *
 * @package	com.uversainc.clearhealth
 */
class CodingData extends ORDataObject {

	/**#@+
	 * Fields of table: coding_data mapped to class members
	 */
	var $id				= '';
	var $foreign_id		= '';
	var $parent_id		= '';
	var $code_id		= '';
	var $modifier		= '';
	var $units			= '1.00';
	/**#@-*/

	var $_parentCode 	= null;

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function CodingData($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'coding_data';
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
		parent::populate('coding_data_id');
	}

	/**#@+
	 * Getters and Setters for Table: coding_data
	 */

	
	/**
	 * Getter for Primary Key: coding_data_id
	 */
	function get_coding_data_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: coding_data_id
	 */
	function set_coding_data_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
	function getModifierList() {
		$list = $this->_load_enum('code_modifier',false);
		return array_flip($list);
	}
	
	/**
	 * Get an array of codes for a provided parent_id
	 * 
	 * @param	string	$foreign_id The string value of the desired foreign id
	 */
	function getChildCodes($parent_id) {
		$parent_id = intval($parent_id);
		$sql = "
			select cd.coding_data_id, cd.foreign_id, cd.parent_id, cd.code_id, 
			cd.modifier, cd.units, CONCAT(c.code, ' : ', c.code_text) AS description  
			FROM coding_data AS cd
			LEFT JOIN codes AS c ON cd.code_id = c.code_id 
			WHERE parent_id = $parent_id
				";
		
		$res = $this->_execute($sql);
		$ret = array();
		while(!$res->EOF) {
			$ret[$res->fields['coding_data_id']] = $res->fields;
			$res->MoveNext();
		}
		return $ret;
	}
	
	function clearChildCodes($foreign_id, $parent_id) {
		$foreign_id = intval($foreign_id);
		$parent_id = intval($parent_id);
		$sql = "
			DELETE FROM coding_data
			WHERE foreign_id = $foreign_id AND parent_id = $parent_id
				";
		
		$res = $this->_execute($sql);
	}
	
	function getCodeList($foreign_id){
		$foreign_id = intval($foreign_id);
		$sql = "
			select cd.coding_data_id, cd.foreign_id, cd.parent_id, cd.code_id, 
			cd.modifier, cd.units, CONCAT(c.code, ' : ', c.code_text) AS description  
			FROM coding_data AS cd
			LEFT JOIN codes AS c ON cd.parent_id = c.code_id 
			WHERE foreign_id = $foreign_id
			GROUP BY cd.parent_id
				";
		
		$res = $this->_execute($sql);
		$ret = array();
		while(!$res->EOF) {
			$ret[$res->fields['coding_data_id']] = $res->fields;
			$res->MoveNext();
		}
		return $ret;		
	}
	
	function getParentCode(){
		if($this->_parentCode == null){
			$this->_parentCode = ORDataObject::factory("Code", $this->get("parent_id"));
		}

		return $this->_parentCode;	
	}


	

}
?>
