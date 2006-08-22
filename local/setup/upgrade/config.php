<?php
/**
 * Configuration variables
 */

$GLOBALS['oldCHDB'] = 'clearhealth';
$GLOBALS['newCHDB'] = 'clearhealth';
$GLOBALS['debug'] = true;
$GLOBALS['eol-style'] = "\n"; // change to <br /> for HTML output

if($GLOBALS['oldCHDB'] == 'default') {
	echo 'Please edit config.php before running upgrade scripts.\n';
	exit;
}

?>
