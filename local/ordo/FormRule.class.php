<?php
/**
 * Object Relational Persistence Mapping Class for table: form_rule
 *
 * @package	com.uversainc.celini
 * @author	Marek Handze <marek@rise.pl>
 */

/**#@+
 * Required Libs
 */
require_once CELINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

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
		$this->_sequence_name = 'seq_enum';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of FormRule with this
	 */
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}
	
	function setupByName($name) {
		$this->set('form_name', $name);
		parent::populate('form_name');
	}

	/**
	 * Create and Populate an instance by name
	 */
	function &fromName($name) {
		$rule =& ORDataObject::factory('FormRule');
		$res = $enum->_execute("select form_rule_id from {$rule->_prefix}{$rule->_table} where form_name = ".$rule->_quote($name));
		if ($res && !$res->EOF) {
			$rule->setup($res->fields['fomm_rule_id']);
		}
		return $rule;
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('form_rule_id');
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
		$result = $this->_Execute($sql) or die ("Database Error: " . $this->_db->ErrorMsg());
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
// 			eval($command);
// 			echo $command;
			return $return;
		}
		else {
			return FALSE;
		}
	}
	
}
?>
