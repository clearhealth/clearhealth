<?php

/*
	this is a utility script to help with the packaging of clearhealth. It takes a mysql database
	and splits it into many sql files. It uses MYSQL323 compatability mode, to make import into 
	older databases possible.

	It splits the databases so that only main tables get there own files. sub tables are added on 
	the files for main tables, so, for instance, all of the gacl tables end up in the clearhealth_gacl.sql file


*/

	//rackspace =  3.23.58

	$user='root';
	$password='password';
	$host='localhost';
	$dbname = 'clearhealth';

	if (!mysql_connect($host, $user, $password)) {
	    echo 'Could not connect to mysql';
	    exit;
	}



	$sql = "SHOW TABLES FROM $dbname";
	$result = mysql_query($sql);

	if (!$result) {
 	   echo "DB Error, could not list tables\n";
	   echo 'MySQL Error: ' . mysql_error();
	   exit;
	}
	while ($row = mysql_fetch_row($result)) {
    		$split_name_array = split("_",$row[0]);
		$tablename = $row[0];
		$corename = $split_name_array[0];
		$sqlfile = "clearhealth_$corename.sql";
		echo "erasing $sqlfile\n";	
		system("rm -rf $sqlfile");		
	}
	//run the results again
	$result = mysql_query($sql);

	if (!$result) {
 	   echo "DB Error, could not list tables\n";
	   echo 'MySQL Error: ' . mysql_error();
	   exit;
	}
	while ($row = mysql_fetch_row($result)) {
    		$split_name_array = split("_",$row[0]);
		$tablename = $row[0];
		$corename = $split_name_array[0];
		$sqlfile = "clearhealth_$corename.sql";
		echo "dumping $tablename to \n\t\t\t\t $sqlfile\n";	
		system("mysqldump  --create-options --compatible=mysql323 -p$password -u$user $dbname $tablename >> $sqlfile");
		
	}


	mysql_close();


?>
