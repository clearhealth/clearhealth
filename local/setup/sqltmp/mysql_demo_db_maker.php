<?php

/*
To use this demo db maker. Simply install a clean clearhealth db. Then create the demo. Then use mysql_split.php to make a set of files that begin with demo_. Then run this program.

This program will compare the clearhealth_ and the demo_ files to see if they are identical. When they are not identical then the demo creation added data to that table. 

This program erases the demo_ files that are identical to the clearhealth_ files and then replaces the demo_ files that are not identical with data-only files. So that they can be imported to the a clean installation.


*/


	$user='root';
	$password='password';
	$host='localhost';
	$dbname = 'clearhealth';

	$file1_prefix='clearhealth_';
	$file2_prefix='demo_';

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
	if(!ereg('^fb',$row[0])&&!ereg('^codes',$row[0])){//this filters out all of the freeb sql files and the codes

  		$split_name_array = split("_",$row[0]);
		$tablename = $row[0];
		$corename = $split_name_array[0];

		echo "---comparing $file1_prefix$corename.sql to  $file2_prefix$corename.sql  ----\n\n";	
		if(filesize("$file1_prefix$corename.sql")!=filesize("$file2_prefix$corename.sql")){
			echo "file is different removing demo file and creating a data only version \n";
			system("rm -rf $file2_prefix$corename.sql");
			system("mysqldump  --add-drop-table  --allow-keywords --skip-comments --quote-names  --compatible=mysql323 -p$password -u$user $dbname $tablename >> $file2_prefix$corename.sql");	
		}else{
			echo "identical\n";
			system("rm -rf $file2_prefix$corename.sql");
		}

	}	
	}


	mysql_close();


?>
