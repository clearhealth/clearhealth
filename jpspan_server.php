<?php
require_once "cellini/bootstrap.php";
// Including this sets up the JPSPAN constant
require_once CELLINI_ROOT . '/lib/jpspan/JPSpan.php';

// Load the PostOffice server
require_once JPSPAN . 'Server/PostOffice.php';

// Some class you've written...
require_once APP_ROOT. '/local/controllers/C_PatientFinder.class.php';

// Create the PostOffice server
$S = & new JPSpan_Server_PostOffice();

// Register your class with it...
$handle_desc = new JPSpan_HandleDescription();
$handle_desc->Class = 'C_PatientFinder';
$handle_desc->methods = array('find_remoting');
$S->addHandler(new C_PatientFinder(), $handle_desc);

// This allows the JavaScript to be seen by
// just adding ?client to the end of the
// server's URL

if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {

		// Compress the output Javascript (e.g. strip whitespace)
		define('JPSPAN_INCLUDE_COMPRESS',TRUE);

		// Display the Javascript client
		$S->displayClient();

} else {

		// This is where the real serving happens...
		// Include error handler
		// PHP errors, warnings and notices serialized to JS
		require_once JPSPAN . 'ErrorHandler.php';

		// Start serving requests...
		$S->serve();

}
?>
