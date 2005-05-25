<?php
/*
	This is a potentially useful script for importing a some outside calendar into ClearHealth.

	It uses a "dataset.php" file which should be a var_export with the follwing arrays. occurences, schedules, and events. These should correspond to the ClearHealth types. This is a bulk import script so it violates the OOP in favor of speed. It will need to be updated as the calendar is updated.


*/


include "dataset.php";
		
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
	

//Insert and update Schedules	
foreach($schedules as $a_schedule)
{

	$id=$a_schedule['id'];
	$schedule_code=$a_schedule['schedule_code'];
	$name=$a_schedule['name'];
	$description_long=$a_schedule['description_long'];
	$description_short=$a_schedule['description_short'];
	$practice_id=$a_schedule['practice_id'];
	$user_id=$a_schedule['user_id'];
	$room_id=$a_schedule['room_id'];

	$checkscheduleSQL='SELECT * FROM `schedules` WHERE `id` = '.$a_schedule['id'];	
	$query = mysql_query($checkscheduleSQL);

	if($result = mysql_fetch_array($query)){
//Note: we dont update the patient id b/c it is already correctly set by the import script.
	$update_schedule_sql = "UPDATE `schedules` SET 
			`schedule_code` = '$schedule_code', 
			`name` = '$name', 
			`description_long` = '$description_long', 
			`description_short` = '$description_short', 
			`practice_id` = '$practice_id', 
			`user_id` = '$user_id', 
			`room_id` = '$room_id' 
			WHERE `id` = $id LIMIT 1";

	if(mysql_query($update_schedule_sql)){	echo 'Update Schedule '.$a_schedule['id']."\n";}else{
						echo 'Update Schedule Failed '.$a_schedule['id']."\n";}
	}else{



	$insert_schedule_sql = "INSERT INTO `schedules` (
		`id`, 
		`schedule_code`, 
		`name`, 
		`description_long`, 
		`description_short`, 
		`practice_id`, 
		`user_id`, 
		`room_id`) 
		VALUES (
		'$id', 
		'$schedule_code', 
		'$name', 
		'$description_long', 
		'$description_short', 
		'$practice_id', 
		'$user_id', 
		'$room_id')";	

	if(mysql_query($insert_schedule_sql)){	echo 'Insert Schedule '.$a_schedule['id']."\n";}else{
						echo 'Insert Schedule Failed '.$a_schedule['id']."\n";}
	}	
		
}
		
/*
//Insert and update Events	
foreach($events as $a_event)
{

	$id=$a_event['id'];
	$title=$a_event['title'];
	$description=$a_event['description'];
	$website=$a_event['website'];
	$contact_person=$a_event['contact_person'];
	$email=$a_event['email'];
	$foreign_id=$a_event['foreign_id'];

	$checkeventSQL='SELECT * FROM `events` WHERE `id` = '.$a_event['id'];	
	$query = mysql_query($checkeventSQL);

	if($result = mysql_fetch_array($query)){
	$update_event_sql = "UPDATE `events` SET 
			`title` = '$title', 
			`description` = '$description', 
			`website` = '$website', 
			`contact_person` = '$contact_person', 
			`email` = '$email', 
			`foreign_id` = '$foreign_id' 
			WHERE `id` = $id LIMIT 1";


	if(mysql_query($update_event_sql)){	echo 'Update Event '.$a_event['id']."\n";}else{
						echo 'Update Event Failed '.$a_event['id']."\n";}
	}else{
	$insert_event_sql = "INSERT INTO `events` (
		`id`, 
		`title`, 
		`description`, 
		`website`, 
		`contact_person`, 
		`email`, 
		`foreign_id`) 
		VALUES (
		'$id', 
		'$title', 
		'$description', 
		'$website', 
		'$contact_person', 
		'$email', 
		'$foreign_id')";	


	if(mysql_query($insert_event_sql)){	echo 'Insert Event '.$a_event['id']."\n";}else{
						echo 'Insert Event Failed '.$a_event['id']."\n";}
	}	
		
}

*/

//Insert and update Occurances	
foreach($occurences as $a_occurence)
{

	$a_occurence = my_clean_array($a_occurence);

	$id= $a_occurence['id'];
	$event_id= $a_occurence['event_id'];
	$start= $a_occurence['start'];
	$end= $a_occurence['end'];
	$notes= $a_occurence['notes'];
	echo "The notes I will use $notes \n";
	$location_id= $a_occurence['location_id'];
	$user_id= $a_occurence['user_id'];
	$last_change_id= $a_occurence['last_change_id'];
	$external_id= $a_occurence['external_id'];
	$reason_code= $a_occurence['reason_code'];

	$checkoccurenceSQL='SELECT * FROM `occurences` WHERE `id` = '.$a_occurence['id'];	
	$query = mysql_query($checkoccurenceSQL);

	if($result = mysql_fetch_array($query)){
	$update_occurence_sql = "UPDATE `occurences` SET 
			`event_id` = '$event_id', 
			`start` = '$start', 
			`end` = '$end', 
			`notes` = '$notes', 
			`location_id` = '$location_id', 
			`user_id` = '$user_id', 
			`last_change_id` = '$last_change_id', 
			`external_id` = '$external_id', 
			`reason_code` = '$reason_code' 
			WHERE `id` = $id LIMIT 1";


	if(mysql_query($update_occurence_sql)){	echo 'Update Occurance '.$a_occurence['id']."\n";}else{
						echo 'Update Occurance Failed '.$a_occurence['id']."\n";}
	}else{
/* 
We need to grap the patient map for this schedule item and use the new patient id in the appointment...
*/

	$check_import_map_sql = "SELECT * FROM `import_map` WHERE `old_table_name` = 'patient' AND `old_id` = ".$external_id;
	$query = mysql_query($check_import_map_sql);
	$import_result = mysql_fetch_array($query);
	$new_external_id=$import_result('new_id');

	$insert_occurence_sql = "INSERT INTO `occurences` (
		`id`, 
		`event_id`, 
		`start`, 
		`end`, 
		`notes`, 
		`location_id`, 
		`user_id`,
		`last_change_id`,
		`external_id`,
		`reason_code`) 
		VALUES (
		'$id', 
		'$event_id', 
		'$start', 
		'$end', 
		'$notes', 
		'$location_id', 
		'$user_id', 
		'$last_change_id', 
		'$new_external_id', 
		'$reason_code')";	


	if(mysql_query($insert_occurence_sql)){	echo 'Insert Occurance '.$a_occurence['id']."\n";}else{
						echo 'Insert Occurance Failed '.$a_occurence['id']."\n";}
	}	
		
}



function my_clean_array ($array){ // remove special characters


$evil_strings=array("\'","'");

	foreach($array as $key => $value)
	{
		echo "Checking $key with $value \n";
		
		if(strstr($value,"'")) {
			echo "Got one in $value \n";
			$value=str_replace($evil_strings,"",$value);
			$array[$key]=$value;
			echo "Replaced with $value \n";
		}

	
	}

	return($array);

}


?>
