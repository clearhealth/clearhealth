<?php
/*

This system imports the Practice Management Components from OpenEMR into ClearHealth. There are many differences between ClearHealth and OpenEMR. This means that this script cannot be perfect, but it is much easier than doing it by hand.

Usage notes. 
This assumes that there is a database in clearhealth called import_map that has the the old_id, new_id, old_table_name, new_table_name

What it does.
Imports Patients -> Patients
Imports Subscribers -> Patients + Insured Relationship(there is no "subscriber" concept imbedded into clearhealth object/relational model, only patients who provide payer coverage for other patients)

What is should do.
* Imports Insurance Programs
* Imports Payers without initial duplicate entry
* Subsequent runs of the script do not duplicate Patients
* Subsequent runs of the script do not duplicate Subscribers
* Subsequent runs of the script do not duplicate Payers
* Import Providers
* Import Users
* Import Future Appointments
* Import EMR data compenents (Requires Comparable ClearHealth Forms)




This can be used to track whether an item is new or not allowing the script to be run over and over again, without fear of creating duplicate entries.

This import table is the only thing in the clearhealth system that should be accessed without objects.

*/
	require_once dirname(__FILE__)."/../../../../cellini/bootstrap.php";
	set_time_limit(0);

require_once "db.php";


if(!function_exists('mysql_connect')){
	die(" There is no mysql_connect looks like php was built without mysql ");
}


$link = mysql_connect($db_host,$db_user,$db_password)
        or die(" Connect to database failed ");

mysql_select_db($openemr_db) or die(" Could not select the database");


/*

Here we create the import array. To start with we will just have an array that we build to avoid between script duplicates.

*/
$imported_payers = array();
$imported_patients = array();
$imported_subscribers = array();

// use this to test trampling
//$theSQL = 'SELECT * FROM `patient_data` WHERE `lname` != "" LIMIT 0,1000';
$theSQL = 'SELECT * FROM `patient_data` WHERE `lname` != ""';

	$query = mysql_query($theSQL);



//start writing the file
echo "<?php\n";
echo "\$dataset =";
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

	} 




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
	}

	$insurance_info["insured_relationship_array"] = $insured_relationship_array;
	$patient_array['insurance_info'][]=$insurance_info;

	}

	$openemr_array[]=$patient_array;

}
mysql_close($link);

echo var_export($openemr_array);

echo "\n?>";




?>
