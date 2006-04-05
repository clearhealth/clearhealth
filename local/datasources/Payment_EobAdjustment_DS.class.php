<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class Payment_EobAdjustment extends Datasource_sql 
{
	var $_internalName = 'Payment_EobAdjustment';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function Payment_EobAdjustment($paymentId) {
		$paymentId = EnforceType::int($paymentId);

		$em =& Celini::enumManagerInstance();
		$
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "type.value type, value",
				'from'    => "eob_adjustment ".$em->joinSql('eob_adjustment_type','type'),
				'orderby' => 'type.value',
				'where'   => "payment_id = {$payment_id}"
			),
			array('type' => 'Type','value'=>'Value'));
	}
}

