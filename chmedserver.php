<?php
/*****************************************************************************
*       chmedserver.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/
<?
/*
*	This is a proxy file which allows an application to interact with the HealthCloud API's AJAX Server. 
*	This proxying is necessary because by default a browser cannot access a web based page on one domain and
*	access an ajax server on another. This is considered a cross site scripting (XSS) vulnerability.
*
*/	

//Your HealthCloud API Key, this must be kept secret.

//The standard URL for the HealthCloud API AJAX Server
$url =  'https://127.0.0.1/hcapi/server.php?';


//this code carries any GET arguments along so they are included in the request to the AJAX server
foreach($_GET as $var => $val)  {
	$url .= "&$var=$val";
}

//initiate a curl request to the HealthCloud AJAX server.
$session = curl_init($url);

//collect all the information from the body of the request and forward that to the HealthCloud AJAX server.
$post = file_get_contents("php://input");
$post = "=lipitor";
curl_setopt ($session, CURLOPT_POSTFIELDS, $post);
//trigger_error($post, E_USER_NOTICE);
//use unsafe certificate, for development purposes only
curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($session, CURLOPT_SSL_VERIFYHOST, false);


//set some required headers 
curl_setopt($session, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8;","X-Requested-With: XMLHttpRequest","X-Ajax-Engine: HTML_AJAX/0.5.5"));

//return the response as a return value rather than just outputting
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

//execute the request to the AJAX server
$xml = curl_exec($session);

echo "test -- " . $xml;exit;
//trigger_error($xml,E_USER_NOTICE);
//get the headers from the HealthCloud AJAX server response.
$headers = curl_getinfo($session);

//if an error occured a special header is sent so the client web page can trigger a javascript alert, otherwise a regular JSON encoding header is used.
if (isset($headers['content_type'])) {
	header("Content-Type: " . $headers['content_type']);
}
else {
	header("Content-Type: application/json; charset=utf-8");

}

//output the response from the backend
echo $xml;
//echo curl_error($session);
//close the curl session
curl_close($session);
?>
