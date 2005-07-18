<?php
	require_once dirname(__FILE__)."/../../../cellini/bootstrap.php";
	set_time_limit(0);
	$import_file = "ch_fake_people.php";


	require $import_file;

	echo "<pre>\n";

	$address =& ORDataObject::factory('Address');
	$states = array_flip($address->getStatelist());

	foreach($data as $person) {
		unset($patient);
		unset($address);
		unset($number);

		$patient =& ORDataObject::factory('Patient');
		$patient->populate_array($person);
		$patient->set('identifier_type',1);
		$patient->set('type',1);
		$patient->persist();

		$address =& $patient->address();
		$address->populate_array($person);
		$address->set('state',$states[$person['state']]);
		$address->set('type',1);
		$address->persist();

		$number =& $patient->numberByType('Home');
		$number->set('number',$person['number']);
		$number->set('number_type',1);
		$number->persist();

		echo $patient->get('id')."\n";
		flush();
	}
?>
