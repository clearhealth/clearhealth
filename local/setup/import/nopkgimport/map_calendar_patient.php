<?php
/*
	This is a potentially useful script for importing a some outside calendar into ClearHealth.

	It uses a "dataset.php" file which should be a var_export with the follwing arrays. occurences, schedules, and events. These should correspond to the ClearHealth types. This is a bulk import script so it violates the OOP in favor of speed. It will need to be updated as the calendar is updated.


*/


		
$clearhealth_db = "clearhealth";
$db_user = "root";
$db_password = "password";
$db_host = "localhost";

if(!function_exists('mysql_connect')){
	die(" There is no mysql_connect looks like php was built without mysql ");
}

$link = mysql_connect($db_host,$db_user,$db_password)
        or die(" Connect to database failed ");

mysql_select_db($clearhealth_db) or die(" Could not select the database");
	

$theSQL = 'SELECT * FROM `occurences` WHERE 1';

	$query = mysql_query($theSQL);



//Insert and update Occurances	
while ($a_occurence = mysql_fetch_array($query)) {

	$id=$a_occurence['id'];
	$event_id=$a_occurence['event_id'];
	$start=$a_occurence['start'];
	$end=$a_occurence['end'];
	$notes=$a_occurence['notes'];
	$location_id=$a_occurence['location_id'];
	$user_id=$a_occurence['user_id'];
	$last_change_id=$a_occurence['last_change_id'];
	$external_id=$a_occurence['external_id'];
	$reason_code=$a_occurence['reason_code'];

	$check_import_map_sql = "SELECT * FROM `import_map` WHERE `old_table_name` = 'patient' AND `old_id` = ".$external_id; 

	$query = mysql_query($check_import_map_sql);

	$import_result = mysql_fetch_array($query)

	$new_external_id=$import_result('new_id');

	$update_occurence_sql = "UPDATE `occurences` SET 
			`event_id` = '$event_id', 
			`start` = '$start', 
			`end` = '$end', 
			`notes` = '$notes', 
			`location_id` = '$location_id', 
			`user_id` = '$user_id', 
			`last_change_id` = '$last_change_id', 
			`external_id` = '$new_external_id', 
			`reason_code` = '$reason_code' 
			WHERE `id` = $id LIMIT 1";


	if(mysql_query($update_occurence_sql)){	echo 'Update Occurance '.$a_occurence['id']."\n";}
	else{
					echo 'Update Occurance Failed '.$a_occurence['id']."\n";}
	}

		
}






?>
