<?php

$conn = mysql_connect('localhost','root','');
mysql_select_db("clearhealth");
$sql = "show tables from clearhealth";
$res = mysql_query($sql);
echo mysql_error();
while ($row = mysql_fetch_array($res))  {
	echo("ALTER TABLE `".$row[0]."` ENGINE=INNODB;\n");
}



?>
