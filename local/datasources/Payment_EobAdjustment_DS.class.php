<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class Payment_EobAdjustment_DS extends Datasource_sql 
{
	var $_internalName = 'Payment_EobAdjustment';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function Payment_EobAdjustment_DS($paymentId) {
		$paymentId = EnforceType::int($paymentId);

		$em =& Celini::enumManagerInstance();
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "ifnull(codes.code,'Claim Level') code, eob_adjustment_type.value type, ea.value",
				'from'    => "eob_adjustment ea ".$em->joinSql('eob_adjustment_type','adjustment_type').
						" left join payment_claimline pc on ea.payment_claimline_id = pc.payment_claimline_id left join codes using(code_id)",
				'where'   => "ea.payment_id = {$paymentId}"
			),
			array('code' => 'Claimline', 'type' => 'Type','value'=>'Value'));
	}
}

