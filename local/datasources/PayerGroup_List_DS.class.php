<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class PayerGroup_List_DS extends Datasource_sql
{
	var $_usedPayerGroups = array();
	var $_forpgForm = false;
	
	function PayerGroup_List_DS() {
		
		$this->setup(
			Celini::dbInstance(),
			array(
				'cols' 	=> 'pg.payer_group_id name,CONCAT(co.name,\'->\',ip.name) AS program,ipg.order, ip.insurance_program_id',
				'from' 	=> 
					'payer_group AS pg
					LEFT JOIN insurance_payergroup AS ipg USING(payer_group_id)
					LEFT JOIN insurance_program  AS ip USING(insurance_program_id)
					LEFT JOIN company AS co USING(company_id) order by pg.name,ipg.order',
				'orderby' => 'ipg.order ASC,pg.name ASC'
			),
			array(
				'name' => 'Group Name',
				'program' => 'Program',
				'order' => 'Order'
			)
		);
		
		$this->registerFilter('name',array(&$this,'_pgLink'));

	}
	
	function _pgLink($value) {
		if(!isset($this->_usedPayerGroups[$value])) {
			$pg =& Celini::newORDO('PayerGroup',$value);
			$this->_usedPayerGroups[$value] = true;
			return '<a href=\''.Celini::link('edit')."id={$value}'>".$pg->get('name')."</a>";
		}
		return '';
	}
	

}
