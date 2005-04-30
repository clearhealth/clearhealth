<?php
/*
 	This is for mirroring one clearhealth user list to another, in a master slave relationship. Cannot think of why someone would want this kind of helper script outside of exactly the situation it was written for, which is to migrate an old system to the new one.

	


*/

$open_db = "open";
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

$userSQL = 'SELECT * FROM `user` WHERE 1 ';

$query = mysql_query($userSQL);

$user_array = array();

while ($result = mysql_fetch_array($query)) {

	$result["provider"]=false;
	$groupSQL = 'SELECT * FROM `users_groups` WHERE user_id = '.$result['user_id'];
	$groupquery = mysql_query($groupSQL);

	while($groupresult = mysql_fetch_array($groupquery)) {
		if($groupresult['group_id'] == 4){// the provider group is number 4
			$result["provider"]=true;		
		}
	} 
 	$user_array[]=$result; 
}


//start writing the file
echo "<?php\n";
echo "\$users =";
echo var_export($user_array);
echo ";\n";
echo "\n?>";


?>
