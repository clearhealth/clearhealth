<?php
	require_once dirname(__FILE__)."/../../../cellini/bootstrap.php";
	set_time_limit(0);
	$import_file = "patient_dataset.php";


	$default_state="CA";


	require $import_file;

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

	
	

	foreach($dataset as $person) {
		unset($patient);
		unset($subscriber);
		unset($address);
		unset($subscriber_address);
		unset($number);
		unset($subscriber_number);
		unset($importMap);	

	//	if($importMap->isImported('patient',$person['old_id']))
	//	{echo "patient has been imported \n";}

		$importMap =& ORDataObject::factory('ImportMap',$person['old_id'],'patient');
		
		if($importMap->_populated){
			echo "Patient Already In:".$person['old_id']."\n";
			$patient_key=$importMap->new_id;
		}
		else{ 

		$patient =& ORDataObject::factory('Patient');
		$patient->populate_array($person);
		$patient->set('identifier_type',1);
		$patient->set('type',1);
		$patient->persist();

		$address =& $patient->address();
		$address->populate_array($person);
		if($person["state"]==""){$person["state"]=$default_state;}
		if(strlen($person["state"])>2){$person["state"]=$default_state;}
		$address->set('state',$states[strtoupper($person['state'])]);
		$address->set('type',1);
		$address->persist();

		$number =& $patient->numberByType('Home');
		$number->set('number',$person['number']);
		$number->set('number_type',1);
		$number->persist();

		$patient_key=$patient->get('id');

		$importMap->set('old_table_name','patient');	
		$importMap->set('new_object_name','patient');	
		$importMap->set('new_id',$patient_key);	
		$importMap->persist();
		unset($importMap);
		echo "Imported Patient: ".$patient_key." From: ".$person["old_id"]."\n";
		}
				
		if(array_key_exists("insurance_info",$person)){
		$insurance_info=$person["insurance_info"];
		foreach($insurance_info as $insurance_info_instance){
		

		$subscriber_key=$patient_key;// default relationship is self.
		$payer_key="";
		$subscriber_key="";
		$program_key="";

		if(array_key_exists("subscriber_array",$insurance_info_instance)){


		$importMap =& ORDataObject::factory('ImportMap',$insurance_info_instance['subscriber_array']['old_id'],'insurance_data_subscriber');
		
		if($importMap->_populated){
			echo "Insurance Data Already In:".$insurance_info_instance['subscriber_array']['old_id']."\n";
			$subscriber_key=$importMap->new_id;
		}
		else{ 

			$subscriber =& ORDataObject::factory('Person');
			$subscriber->populate_array($insurance_info_instance["subscriber_array"]);
			$subscriber->set('identifier_type',1);
			$subscriber->set('type',1);
			$subscriber->persist();

			$subscriber_address =& $subscriber->address();
			$subscriber_address->populate_array($insurance_info_instance["subscriber_array"]);
			if($insurance_info_instance["subscriber_array"]['state']=='')
				{$insurance_info_instance["subscriber_array"]['state']=$default_state;}
			$subscriber_address->set('state',$states[$insurance_info_instance["subscriber_array"]['state']]);
			$subscriber_address->set('type',1);
			$subscriber_address->persist();

			$subscriber_number =& $subscriber->numberByType('Home');
			$subscriber_number->set('number',$insurance_info_instance["subscriber_array"]['number']);
			$subscriber_number->set('number_type',1);
			$subscriber_number->persist();

			$subscriber_key=$subscriber->get('id');

			$importMap->set('old_table_name','insurance_data_subscriber');	
			$importMap->set('new_object_name','subscriber');	
			$importMap->set('new_id',$subscriber_key);	
			$importMap->persist();
			unset($importMap);

			echo "Imported Subscriber: ".$subscriber_key.
			     " From: ".$insurance_info_instance["subscriber_array"]["old_id"]."\n";

			} //endelse not imported
			} //endif (array_key_exists...


		
		if(array_key_exists("payer_array",$insurance_info_instance)){
	

		$importMap =& ORDataObject::factory('ImportMap',$insurance_info_instance['payer_array']['old_id'],'insurance_data_payer');
		
		if($importMap->_populated){
			echo "Insurance Data Already In:".$insurance_info_instance['payer_array']['old_id']."\n";
			$company_key=$importMap->new_id;
		}
		else{ 

	
			$company =& ORDataObject::factory('Company');
			$company->populate_array($insurance_info_instance["payer_array"]);
			$company->persist();
			$company_key=$company->get('id');

			echo "Imported Company: ".$company_key.
			     " From: ".$insurance_info_instance["payer_array"]["old_id"]."\n";

			$importMap->set('old_table_name','insurance_data_payer');	
			$importMap->set('new_object_name','subscriber');	
			$importMap->set('new_id',$subscriber_key);	
			$importMap->persist();
			unset($importMap);
		} //endelse not imported.



		}

		if(array_key_exists("program_array",$insurance_info_instance)){


		$importMap =& ORDataObject::factory('ImportMap',$insurance_info_instance['program_array']['old_id'],'insurance_data_program');
		
		if($importMap->_populated){
			echo "Insurance Data Already In:".$insurance_info_instance['program_array']['old_id']."\n";
			$subscriber_key=$importMap->new_id;
		}
		else{ 	
			$program =& ORDataObject::factory('InsuranceProgram');
			$program->populate_array($insurance_info_instance["program_array"]);
			$program->set('company_id',$company_key);
			$program->persist();

			$program_key=$program->get('id');
			echo "Imported Program: ".$program_key.
			     " From: ".$insurance_info_instance["program_array"]["old_id"]."\n";

			$importMap->set('old_table_name','insurance_data_program');	
			$importMap->set('new_object_name','subscriber');	
			$importMap->set('new_id',$program_key);	
			$importMap->persist();
			unset($importMap);
		} //endelse not imported.


			} //endif (array_key program_array

		if(array_key_exists("insured_relationship_array",$insurance_info_instance)){
		if(array_key_exists("old_id",$insurance_info_instance['insured_relationship_array'])){

		$importMap =& ORDataObject::factory('ImportMap',$insurance_info_instance['insured_relationship_array']['old_id'],'insurance_data_insured_relationship');
		
		if($importMap->_populated){
			echo "Insurance Data Already In:".$insurance_info_instance['insured_relationship_array']['old_id']."\n";
			$program_key=$importMap->new_id;
		}
		else{ 
			$program =& ORDataObject::factory('InsuredRelationship');
			$program->populate_array($insurance_info_instance["insured_relationship_array"]);
			$program->set('insurance_program_id',$program_key);
			$program->set('person_id',$patient_key);
			$program->set('subscriber_id',$subscriber_key);
			$program->persist();
			$program_key=$program->get('id');

			$importMap->set('old_table_name','insurance_data_insured_relationship');
			$importMap->set('new_object_name','insured_relationship');	
			$importMap->set('new_id',$program_key);	
			$importMap->persist();
			unset($importMap);
		} //endelse not imported.


		//	echo "Imported Program: ".$program_key.
		//	     " From: ".$insurance_info_instance["insured_relationship_array"]["old_id"]."\n";
			} //endif array_key old_id
			} //endif (array_key program_array





		} //end foreach insurance_info
		}//endif array_key insurance_info


		flush();
	}
?>
