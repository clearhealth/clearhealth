<?php

/*

	Given the output of --compact that is available in Mysql 4.1 it is possible to do file based comparisons between sql files. This reads a database table list and then compares files for each table based on file prefixes.  So if you set one prefix to clearhealth_ (probably the old db name) and then the other to proposed_ this will do a diff command on clearhealth_address vs proposed_address and so on...



*/


	$user='root';
	$password='password';
	$host='localhost';
	$dbname = 'clearhealth';

	$file1_prefix='clearhealth_';
	$file2_prefix='prop_';

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

		echo "--------comparing $file1_prefix$corename.sql to  $file2_prefix$corename.sql  ----\n\n";	
		system("diff $file1_prefix$corename.sql $file2_prefix$corename.sql");
		
	}


	mysql_close();


?>
