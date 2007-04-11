<?php

// configure js libraries in the ajax helper
$ajax =& Celini::ajaxInstance();
$ajax->jsLibraries[] = 'yui_treeview';
$ajax->jsLibraries[] = 'docsmart';
$ajax->jsLibraries[] = 'scriptaculous';

// Add application-specific js libraries
$ajaxServer =& Celini::ajaxServerInstance();
$ajaxServer->registerJSLibrary('yui_treeview', array('YAHOO.js', 'treeview.js'), realpath(dirname(__FILE__) . '/..') . '/js/yahoo/');
$ajaxServer->registerJSLibrary('docsmart', 'main.js', realpath(dirname(__FILE__) . '/..') . '/js/');

