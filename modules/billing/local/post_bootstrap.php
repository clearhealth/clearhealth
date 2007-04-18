<?php
$conf =& Celini::configInstance();
$ajaxConf = (array)$conf->get('ajaxConfClasses');
$ajaxConf[] = 'BillingAJAX';
$conf->set('ajaxConfClasses',$ajaxConf);

define('BILLING_ROOT',realpath(dirname(__FILE__).'/../'));
$ajax =& Celini::ajaxServerInstance();
$ajax->registerJSLibrary('billingList', 'list.js', BILLING_ROOT . '/js/');
			
?>
