<?php
/**
 * Object Relational Persistence Mapping Class for table: payer_group
 *
 * @package	com.clear-health.clearhealth
 * @author	ClearHealth Inc.
 */
class PayerGroup extends ORDataObject {

	/**#@+
	 * Fields of table: payer_group mapped to class members
	 */
	var $payer_group_id		= '';
	var $name		= '';
	var $description		= '';
	/**#@-*/

	var $_payers = null;

	/**
	 * DB Table
	 */
	var $_table = 'payer_group';

	/**
	 * Primary Key
	 */
	var $_key = 'payer_group_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'PayerGroup';

	/**
	 * Handle instantiation
	 */
	function PayerGroup() {
		parent::ORDataObject();
	}

	function get_payers() {
		if(!is_null($this->_payers)) {
			return $this->_payers;
		}
		$db =& $this->dbHelper;
		$sql = "
		SELECT
			insurance_program_id
		FROM
			insurance_payergroup
		WHERE
			payer_group_id = ".$db->quote($this->get('id'))."
		ORDER BY
			`order` ASC
		";
		$res = $db->execute($sql);
		$payers = array();
		for($res->MoveFirst();!$res->EOF;$res->MoveNext()) {
			$payers[$res->fields['insurance_program_id']] =& Celini::newORDO('InsuranceProgram',$res->fields['insurance_program_id']);
		}
		$this->_payers = $payers;
		return $payers;
	}
	
	function valueList_payer_id() {
		$db =& $this->dbHelper;
		$sql = "
		SELECT
			insurance_program_id
		FROM
			insurance_payergroup
		WHERE
			payer_group_id = ".$db->quote($this->get('id'))."
		ORDER BY
			`order` ASC
		";
		$res = $db->execute($sql);
		$payers = array();
		for($res->MoveFirst();!$res->EOF;$res->MoveNext()) {
			$payers[] = $res->fields['insurance_program_id'];
		}
		return $payers;
	}
}
?>