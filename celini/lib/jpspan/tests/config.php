<?php
/**
* @package JPSpan
* @subpackage Tests
* @version $Id: config.php,v 1.3 2004/11/22 10:59:24 harryf Exp $
*/
//----------------------------------------------------------------------------------------
// Configuration
//----------------------------------------------------------------------------------------
if (!defined('JSUNIT')) {
     // HTTP Path to jsunit (no trailing slash)
    define('JSUNIT', 'http://localhost/jsunit');
}

if (!defined('SIMPLE_TEST')) {
    // Should point at SimpleTest (absolute path required with trailing slash)
    define('SIMPLE_TEST', 'simpletest/'); // Use your include path
}

if (!defined('JPSPAN_PATH') ) {
    // You can probably leave this untouched
    // Should point at JPSpan (absolute path required with trailing slash)
    define('JPSPAN_PATH', dirname(__FILE__).'/../'); // Use your include path
}

if (!defined('JPSPAN_JSLIB')) {
     // HTTP Path to location of JPSpan JS libraries (no trailing slash)
    define('JPSPAN_JSLIB', 'http://localhost/jpspan/JPSpan/js');
}

if (!defined('JPSPAN_TESTS')) {
     // HTTP Path to location of JPSpan test pages
     // for XMLHttpRequest tests (no trailing slash)
    define('JPSPAN_TESTS', 'http://localhost/jpspan/tests');
}

//----------------------------------------------------------------------------------------

// Load SimpleTest and main JPSpan
if ( @include_once SIMPLE_TEST . 'unit_tester.php' ) {
    require_once SIMPLE_TEST . 'mock_objects.php';
    require_once SIMPLE_TEST . 'reporter.php';
} else {
    trigger_error('Unable to load SimpleTest: configure SIMPLE_TEST in config.php');
}

// Load SimpleTest and main JPSpan
if ( !@include_once JPSPAN_PATH . 'JPSpan.php' ) {
    trigger_error('Unable to load JPSpan: configure JPSPAN_PATH in config.php');
}

// Utility functions
function jsunit_drawHeader() {
?>
<link rel="stylesheet" type="text/css" href="<?php echo JSUNIT . '/css/jsUnitStyle.css';?>">
<script language="JavaScript" type="text/javascript" src="<?php echo JSUNIT . '/app/jsUnitCore.js';?>">
</script>
<?php
}

function jsunit_drawUtils() {
?>
//-----------------------------------------------------------------------------

function echo(string) {
    document.getElementById("results").innerHTML += string;
}

function clear() {
    document.getElementById("results").innerHTML = "";
}

function result() {
    return document.getElementById("results").innerHTML;
}
<?php
}

function jsunit_drawResults() {
?>
<h3>Results</h3>
<div id="results">
</div>
<?php
}
?>
