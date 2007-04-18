<?php
/**
* @version $Id: request_get.test.php,v 1.7 2004/12/10 23:32:06 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/
/**
* Prevent caching
*/
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header( "Cache-Control: no-cache, must-revalidate" ); 
header( "Pragma: no-cache" );
/**
* Include
*/
require_once '../config.php';
?>
<html>
<head>
<title>JPSpan_Request_Get</title>
<?php jsunit_drawHeader();
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('request/get.js');
JPSpan_Include_Register('encode/xml.js');
JPSpan_Include_Register('util/mock.js');
JPSpan_Include_Register('util/mockxmlhttp.js');
?>
<script language="JavaScript" type="text/javascript">
<!--

<?php jsunit_drawUtils(); ?>
<?php JPSpan_Includes_Display(); ?>

//-----------------------------------------------------------------------------
function testBuildBasicUrl() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','blah');
    var get = new JPSpan_Request_Get(mEnc);
    get.addArg('foo','bar');
    get.addArg('bar','foo');
    get.serverurl = 'http://www.google.com';
    get.build();
    assertEquals('http://www.google.com?foo=blah&bar=blah',get.requesturl);
}
//-----------------------------------------------------------------------------
function testBuildUrlEncoded() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','x+y');
    var get = new JPSpan_Request_Get(mEnc);
    get.addArg('foo','bar');
    get.serverurl = 'http://www.google.com';
    get.build();
    assertEquals('http://www.google.com?foo=x%2By',get.requesturl);
}
//-----------------------------------------------------------------------------
function testBuildExtended() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','bar');
    var get = new JPSpan_Request_Get(mEnc);
    get.addArg('foo','bar');
    get.serverurl = 'http://www.google.com?x=y';
    get.build();
    assertEquals('http://www.google.com?x=y&foo=bar',get.requesturl);
}

//-----------------------------------------------------------------------------
function testAsyncPrepare() {
    var mHttp = new JPSPan_Util_MockXMLHttpRequest();
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','bar');
    var get = new JPSpan_Request_Get(mEnc);
    get.addArg('foo','bar');
    get.serverurl = 'http://www.google.com';
    get.type = 'async';
    get.prepare(mHttp);
    var args = mHttp.getLastCallArgs('open');
    assertEquals('GET',args[0]);
    assertEquals('http://www.google.com?foo=bar',args[1]);
    assertTrue(args[2]);
}
//-----------------------------------------------------------------------------
function testSyncPrepare() {
    var mHttp = new JPSPan_Util_MockXMLHttpRequest();
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','bar');
    var get = new JPSpan_Request_Get(mEnc);
    get.addArg('foo','bar');
    get.serverurl = 'http://www.google.com';
    get.type = 'sync';
    get.prepare(mHttp);
    var args = mHttp.getLastCallArgs('open');
    assertEquals('GET',args[0]);
    assertEquals('http://www.google.com?foo=bar',args[1]);
    assertFalse(args[2]);
}
//-----------------------------------------------------------------------------
function testOpen() {
    var mHttp = new JPSPan_Util_MockXMLHttpRequest();
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','bar');
    var get = new JPSpan_Request_Get(mEnc);
    get.addArg('foo','bar');
    get.serverurl = 'http://www.google.com';
    get.type = 'sync';
    get.prepare(mHttp);
    get.send();
    var args = mHttp.getLastCallArgs('send');
    assertEquals(null,args[0]);
}
//-----------------------------------------------------------------------------
-->
</script>
</head>
<body>
<h2>JPSpan_Request_Get</h2>

<p><a href="<?php echo JSUNIT; ?>/testRunner.html?testpage=<?php echo $_SERVER['PHP_SELF']; ?>">Run Tests</a></p>
</body>
</html>
