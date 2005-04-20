<?php
/*
 	This is for mirroring one clearhealth calendar to another, in a master slave relationship. Cannot think of why someone would want this kind of helper script outside of exactly the situation it was written for.


*/

$open_db = "op-en-hcs";
$clearhealth_db = "clearhealth";
$db_user = "root";
$db_password = "password";
$db_host = "localhost";

if(!function_exists('mysql_connect')){
	die(" There is no mysql_connect looks like php was built without mysql ");
}

$link = mysql_connect($db_host,$db_user,$db_password)
        or die(" Connect to database failed ");

mysql_select_db($open_db) or die(" Could not select the database");

$scheduleSQL = 'SELECT * FROM `schedules` WHERE 1 ';

$query = mysql_query($scheduleSQL);

$schedule_array = array();

while ($result = mysql_fetch_array($query)) { $schedule_array[]=$result; }

$eventSQL = 'SELECT * FROM `events` WHERE 1 ';

$query = mysql_query($eventSQL);

$event_array = array();

while ($result = mysql_fetch_array($query)) { $event_array[]=$result; }


$occurSQL = 'SELECT * FROM `occurences` WHERE 1 ';

$query = mysql_query($occurSQL);

$occur_array = array();

while ($result = mysql_fetch_array($query)) { $occur_array[]=$result; }


//start writing the file
echo "<?php\n";
echo "\$schedules =";
echo var_export($schedule_array);
echo ";\n";
echo "\$events =";
echo var_export($event_array);
echo ";\n";
echo "\$occurences =";
echo var_export($occur_array);
echo ";\n";

echo "\n?>";


?>
