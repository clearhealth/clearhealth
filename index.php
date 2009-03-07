<?php
/*$session_save_path = "tcp://localhost:11211?persistent=1&weight=2&timeout=2&retry_interval=10,  ,tcp://localhost:11211  ";
ini_set('session.save_handler', 'memcache');
ini_set('session.save_path', $session_save_path);
*/

/*function calcTS() {
        list($usec, $sec) = explode(" ", microtime());
        $ts = ((float)$usec + (float)$sec);
        if (!isset($GLOBALS['gts'])) $GLOBALS['gts'] = $ts;
        return $ts-$GLOBALS['gts'];
}
calcTS();*/

if (file_exists('cellini')) {
	require_once "cellini/bootstrap.php";
}
else {
	require_once "celini/bootstrap.php";
}
/*if (isset($_GET['tester'])) {
	$_SESSION['tester'] = true;
}
if (!isset($_SESSION['tester'])) {
?>
<p>
Regular Internal & External ClearHealth Users Please Select This Link to Access the System:
<a href="https://clearhealth.ccihsv.com?tester">https://clearhealth.ccihsv.com?tester</a>
</p>
<p>
If you are experiencing a problem with the link above and belong to a scheduling department please access ClearHealth using this link:
<a href="https://192.168.11.51?tester">http://192.168.11.51?tester</a>
</p>
<p>
If you are experiencing a problem with the link above and belong to any other department please use this link:
<a href="http://192.168.11.52?tester">http://192.168.11.52?tester</a>
</p>
<?php
exit;
}
*/
if (file_exists(CELINI_ROOT."/controllers/Dispatcher.class.php")) {
	$loader->requireOnce('controllers/Dispatcher.class.php');
}
else {
	$loader->requireOnce('controllers/Controller.class.php');
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
//echo strlen($d->act($args));
echo $d->act($args);
<<<<<<< .mine
//echo calcTS();
=======
//echo "<!--ts: " .calcTS() . "-->";
>>>>>>> .r5300
?>
