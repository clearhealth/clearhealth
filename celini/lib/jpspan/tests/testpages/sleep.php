<?php
if ( !isset($_GET['sleep']) ) {
    $_GET['sleep'] = 5;
}
sleep($_GET['sleep']);
$out = 'Hello World';
header('Content-Length: '.strlen($out));
header('Content-Type: text/plain');
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header( "Cache-Control: no-cache, must-revalidate" ); 
header( "Pragma: no-cache" );
echo ( $out );
?>
