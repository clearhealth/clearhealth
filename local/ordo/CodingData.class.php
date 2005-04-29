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
	var $id			= '';
	var $foreign_id		= '';
	var $parent_id		= '';
	var $code_id		= '';
	var $modifier		= '';
	var $units		= '1.00';
	var $fee		= '';
	var $primary_code	= '';
	var $code_order		= '';
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

	function persist() {
		// Most of the specific persist code here seems to deal with the ordering of ICD codes. 
		// All of the sql criterion were based on parent which points to the row in coding_data where
		// the procedure code lives and foreign which points to the row in codes where the 
		// code definition lives. I am migrating this to not respect foriegn ids which are irrelevant

		if ($this->get('id') == 0) {
			$res = $this->_execute("select count(*) c from $this->_table where parent_id = ".(int)$this->get('parent_id'));

			if ($res && !$res->EOF) {
				$this->set('code_order',($res->fields['c']+1));
				//var_dump($this->get('code_order'));
			}
		}

		parent::persist();

		// set primary_code == 1 when its the first coding_data row for a giving foreign,parent_id combo
		$res = $this->_execute("select max(primary_code) pc, min(coding_data_id) cdi from $this->_table where parent_id = ".(int)$this->get('parent_id'));
		if ($res && !$res->EOF) {
			if ($res->fields['pc'] != 1) {
				$this->_execute("update coding_data set primary_code = 1 where coding_data_id = ".$res->fields['cdi']);
			}
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
		$list = $this->_load_enum('code_modifier');
		return array_flip($list);
	}

	/**
	 * Get an array of codes for a provided parent_id
	 * 
	 * @param	string	$parent_id The string value of the desired parent id
	 */
	function getChildCodes($parent_id) {
//	echo "CodingData getChildCodes with $parent_id <br>";
		
		$parent_id = intval($parent_id);
		$sql = "
			select cd.coding_data_id, cd.foreign_id, cd.parent_id, cd.code_id, 
			cd.modifier, cd.units, CONCAT(c.code, ' : ', c.code_text) AS description, c.code, cd.fee 
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
	
	function clearChildCodes($parent_id) {
	//echo "CodingData clearChildCodes with $parent_id <br>";
		$parent_id = intval($parent_id);
		$sql = "
			DELETE FROM coding_data
			WHERE parent_id = $parent_id
				";
		
		$res = $this->_execute($sql);
	}
	
	function getCodeList($foreign_id){
	//echo "CodingData getCodeList with $foreign_id <br>";
		$foreign_id = intval($foreign_id);
		$sql = "
			select cd.coding_data_id, cd.foreign_id, cd.parent_id, cd.code_id, 
			cd.modifier, cd.units, CONCAT(c.code, ' : ', c.code_text) AS description, c.code, cd.fee  
			FROM coding_data AS cd
			LEFT JOIN codes AS c ON cd.code_id = c.code_id 
			WHERE foreign_id = $foreign_id
			AND parent_id = 0
			order by cd.coding_data_id
				";
		$res = $this->_execute($sql);
		$ret = array();
		while(!$res->EOF) {
			$ret[$res->fields['coding_data_id']] = $res->fields;
			$res->MoveNext();
		}
		//echo "returning: <br>";
		//var_export($ret);
		//echo "SQL: <br>".$sql;
		
		return $ret;		
	}
	
	function getParentCode(){
	//echo "CodingData getParentCodes with <br>";
		if($this->_parentCode == null){
			$this->_parentCode = ORDataObject::factory("Code", $this->get("parent_id"));
		}

		return $this->_parentCode;	
	}


	// is there a better way to do this??

	function printme()
	{
	
		return(	
			"id ".$this->id."<br>".
			" foreign_id".$this->foreign_id."<br>".
			" parent_id ".$this->parent_id."<br>".
			" code_id ".$this->code_id."<br>".
			" modifier ".$this->modifier."<br>".
			" units ".$this->units."<br>".
			" fee ".$this->fee."<br>".
			" primary_code ".$this->primary_code."<br>".
			" code_order ".$this->code_order."<br>".
			" <br>\n");


	}

}
?>
