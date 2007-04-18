<?php

// Handle registering app-specific JS libraries
$ajaxServer =& Celini::ajaxServerInstance();
$ajaxServer->registerJSLibrary('referral', 'referral.js', dirname(__FILE__). '/../js/');

$ajax =& Celini::ajaxInstance();
$ajax->jsLibraries[] = 'referral';
$ajax->jsLibraries[] = 'hover';


$conf =& Celini::configInstance();
$ajaxConf = (array)$conf->get('ajaxConfClasses');
$ajaxConf[] = 'PCCAJAX';
$ajaxConf[] = 'chlReferralAJAX';
$conf->set('ajaxConfClasses',$ajaxConf);





?>
