<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class Encounter_PayerGroup_DS extends Datasource_sql
{
	var $_usedPayerGroups = array();
	var $_patient_id = false;
	var $_selectedPayerGroup = null;
	
	function Encounter_PayerGroup_DS($patient_id=null,$selected = null) {
		$this->_patient_id = $patient_id;
		$this->_selectedPayerGroup = $selected;
		$this->setup(
			Celini::dbInstance(),
			array(
				'cols' 	=> 'pg.payer_group_id name,CONCAT(co.name,\'=>\',ip.name) AS program,ipg.`order`, insurance_program_id',
				'from' 	=> 
					'payer_group AS pg
					INNER JOIN insurance_payergroup AS ipg USING(payer_group_id)
					INNER JOIN insurance_program  AS ip USING(insurance_program_id)
					INNER JOIN insured_relationship AS ir ON(ip.insurance_program_id=ir.insurance_program_id)
					INNER JOIN company AS co USING(company_id) ',
				'orderby' => 'ipg.order ASC,pg.name ASC',
				'where' => 'ir.active = 1 AND ir.person_id = '.$patient_id
			),
			array(
				'name' => 'Group Name',
				'program' => 'Program'
			)
		);
		
		$this->registerFilter('name',array(&$this,'_pgLink'));

	}
	
	function _pgLink($value) {
		if(!isset($this->_usedPayerGroups[$value])) {
			$pg =& Celini::newORDO('PayerGroup',$value);
			$this->_usedPayerGroups[$value] = true;
			$selected = ($value == $this->_selectedPayerGroup) ? "checked='checked'" : '';
			$input = "<input type='radio' name='encounter[payer_group]' value='{$value}' $selected />&nbsp;";
			return $input.$pg->get('name');
		}
		return '';
	}
	

}
