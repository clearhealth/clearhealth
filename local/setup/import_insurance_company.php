<?php
	require_once dirname(__FILE__)."/../../cellini/bootstrap.php";
	set_time_limit(0);

$data = array(
		array(
			'name'	=> 'Test Company',
			'description' => 'A test company',
			'url' => 'http://example.com',
			'notes' => 'notes blah',
			'numbers' => array(
				array(
					'number_type' => 'Primary',
					'number' => '555-555-5555'
				),
			),
			'addresses' => array(
				array(
					'name' => 'Main',
					'type' => 'Billing',
					'line1' => '1234 some street',
					'line2' => 'suite 34',
					'city'	=> 'Blah',
					'state' => 'AZ',
					'postal_code' => '12345'
				),
			),
			'programs' => array(
				array(
					'name' => 'My program',
					'payer_type' => 'medicare',
					'fee_schedule' => 'test'
				)
			)
		)
	);


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
			$address =& ORDataObject::factory('CompanyAddress',0,$company->geT('id'));
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
?>
