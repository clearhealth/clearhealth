<?php
	require_once dirname(__FILE__)."/../../../../cellini/bootstrap.php";
	set_time_limit(0);

	$import_file = "ch_payers.php";


	require $import_file

	echo "<pre>\n";

	$address =& ORDataObject::factory('Address');
	$states = array_flip($address->getStatelist());

	$ip =& ORDataObject::factory('InsuranceProgram');
	$payerTypes = array_flip($ip->getPayerTypeList());

	$number =& ORDataObject::factory('CompanyNumber');
	$numberTypes = array_flip($number->getTypeList());

	$address =& ORDataObject::factory('CompanyAddress');
	$addressTypes = array_flip($address->getTypeList());

	$feeSchedule =& ORDataObject::factory('FeeSchedule');
	$schedules = array_flip($feeSchedule->toArray());

}// end reading while loop


// starting the writing foreach loop

	foreach($data as $payer) {
		unset($company);
		unset($address);
		unset($number);
		unset($program);

		$company =& ORDataObject::factory('Company');
		$company->populate_array($payer);
		$company->set('types',array(1));
		$company->persist();

		foreach($payer['addresses'] as $addr) {
			unset($address);
			$address =& ORDataObject::factory('CompanyAddress',0,$company->get('id'));
			$address->populate_array($addr);
			$address->set('state',$states[$addr['state']]);
			$address->set('type',$addressTypes[$addr['type']]);
			$address->persist();
		}

		foreach($payer['numbers'] as $num) {
			$number =& ORDataObject::factory('CompanyNumber',0,$company->get('id'));
			$number->set('number',$num['number']);
			$number->set('number_type',$numberTypes[$num['number_type']]);
			$number->persist();
		}

		foreach($payer['programs'] as $pro) {
			$program =& ORDataObject::factory('InsuranceProgram',0,$company->get('id'));
			$program->populate_array($pro);
			$program->set('payer_type',$payerTypes[$pro['payer_type']]);
			$program->set('fee_schedule_id',$schedules[$pro['fee_schedule']]);
			$program->persist();
		}

		echo $company->get('id')."\n";
		flush();
	}

mysql_close($link);
?>
