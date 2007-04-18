<?php
require_once dirname(__FILE__) . '/../bootstrap.php';

//$gacl_options['debug'] = true;
$gacl_api = new gacl_api($gacl_options);


$result = $gacl_api->add_object_section('Resources', 'resources', 10, 0, 'AXO'); 
if ($result === TRUE) {
	echo "AXO Section: resources already exists<br>\n";
} else if ($result !== FALSE) {
	echo "Created AXO Section: resources sucessfully. <br>\n";
} else {
	echo "Error creating AXO Section: resources.<br>\n";
}
unset($result);

$paths = array(
	'celini'=>CELINI_ROOT,
	'app'=>APP_ROOT,
);
$c =& Celini::configInstance();
$paths = array_merge($paths,$c->get('module_paths'));

foreach($paths as $path) {
	if (strstr($path,'celini')) {
		$path = $path . "/controllers/";
	}
	else {
		$path = $path . "/local/controllers/";
	}
	if (file_exists($path)) {
		$d = dir($path);
		while($entry = $d->read())
		{
			createAxo($entry);
		}
	}
}


function createAxo($entry) {
	global $gacl_api;
	if (preg_match('/C_([a-zA-Z0-9_-]+)\.class\.php/',$entry,$match))
	{
		$controller = $match[1];
		$result = $gacl_api->add_object('resources', "Section - $controller", strtolower($controller), 10, 0, 'AXO');
		$result2 = $gacl_api->add_group_object(11,'resources',strtolower($controller),'AXO');
		if ($result === TRUE) {
			echo "AXO: $controller already exists<br>\n";
		} else if ($result !== FALSE) {
			echo "Created AXO: $controller sucessfully. <br>\n";
		} else {
			echo "Error creating AXO: $controller.<br>\n";
		}
		unset($result);
	}
}
?>
