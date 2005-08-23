<?php
/*

Script Notes:
unpacks the openemr database into a php file with data closely matching the structure in ClearHealth. It should be run as squeeze_openemr.php > dataset.php


General Notes:
This system imports the Practice Management Components from OpenEMR into ClearHealth. There are many differences between ClearHealth and OpenEMR. This means that this script cannot be perfect, but it is much easier than doing it by hand.

Usage notes. 
This assumes that there is a database in clearhealth called import_map that has the the old_id, new_id, old_table_name, new_table_name

What it does.
Imports Patients -> Patients
Imports Subscribers -> Patients + Insured Relationship(there is no "subscriber" concept imbedded into clearhealth object/relational model, only patients who provide payer coverage for other patients)
Imports Subcribers -> Insurance Programs
Imports Insurance_Companies -> Companies


Multiple runs of this script are protected because it does not import any id again that it records in import map. So it should be safe to run over and over again.


*/
$config['db_type'] = "mysql";
$config['db_host'] = "localhost";
$config['db_user'] = "root"; 
$config['db_password'] = "root";
$config['db_name'] = "clearhealth";
$config['openemr_db'] = "openemr";
$GLOBALS['config'] = $config;

if (!defined('CELLINI_ROOT')) {
        define('CELLINI_ROOT',dirname(__FILE__) . "/../../../../cellini/");
}                                                                                 
/**
* Base application dir
*/
if (!defined('APP_ROOT')) {
        define('APP_ROOT',realpath(CELLINI_ROOT."/../"));
}
                                                                                
/**
* Base module dir
*/
if (!defined('MODULE_ROOT')) {
        define('MODULE_ROOT',realpath(APP_ROOT."/modules"));
}

require_once CELLINI_ROOT . "config.php";


//require_once dirname(__FILE__)."/../../../../cellini/bootstrap.php";
//require_once dirname(__FILE__)."/../../../../cellini/ordo/ORDataObject.class.php";
set_time_limit(0);


$data = import_openemr();
load_from_varexport($data);

function import_openemr()  {
	$config = $GLOBALS['config'];
	if(!function_exists('mysql_connect')){
		die(" There is no mysql_connect looks like php was built without mysql ");
	}
	mysql_select_db($config['openemr_db']) or die(" Could not select the database");


/*

Here we create the import array. To start with we will just have an array that we build to avoid between script duplicates.

*/
	$imported_payers = array();
	$imported_patients = array();
	$imported_subscribers = array();

	// use this to test trampling
	$theSQL = 'SELECT * FROM `patient_data` WHERE `lname` != "" LIMIT 0,1000';
	//$theSQL = 'SELECT * FROM `patient_data` WHERE `lname` != ""';

	$query = mysql_query($theSQL);



	//start writing the file
	//echo "<?php\n";
	//echo "\$dataset =";
	$openemr_data = array();

	// For each Patient
	while ($result = mysql_fetch_array($query)) {
	

		// Logic here for between-run duplicates

		$patient_array = array(
		"first_name" => $result["fname"],
		"middle_name" => $result["mname"],
		"last_name" => $result["lname"],
		"line1" => $result["street"],
		"city" => $result["city"],
		"state" => $result["state"],
		"zip" => $result["postal_code"],
		"number" => $result["phone_home"],
		"record_number" => $result["id"],
		"old_id" => $result["id"],
		"identifier" => $result["ss"],
		"date_of_birth" => $result["DOB"]
		);
	
		//echo "\n//PATIENT: Squeezed\n";
		//echo "\n";
	



		$subscriberSQL = 'SELECT *
			FROM `insurance_data`
			WHERE ! ( `provider` = ""
			AND `plan_name` = ""
			AND `policy_number` = ""
			AND `group_number` = ""
			AND `subscriber_ss` = "" ) AND `pid` = "'.$result["id"].'"';


		$subscriber_query = mysql_query($subscriberSQL);

	

//For Each Subscriber	
	while ($subscriber_result = mysql_fetch_array($subscriber_query)) {


			$insurance_info=array();

			if(($subscriber_result["subscriber_relationship"]!="self")&&
			   (($subscriber_result["subscriber_fname"]!="")||
       		 	    ($subscriber_result["subscriber_lname"]!="")||
  		          ($subscriber_result["subscriber_ss"]!=""))
			  ){
				$subscriber_array = array(
				"old_id" => $subscriber_result["id"],// to de-duplicate in the next script
				"first_name" => $subscriber_result["subscriber_fname"],
				"middle_name" => $subscriber_result["subscriber_mname"],
				"last_name" => $subscriber_result["subscriber_lname"],
				"line1" => $subscriber_result["subscriber_street"],
				"city" => $subscriber_result["subscriber_city"],
				"state" => $subscriber_result["subscriber_state"],
				"zip" => $subscriber_result["subscriber_postal_code"],
				"number" => $subscriber_result["subscriber_phone"],
				"record_number" => $subscriber_result["id"],
				"identifier" => $subscriber_result["subscriber_ss"],
				"date_of_birth" => $subscriber_result["subscriber_DOB"]
				);
	

				$insurance_info["subscriber_array"] = $subscriber_array;
			}	
		// Payer. 

		$payertype="private";

		if(($subscriber_result["provider"]!="")){
			$payerSQL = "SELECT * FROM `insurance_companies` WHERE `id` = ".$subscriber_result["provider"];
			$payer_query = mysql_query($payerSQL);

			while ($payer_result = mysql_fetch_array($payer_query)){

				$payer_array = array(
				"old_id" => $payer_result["id"],// to de-duplicate in the next script
				"name" => $payer_result["name"],
				"description" => $payer_result["name"],
				"notes" => $payer_result["name"]."imported from OpenEMR",
				"initials" => "",
				"url" => "",
				"is_historic" => "",
				);
			}

			$payertype="private";
			if($payer_result['freeb_type']=6){$payertype="bcbs";}
			if($payer_result['freeb_type']=5){$payertype="champus";}
			if($payer_result['freeb_type']=3){$payertype="medical";}
			if($payer_result['freeb_type']=2){$payertype="medicare";}

			$insurance_info["payer_array"] = $payer_array;

		}//if(subscriber_result["provider"]) 




		if(($subscriber_result["provider"]!="")||
	       	    ($subscriber_result["plan_name"]!="")){
		$program_array = array(
		"old_id" => $subscriber_result["id"],// to de-duplicate in the next script
		"payer_id" => $subscriber_result["provider"],// to associate with the inco output
		"insurance_program_id" => "",//auto generate
		"payer_type" => "",//enumeration based
		"company_id" => "",//already made a payer?
		"name" => $subscriber_result["plan_name"],
		"fee_schedule_id" => $payertype,
		);

		//echo "//PROGRAM: Squeezed\n";
		$insurance_info["program_array"] = $program_array;
		}	

		if(($subscriber_result["subscriber_relationship"]!="")||
	       	    ($subscriber_result["copay"]!="")||
	       	    ($subscriber_result["group_number"]!="")||
	           ($subscriber_result["plan_name"]!="")){
			$insured_relationship_array = array(
			"old_id" => $subscriber_result["id"],// to de-duplicate in the next script
			"insured_relationship_id" => "",// auto generate
			"insurance_program_id" => "",// make program 
			"person_id" => "",//make patient first
			"subscriber_id" => "",// make subscriber patient first
			"subscriber_to_patient_relationship" => $subscriber_result["subscriber_relationship"],// Enumeration
			"copay" => $subscriber_result["copay"],
			"assigning" => "yes",// OpenEMR doesnt know
			"group_name" => $subscriber_result["plan_name"],// Should I do this??
			"group_number" => $subscriber_result["group_number"],
			"default_provider" => "",// ignore
			"program_order" => "",// ignore
			);
		//echo "//RELATIONSHIP: Squeezed \n";
	
		}else{
			$insured_relationship_array = array(
			"subscriber_to_patient_relationship" => "self"		
			);
		}//if subscriber_relationship...

		$insurance_info["insured_relationship_array"] = $insured_relationship_array;
		$patient_array['insurance_info'][]=$insurance_info;

	}// each subscriber

	$openemr_array[]=$patient_array;

    }//each patient


return($openemr_array);


}

function load_from_varexport($dataset) {
	$default_state="CA";


//	echo "<pre>\n";

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
			$company->set('types',array(1));// makes it an insurance company
			$company->persist();
			$company_key=$company->get('id');


		// Does not work...
		//	$company->set('company_type','1');

		// Does not work either...
		//	$company_type =& ORDataObject::factory('CompanyType');
		//	$company_type->set("$company_key",'1');
		//	$company_type->persist();

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
}
?>
