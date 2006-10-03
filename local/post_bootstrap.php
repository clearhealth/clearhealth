<?php
// setup clearhealth specific stuff

// configure js libraries in the ajax helper
$ajax =& Celini::ajaxInstance();
$ajax->jsLibraries[] = 'calendar';
$ajax->jsLibraries[] = 'clniUtil';
$ajax->jsLibraries[] = array('suggest','chsuggest');
$ajax->jsLibraries[] = 'chBehaviors';
$ajax->jsLibraries[] = 'zipcode';
// Add application-specific js libraries
$ajaxServer =& Celini::ajaxServerInstance();
$ajaxServer->registerJSLibrary('chBehaviors', 'behavior.js', realpath(dirname(__FILE__) . '/..') . '/js/');
$ajaxServer->registerJSLibrary('zipcode', 'zipcode.js', realpath(dirname(__FILE__) . '/..') . '/js/');
$ajaxServer->registerJSLibrary('quicksave', 'quicksave.js', realpath(dirname(__FILE__) . '/..') . '/js/');
$ajaxServer->registerJSLibrary('conflicts', 'conflicts.js', realpath(dirname(__FILE__) . '/..') . '/js/');

// todo: where should this setting end up, does it really make sense to have it user configurable
$conf =& Celini::configInstance();
$ajaxConf = (array)$conf->get('ajaxConfClasses');
$ajaxConf[] = 'ClearhealthAJAX';
$conf->set('ajaxConfClasses',$ajaxConf);

define('CLEARHEALTH_ROOT',APP_ROOT);
$ajaxServer->registerJSLibrary('suggest', 'suggest.js', CELINI_ROOT . '/js/');
$ajaxServer->registerJSLibrary('chsuggest', 'chsuggest.js', APP_ROOT . '/js/');

if(isset($_GET['changepractice']) && isset($_SESSION['defaultpractice']) && $_GET['changepractice'] != $_SESSION['defaultpractice']) {
	if(isset($_GET['Filter'])) {
		unset($_GET['Filter']);
	}
	$_SESSION['calendar']['filter_settings'] = array();
}
if(isset($_GET['changepractice'])){
	$_SESSION['defaultpractice']=$_GET['changepractice'];
}

?>