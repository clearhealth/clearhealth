<?php
$conf =& Celini::configInstance();
$ajaxConf = (array)$conf->get('ajaxConfClasses');
$ajaxConf[] = 'CalendarAJAX';
$conf->set('ajaxConfClasses',$ajaxConf);

define('CALENDAR_MODULE_ROOT',realpath(dirname(__FILE__).'/../'));
//define('CALENDAR_ROOT',CALENDAR_MODULE_ROOT.'/local/lib/Calendar/');
$ajax =& Celini::ajaxServerInstance();
$ajax->registerJSLibrary('calendar', 'list.js', CALENDAR_MODULE_ROOT . '/js/');
			
?>
