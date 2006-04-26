<?php
// setup clearhealth specific stuff

// configure js libraries in the ajax helper
$ajax =& Celini::ajaxInstance();
$ajax->jsLibraries[] = 'calendar';
$ajax->jsLibraries[] = 'clniUtil';
$ajax->jsLibraries[] = array('suggest');
$ajax->jsLibraries[] = 'chBehaviors';
$ajax->jsLibraries[] = 'zipcode';
// Add application-specific js libraries
$ajaxServer =& Celini::ajaxServerInstance();
$ajaxServer->registerJSLibrary('chBehaviors', 'behavior.js', realpath(dirname(__FILE__) . '/..') . '/js/');
$ajaxServer->registerJSLibrary('zipcode', 'zipcode.js', realpath(dirname(__FILE__) . '/..') . '/js/');

// todo: where should this setting end up, does it really make sense to have it user configurable
$conf =& Celini::configInstance();
$ajaxConf = (array)$conf->get('ajaxConfClasses');
$ajaxConf[] = 'ClearhealthAJAX';
$conf->set('ajaxConfClasses',$ajaxConf);

define('CLEARHEALTH_ROOT',APP_ROOT);
$ajax =& Celini::ajaxServerInstance();
$ajax->registerJSLibrary('suggest', 'suggest.js', CELINI_ROOT . '/js/');
?>
