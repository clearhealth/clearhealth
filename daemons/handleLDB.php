<?php
/***********************************************************
Lab Demographics Bridge daemon

xinetd file ldb:

service ldb
{
	type                = UNLISTED
	socket_type         = stream
	protocol            = tcp
	wait                = no
	server              = /usr/bin/php
	server_args         = handleLDB.php
	port                = 9006
	user                = root
}

***********************************************************/
set_time_limit(90);

$appFile = realpath(dirname(__FILE__) . '/../application/models/HandleLDB.php');
require_once $appFile;

function handleLDBLog($data) {
	file_put_contents('/tmp/handle_ldb.log',"\n$data",FILE_APPEND);
}

file_put_contents('/tmp/handle_ldb.log',"START: ".date('c'));

$request = '';
while ($data = fgets(STDIN)) {
	$request .= $data;
}

if ($request != '') {
	handleLDBLog("REQUEST: $request");
	$ldb = HandleLDB::getInstance();
	$message = $ldb->process($request);
	handleLDBLog("RESPONSE: $message");
	fwrite(STDOUT,$message."\n");
}
