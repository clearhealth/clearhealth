<?php
if(isset($_GET['session_id'])){
	session_id($_GET['session_id']);
}
session_start();
require_once("includes/config.php");
require_once("includes/Controller.class.php");

$c = new Controller();
$c->trail_build($_GET);
echo $c->act($_GET);
?>
