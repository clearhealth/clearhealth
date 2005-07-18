<?php

$clearhealth_db = "clearhealth";
$db_user = "root";
$db_password = "password";
$db_host = "localhost";
$newsequence=500000;

if(!function_exists('mysql_connect')){
	die(" There is no mysql_connect looks like php was built without mysql ");
}

$link = mysql_connect($db_host,$db_user,$db_password)
        or die(" Connect to database failed ");

mysql_select_db($clearhealth_db) or die(" Could not select the database");

$updatecounterSQL="UPDATE `sequences` SET `id` = '$newsequence' WHERE 1 LIMIT 1";	
if(mysql_query($updatecounterSQL)){
	echo "Updated Sequence to $newsequence to make space for imports\n";
}
else{
	echo "Update Sequence FAILED\n";
}

?>
