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
	var $_table = 'coding_data';
	var $_internalName='CodingData';

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function CodingData($db = null) {
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
			$sql="SELECT * FROM coding_data_dental WHERE coding_data_id='".$this->get('id')."'";
			$teeth=$this->_db->GetAll($sql);
			if(count($teeth)>0){
				$this->set('tooth',$teeth['tooth']);
				$this->set('toothside',$teeth['toothside']);
			}
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

	function lookupModifier($id) {
		$ml = $this->getModifierList();
		if (isset($ml[$id])) {
			return $ml[$id];
		}
	}

	/**
	 * Get an array of codes for a provided parent_id
	 * 
	 * @param	string	$parent_id The string value of the desired parent id
	 */
	function getChildCodes($parent_id,$foreign_id=0) {
//	echo "CodingData getChildCodes with $parent_id <br>";
		
		$parent_id = intval($parent_id);
		$foreign_id = intval($foreign_id);
		$where = $foreign_id > 0 ? "foreign_id = {$foreign_id}" : "parent_id = {$parent_id}";
		$sql = "
			SELECT
				cd.coding_data_id, 
				cd.foreign_id,
				cd.parent_id,
				cd.code_id, 
				cd.modifier,
				cd.units,
				CONCAT(c.code, ' : ', c.code_text) AS description, 
				c.code,
				cd.fee 
			FROM 
				coding_data AS cd
				LEFT JOIN codes AS c ON cd.code_id = c.code_id 
			WHERE $where
				";
		$res = $this->_execute($sql);
		$ret = array();
		while(!$res->EOF) {
			$ret[$res->fields['coding_data_id']] = $res->fields;
			$res->MoveNext();
		}
		return $ret;
	}
	
	/**
	 * Get code list using encounter_id
	 *
	 * @param int $foreign_id
	 * @return array
	 */
	function getCodeList($foreign_id){
	//echo "CodingData getCodeList with $foreign_id <br>";
		$foreign_id = intval($foreign_id);
		$sql = "
		SELECT
			cd.coding_data_id, cd.foreign_id, cd.parent_id, cd.code_id, 
			cd.modifier, cd.units, CONCAT(c.code, ' : ', c.code_text) AS description, c.code, cd.fee,
			cdd.tooth, cdd.toothside
		FROM
			coding_data AS cd
			LEFT JOIN codes AS c ON cd.code_id = c.code_id 
			LEFT JOIN coding_data_dental AS cdd ON cd.coding_data_id=cdd.coding_data_id
		WHERE foreign_id = $foreign_id
			AND parent_id = 0
		ORDER BY
			cd.coding_data_id
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
	
	/**
	 * Get code list using claim_id and encounter_id
	 *
	 * @param int $claim_id
	 * @param int $foreign_id
	 * @return array
	 */
	function getCodeListByClaimId($claim_id,$foreign_id){
		$claim_id = intval($claim_id);
		$foreign_id = intval($foreign_id);
		$sql = "
		SELECT
                        CASE WHEN cd.coding_data_id IS NULL THEN fbcl.claimline_id ELSE cd.coding_data_id END AS coding_data_id, 
			cd.foreign_id, cd.parent_id, cd.code_id,
       			CASE WHEN cd.modifier IS NULL THEN fbcl.modifier ELSE cd.modifier END AS modifier, 
			CASE WHEN cd.units IS NULL THEN fbcl.units ELSE cd.units END AS units, 
			CASE WHEN c.code IS NULL THEN fbcl.procedure ELSE CONCAT(c.code, ' : ', c.code_text) END AS description, 
CASE WHEN c.code IS NULL THEN fbcl.procedure ELSE c.code END AS code,c.code, 
fbcl.amount AS fee,
                        cdd.tooth, cdd.toothside
                FROM
                        encounter AS e
                        LEFT JOIN clearhealth_claim AS cc on cc.encounter_id = e.encounter_id
                        LEFT JOIN fbclaim AS fbc ON(fbc.claim_identifier=cc.identifier)
                        LEFT JOIN fbclaimline fbcl ON(fbcl.claim_id=fbc.claim_id)
                        LEFT JOIN codes AS c ON (c.code = fbcl.procedure)
                        LEFT JOIN coding_data cd ON(cd.code_id=c.code_id AND cd.foreign_id='$foreign_id')
                        LEFT JOIN coding_data_dental AS cdd ON cd.coding_data_id=cdd.coding_data_id
                WHERE
                        cc.claim_id='$claim_id'
                        AND e.encounter_id='$foreign_id'
                        AND (cd.parent_id = 0 OR cd.parent_id IS NULL)

		GROUP BY
			cd.coding_data_id
		ORDER BY
			cd.coding_data_id
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
	//echo "CodingData getParentCodes with <br>";
		if($this->_parentCode == null){
			$this->_parentCode = ORDataObject::factory("Code", $this->get("parent_id"));
		}

		return $this->_parentCode;	
	}

	function delete_claimline($parent_code){
		if(!isset($parent_code)){ return;}
	
		//delete parent code...
	$delete_parent_sql = "DELETE FROM `coding_data` WHERE `coding_data_id` = $parent_code";
	//echo $delete_parent_sql."<br>";
	$res = $this->_execute($delete_parent_sql);

		//delete child codes...
	$delete_children_sql = "DELETE FROM `coding_data` WHERE `parent_id` = $parent_code";
	//echo $delete_children_sql."<br>";
	$res = $this->_execute($delete_children_sql);



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
