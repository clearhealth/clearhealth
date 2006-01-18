<?php
// setup clearhealth specific stuff

// configure js libraries in the ajax helper
$ajax =& Celini::ajaxInstance();
$ajax->jsLibraries[] = 'calendar';
$ajax->jsLibraries[] = 'clniUtil';

// todo: where should this setting end up, does it really make sense to have it user configurable
$conf =& Celini::configInstance();
$ajaxConf = (array)$conf->get('ajaxConfClasses');
$ajaxConf[] = 'ClearhealthAJAX';
$conf->set('ajaxConfClasses',$ajaxConf);

define('CLEARHEALTH_ROOT',APP_ROOT);
?>
