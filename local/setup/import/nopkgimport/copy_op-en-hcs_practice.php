<?php

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

$copy_rooms_sql = "
INSERT INTO `clearhealth`.`rooms`
SELECT *
FROM `op-en-hcs`.`rooms`";

if(mysql_query($copy_rooms_sql)){
	echo "Copied Rooms\n";
}
else{
	echo "Copy Rooms FAILED\n";
}

$copy_practice_sql = "
 INSERT INTO `clearhealth`.`practices`
SELECT *
FROM `op-en-hcs`.`practices` ";

if(mysql_query($copy_practice_sql)){
	echo "Copied Practices\n";
}
else{
	echo "Copy Practice FAILED\n";
}

$copy_buildings_sql = "
 INSERT INTO `clearhealth`.`buildings`
SELECT *
FROM `op-en-hcs`.`buildings`";

if(mysql_query($copy_buildings_sql)){
	echo "Copied Buildings\n";
}
else{
	echo "Copy Building FAILED\n";
}
?>
