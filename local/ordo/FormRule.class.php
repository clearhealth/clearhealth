<?php
/**
 * Object Relational Persistence Mapping Class for table: form_rule
 *
 * @package	com.uversainc.clearhealth
 * @author	Marek Handze <marek@rise.pl>
 */

/**
 * Object Relational Persistence Mapping Class for table: form_rule
 *
 * @package	com.uversainc.celini
 */
class FormRule extends ORDataObject {

	/**#@+
	 * Fields of table: form_rule mapped to class members
	 */
	var $form_rule_id = '';
	var $field_name	= '';
	var $rule_name	= '';
	var $operator	= '';
	//if TRUE value contains another field definition, if FALSE value contains simple value
	var $value_type = '';
	var $value		= '';
	var $message	= '';
	/**#@-*/


	/**
	 * Hack to allow this object to properly map to the file name since PHP 4 
	 * doesn't maintain case for classes.
	 *
	 * @var string
	 * @access protected
	 */
	var $_internalName = 'FormRule';
	
	
	/**
	 * {@inheritdoc}
	 */
	var $_key = 'form_rule_id';
	
	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FormRule($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'form_rule';
	}

	
	function &ruleList() {
		$ds =& new Datasource_sql();
		$sql = array(
			'cols' 	=> "form_rule_id, field_name, rule_name, operator ",
			'from' 	=> "$this->_table",
			'orderby' => 'form_rule_id'
			);
		$cols = array('field_name' => 'Form Name', 'rule_name' => 'Rule Name', 'operator' => 'Operator');


		$ds->setup($this->_db,$sql,$cols);
		return $ds;
	}
	
	function checkFieldRule ($field_name, $field_value) {
	
		$sql = "SELECT operator, value, message FROM " . $this->_table . " WHERE field_name = '" . $field_name."'";
		$result = $this->dbHelper->execute($sql);
		$ar = array();
		
		$first = 1;	
		while ($result && !$result->EOF) {
			if ($first == 1) {
				$rules = "\$field_value ".$result->fields['operator']." ".$result->fields['value'];
				$first=0;
			}
			else {
				$rules .= "AND ".$field_value." ".$result->fields['operator']." ".$result->fields['value'];
			}
			$messages[] = $result->fields['message'];
			$result->MoveNext();
		}

		if (isset($rules)) {
			
			$return = array();
			$command = 'if ('.$rules.') { $return = $messages; } else {$return = FALSE;}';
 			eval($command);
// 			echo $command;
			return $return;
		}
		else {
			return FALSE;
		}
	}
	
	
	
	 /**#@+
	 * ValueType getters
	 */
	function getValueTypeList() {
		$list = $this->_load_enum('value_type',false);
		return array_flip($list);
	}
	/**#@-*/
	
}
?>
