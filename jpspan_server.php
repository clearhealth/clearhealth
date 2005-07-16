<?php
// todo: reduce the number of classes were handling to decrease the size of the javascript were generating (doesn't matter all that much in the local server case)
require_once "cellini/bootstrap.php";

// verify that were logged in
$me =& Me::getInstance();
if ($me->get_id() == 0) {
	die('Access Denied');
}
// Including this sets up the JPSPAN constant
require_once CELLINI_ROOT . '/lib/jpspan/JPSpan.php';

// Load the PostOffice server
require_once JPSPAN . 'Server/PostOffice.php';

// Some class you've written...
require_once APP_ROOT. '/local/controllers/C_PatientFinder.class.php';
require_once APP_ROOT. '/local/controllers/C_Coding.class.php';
require_once APP_ROOT. '/local/includes/FeeScheduleDatasource.class.php';
require_once APP_ROOT. '/local/includes/SuperbillDatasource.class.php';
require_once APP_ROOT. '/local/includes/CodingDatasource.class.php';
require_once APP_ROOT. '/local/ordo/Report.class.php';
require_once APP_ROOT. '/local/ordo/MenuReport.class.php';
require_once APP_ROOT. '/local/ordo/Form.class.php';
require_once APP_ROOT. '/local/ordo/MenuForm.class.php';
require_once APP_ROOT. '/local/ordo/Encounter.class.php';

// Create the PostOffice server
$S = & new JPSpan_Server_PostOffice();

// Register your class with it...
$handle_desc = new JPSpan_HandleDescription();
$handle_desc->Class = 'C_PatientFinder';
$handle_desc->methods = array('find_remoting');
$S->addHandler(new C_PatientFinder(), $handle_desc);

$handle_desc2 = new JPSpan_HandleDescription();
$handle_desc2->Class = 'C_Coding';
$handle_desc2->methods = array('icd_search', 'cpt_search');
$S->addHandler(new C_Coding(), $handle_desc2);

$S->addHandler(new FeeScheduleDatasource());
$S->addHandler(new SuperbillDatasource());
$S->addHandler(new IcdCodingDatasource());
$S->addHandler(new CptCodingDatasource());
$encounterHandler = new JPSpan_HandleDescription();
$encounterHandler->Class = 'Encounter';
$encounterHandler->methods = array('appointmentlist_remoting');
$S->addHandler(new Encounter(), $encounterHandler);

// used by C_Report connect action
$S->addHandler(new Report());
$S->addHandler(new MenuReport());

// used by C_Form connect action
$S->addHandler(new MenuForm());



// This allows the JavaScript to be seen by
// just adding ?client to the end of the
// server's URL

if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {
	// cache dir
	$cacheDir = APP_ROOT."/tmp/";

	// create a hash from the api of the handlers
	// turn the descriptions into a string
	$api = "";
	foreach($S->descriptions as $key => $val) {
		$api .= $key;
		foreach($val->methods as $method) {
			$api .= $method;
		}
	}
	$apihash = md5($api);

	// get the host the request is being made with since it gets embedded in the client file
	$server = preg_replace('/[^a-zA-Z0-9\._]/','',$_SERVER['HTTP_HOST']);

	// create the filename
	$cacheFile = "client-$apihash-$server.js";

	// create the etag
	$etag = md5($cacheFile);

	// setup HTTP_Cache give it our custom etag and see if we need to generate the client
	require_once 'HTTP/Cache.php';

	$cache = &new HTTP_Cache();
	$cache->setEtag($etag);

	if (!$cache->isValid()) {

		if (!file_exists($cacheDir.$cacheFile)) {
			// Compress the output Javascript (e.g. strip whitespace)
			define('JPSPAN_INCLUDE_COMPRESS',TRUE);

			// Display the Javascript client
			$G = & $S->getGenerator();
			require_once JPSPAN . 'Include.php';
			$I = & JPSpan_Include::instance();
			
			// HACK - this needs to change
			$I->loadString(__FILE__,$G->getClient());
			$client = $I->getCode();

			file_put_contents($cacheDir.$cacheFile,$client);
		}
		else {
			$client = file_get_contents($cacheDir.$cacheFile);
		}


		header('Content-Type: application/x-javascript');

		$cache->setBody($client);
	}
	else {
		// something is setting Cache-Control 
		header('Cache-Control: must-revalidate');
	}

	$cache->send();

} else {

		// This is where the real serving happens...
		// Include error handler
		// PHP errors, warnings and notices serialized to JS
		require_once JPSPAN . 'ErrorHandler.php';

		// Start serving requests...
		$S->serve();

}
?>
