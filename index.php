<?php
if (file_exists('cellini')) {
	require_once "cellini/bootstrap.php";
}
else {
	require_once "celini/bootstrap.php";
}

if (file_exists(CELLINI_ROOT."/controllers/Dispatcher.class.php")) {
	require_once CELLINI_ROOT."/controllers/Dispatcher.class.php";
}
else {
	require_once CELLINI_ROOT."/controllers/Controller.class.php";
}

if ($config['dir_style_paths']) {

	$uri = '';
	if (isset($_SERVER['PATH_INFO'])) {
		$uri = $_SERVER['PATH_INFO'];
	}
	if (substr($uri,-1) == "/") {
		$uri = substr($uri,0,-1);	
	}
	if (substr($uri,0,1) == "/") {
		$uri = substr($uri,1);	
	}
	$path = explode("/",$uri);

	// check if were in a subdir
	if (isset($config['app_prefix']) && $path[0] == $config['app_prefix']) {
		array_splice($path,0,1);
	}

	// check if were just using index.php/main instead of forcetyping main
	if (isset($path[0]) && $path[0] === "index.php") {
		array_splice($path,0,1);
	}

	// setup args arguments so its like the _GET setup

	// main/print level
	if (isset($path[0]) && !empty($path[0])) {
		if (strtolower($path[0]) == 'util') {
			$path[0] = 'main';
			$GLOBALS['util'] = true;
		}
		$args[$path[0]] = $path[0];
	}
	else {
		$args["main"] = 'main';
	}

	// controller
	if (isset($path[1])) {
		$args[$path[1]] = $path[1];
	}
	else {
		$args['default'] = 'default'; 
	}

	// action
	if (isset($path[2])) {
		$args['action'] = $path[2];
	}
	else {
		$args['action'] = 'default'; 
	}

	if (isset($path[3])) {
		$args[$path[3]] = $path[3];
	}
	if (isset($path[4])) {
		$args[$path[4]] = $path[4];
	}


	foreach($_GET as $key => $val) {
		$args[$key] = $val;
	}

	// map default args onto _GET
	if (isset($path[4])) {
		array_unshift($_GET,$path[4]);
	}
	else if (isset($path[3])) {
		array_unshift($_GET,$path[3]);
	}
}
else {
	$args = $_GET;
}

if (class_exists('Dispatcher')) {
	$d = new Dispatcher();
}
else {
	$d = new Controller();
}
$d->check_input();

$d->trail_build($args);

echo $d->act($args);
?>
