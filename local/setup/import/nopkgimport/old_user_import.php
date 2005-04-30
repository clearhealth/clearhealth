<?php
require_once("config.php");
$db = $GLOBALS['frame']['adodb']['db'];
$sql = "SELECT * from ".$GLOBALS['frame']['config']['openemr'].".users where username != 'admin'";

$result = $db->Execute($sql);

while ($result && !$result->EOF) {

	$u = new User(null,null);
	$u->set_username($result->fields['username']);
	$u->set_password($result->fields['password']);

	//print_r($u);
	$u->persist();
	$result->moveNext();

}


?>
