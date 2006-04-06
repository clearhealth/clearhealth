<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class Company_DetailedProgramList_DS extends Datasource_sql
{
	/**
	 * An array of the various payer types
	 *
	 * @see _payerType()
	 * @var array
	 * @access private
	 */
	var $_payerTypes = array();
	
	function Company_DetailedProgramList_DS($company_id) {
		$company_id = EnforceType::int($company_id);
		$db =& new clniDB();
		
		$this->setup(
			Celini::dbInstance(),
			array(
				'cols' 	=> 'ip.name, payer_type, fsd.label as fee_schedule_name, insurance_program_id',
				'from' 	=> 
					'insurance_program AS ip 
					LEFT JOIN fee_schedule AS fsd USING(fee_schedule_id)',
				'where' => ' company_id = ' . $db->quote($company_id),
				'orderby' => 'ip.name'
			),
			array(
				'name' => 'Program Name',
				'payer_type' => 'Payer Type',
				'fee_schedule_name' => 'Fee Schedule')
		);

		$this->registerFilter('payer_type',array(&$this,'_payerType'));
	}
	
	
	/**
	 * Loads the payer_type enum and replaces key with value
	 *
	 * @param  int
	 * @return string
	 * @access private
	 */
	function _payerType($value) {
		if (count($this->_payerTypes) <= 0) {
			$em =& Celini::enumManagerInstance();
			$this->_payerTypes = $em->enumArray('payer_type');
		}
		return $this->_payerTypes[$value];
	}
}
