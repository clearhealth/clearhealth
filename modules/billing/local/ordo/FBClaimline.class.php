<?php
/**
 * Object Relational Persistence Mapping Class for table: claimline
 *
 * @package	com.clear-health.freeb2
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: claimline
 *
 * @package	com.clear-health.freeb2
 */
class FBClaimline extends ORDataObject {

	/**#@+
	 * Fields of table: claimline mapped to class members
	 */
	var $id			= '';
	var $claim_id			= '';
	var $procedure			= '';
	var $modifier			= '';
	var $diagnoses			= array();
	var $amount				= '';
	var $units				= '';
	var $comment			= '';
	var $comment_type		= '';			
	var $date_of_treatment		= '';
	var $amount_paid		= '';
	var $diagnosis_pointer 		= '';
	var $index              = '';
	/**#@-*/
	var $_table = 'fbclaimline';
	
	/**
	 * {@inheritdoc}
	 */
	var $storage_metadata = array(
		'int' => array(), 
		'date' => array(),
		'string' => array(
			'clia_number' => '',
			'tooth' => '',
			'toothside' => '',
			'modifier_2' => '',
			'modifier_3' => '',
			'modifier_4' => ''
		)
	);

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FBClaimline($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
		$this->addMetaHints("hide",array("claim_id"));
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Claimline with this
	 */
	function setup($id = 0, $claim_id = false, $index = null) {
		$this->set('id',$id);
		if ($claim_id != false) {
			$this->set('claim_id',$claim_id);
		}
		
		// try to populate
		if ($id > 0) {
			// we have exact id, use that
			$this->populate();
		}
		elseif ($claim_id > 0 && is_numeric($index)) {
			// we have the parent claim_id and the index, use that info
			$sql = '
				SELECT 
					*
				FROM
					' . $this->_table . '
				WHERE
					`claim_id` = ' . $this->dbHelper->quote($this->get('claim_id')) . ' AND
					`index` = ' . $this->dbHelper->quote($index);
			$this->helper->populateFromResults($this, $this->dbHelper->execute($sql));
			$this->helper->populateStorageValues($this);
		}
	}

	/**
	 * Get all the claimlines associated with a claim_id
	 *
	 * note: this is a high performance method bypassing normal setup method
	 *
	 * @param	int	$claim_id
	 */
	function &arrayFromClaimId($claim_id) {
		settype($claim_id,'int');
		if (!strlen($claim_id) > 0) return false;
		$sql = "
			select 
				ordo.*, 
				d.diagnosis, " .
				$this->helper->storageColumnAliases($this) . "
			from 
				" . $this->tableName() . " AS ordo
				left join fbdiagnoses as d using (claimline_id) " .
				$this->helper->storageTableJoins($this) . "
			where 
				claim_id = " . $this->_quote($claim_id) . "
			order by 
				ordo.claimline_id ASC,
				d.id ASC";
		$res = $this->_execute($sql);

		$lines = array();
		$i = 0;
		$diagnoses = array();
		while($res && $res->RecordCount() > 0) {
			$crow = $res->fields;
			$res->moveNext();
			if (strlen($crow['claimline_id']) > 0 && $crow['claimline_id'] === $res->fields['claimline_id']) {
				$diagnoses[] = $crow['diagnosis'];
				continue;
			}
			$diagnoses[] = $crow['diagnosis'];
			$lines[$i] = new FBClaimline();
			$lines[$i]->populate_array($crow);
			$lines[$i]->set("diagnoses",$diagnoses);
			$diagnoses = array();
			$i++;
			if ($res->EOF) 	break;
		}
		return $lines;
	}

	/**
	 * Get a grid datasource with a listing of all the claimlines for a specific claim_id
	 */
	function &lineList($claim_id) {
		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> '*',
				'from' 	=> "$this->_table c ",
			),
			true);
		return $ds;
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('claimline_id');
		$sql = "select diagnosis from fbdiagnoses d where claimline_id = " . $this->_quote($this->get("id")) . " order by d.id ASC";
		$result = $this->_execute($sql);
		
		$diagnoses = array();
		if($result && !$result->EOF) {
			$diagnoses[] = $result->fields['diagnosis'];
			$result->MoveNext();	
		}
		$this->set("diagnoses",$diagnoses);
	}
	
	/**
	 * Persist the class from the db
	 */
	function persist() {
		parent::persist();
		$diagnoses = $this->get("diagnoses");
		$sql = "delete from fbdiagnoses where claimline_id = " . $this->_quote($this->get("id"));
		$result = $this->_execute($sql);
		if (is_array($diagnoses) && count($diagnoses) >0) {
			foreach ($diagnoses as $diagnosis) {
				$genid = $this->_db->GenID("sequences");
				$sql = "insert into fbdiagnoses values (" . $genid . "," . $this->_quote($this->get("id")) . "," . $this->_quote($diagnosis) . ")";
				$result = $this->_execute($sql);
			}
		}
	}

	/**#@+
	 * Getters and Setters for Table: claimline
	 */

	
	/**
	 * Getter for Primary Key: claimline_id
	 */
	function get_claimline_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: claimline_id
	 */
	function set_claimline_id($id)  {
		$this->id = $id;
	}
	
	function set_diagnosis_1($value)  {
		if (strlen($value) > 0) {
			$this->diagnoses[] = $value;
		}
	}
	
	function set_diagnosis_2($value)  {
		if (strlen($value) > 0) {
			$this->diagnoses[] = $value;
		}
	}
	
	function set_diagnosis_3($value)  {
		if (strlen($value) > 0) {
			$this->diagnoses[] = $value;
		}
	}
	function set_diagnosis_4($value)  {
		if (strlen($value) > 0) {
			$this->diagnoses[] = $value;
		}
	}
	

	/**#@-*/
	
	function get_diagnosis($index) {
		if (isset($this->diagnoses[(int)$index])) {
			return $this->diagnoses[(int)$index];
		}
		return "";
	}
	
	
	/**
	 * Return all of the diagnoses codes associated with this claim line
	 *
	 * @return array
	 * @access protected
	 */
	function value_all_diagnoses() {
		return $this->diagnoses;
	}
	
	
	/**
	 * Sets the date of treatment, insuring it's ISO formatted
	 */
	function set_date_of_treatment($date) {
		$this->_setDate('date_of_treatment', $date);
	}
	
	/**
	 * Returns the date of treatment, using the configured formatting
	 *
	 * @return string
	 */
	function get_date_of_treatment() {
		return $this->_getDate('date_of_treatment');
	}
	
	
	/**
	 * Returns the month of the date of treatment
	 *
	 * @return string
	 */
	function value_date_of_treatment_month() {
		$date =& $this->date_of_treatment->getDate();
		return $date->month;
	}
	
	
	/**
	 * Returns the date of the date of treatment
	 *
	 * @return string
	 */
	function value_date_of_treatment_date() {
		$date =& $this->date_of_treatment->getDate();
		return $date->date;
	}
	
	
	/**
	 * Returns the year of the date of treatment
	 *
	 * @return string
	 */
	function value_date_of_treatment_year_short() {
		$date =& $this->date_of_treatment->getDate();
		return $date->year;
	}
	
	
	/**
	 * Returns an array of all of the modifiers for this claimline
	 *
	 * @return array
	 */
	function value_all_modifiers() {
		$modifierArray = array();
		
		if ($this->get("modifier") !== '0' && $this->get("modifier") != '') {
			$modifierArray[] = $this->get('modifier');
		}
		
		for ($i = 2; $i < 5; $i++) {
			
			if ($this->get("modifier_{$i}") !== '0' && $this->get("modifier_{$i}") != '') {
				$modifierArray[] = $this->get("modifier_{$i}");
			}
		}
		
		return $modifierArray;
	}
	
	
	/**
	 * Return the description for the procedure code
	 *
	 * @return string
	 * @access protected
	 */
	function value_procedure_description() {
		$qCode = $this->dbHelper->quote($this->get('procedure'));
		$query = "SELECT code_text FROM codes WHERE code = {$qCode}";
		return $this->dbHelper->getOne($query);
	}

	
	
	function is($field_name,$char_to_return = "") {
	$field_name = strtolower($field_name);
	if($char_to_return == ""){
			$rc = false;
	}else{		$rc = true;}

		if($rc){
			return "X";
		}else{
			return(" ");
		}
	}
	
	function is_not($field_name, $char_to_return = "") {
	if($char_to_return == ""){
			$rc = false;
	}else{		$rc = true;}

		if($this->is($field_name)){
			if($rc){
				return " ";
			}else{
				return(false);
			}
		}
		else{
			if($rc){
				return $char_to_return;
			}else{
				return(true);
			}
			
		}	
	}
}
?>
