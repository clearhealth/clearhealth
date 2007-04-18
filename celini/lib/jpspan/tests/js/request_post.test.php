<?php
/**
* @version $Id: request_post.test.php,v 1.4 2004/12/10 23:32:06 harryf Exp $
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
<title>JPSpan_Request_Post</title>
<?php jsunit_drawHeader();
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('request/post.js');
JPSpan_Include_Register('encode/xml.js');
JPSpan_Include_Register('util/mock.js');
JPSpan_Include_Register('util/mockxmlhttp.js');
?>
<script language="JavaScript" type="text/javascript">
<!--

<?php jsunit_drawUtils(); ?>
<?php JPSpan_Includes_Display(); ?>

//-----------------------------------------------------------------------------
function testRequestUrl() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var post = new JPSpan_Request_Post(mEnc);
    post.serverurl = 'foo';
    post.build();
    assertEquals('foo',post.requesturl);
}

//-----------------------------------------------------------------------------
function testBuildBasicBody() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','blah');
    var post = new JPSpan_Request_Post(mEnc);
    post.addArg('foo','bar');
    post.addArg('bar','foo');
    post.build();
    assertEquals('foo=blah&bar=blah',post.body);
}

//-----------------------------------------------------------------------------
function testBuildBodyUrlEncoded() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','x+y');
    var post = new JPSpan_Request_Post(mEnc);
    post.addArg('foo','bar');
    post.build();
    assertEquals('foo=x%2By',post.body);
}
//-----------------------------------------------------------------------------
function testAsyncPrepare() {
    var mHttp = new JPSPan_Util_MockXMLHttpRequest();
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','bar');
    var post = new JPSpan_Request_Post(mEnc);
    post.serverurl = 'http://www.google.com';
    post.type = 'async';
    post.prepare(mHttp);
    var args = mHttp.getLastCallArgs('open');
    assertEquals('POST',args[0]);
    assertEquals('http://www.google.com',args[1]);
    assertTrue(args[2]);
}
//-----------------------------------------------------------------------------
function testSyncPrepare() {
    var mHttp = new JPSPan_Util_MockXMLHttpRequest();
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','bar');
    var post = new JPSpan_Request_Post(mEnc);
    post.serverurl = 'http://www.google.com';
    post.type = 'sync';
    post.prepare(mHttp);
    var args = mHttp.getLastCallArgs('open');
    assertEquals('POST',args[0]);
    assertEquals('http://www.google.com',args[1]);
    assertFalse(args[2]);
}
//-----------------------------------------------------------------------------
function testHeaders() {
    var mHttp = new JPSPan_Util_MockXMLHttpRequest();
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','bar');
    mEnc.contentType = 'text/foo';
    var post = new JPSpan_Request_Post(mEnc);
    post.addArg('foo','bar');
    post.serverurl = 'http://www.google.com';
    post.type = 'sync';
    post.prepare(mHttp);
    var args = mHttp.getCallArgsAt('setRequestHeader',0);
    assertEquals('Content-Length',args[0]);
    assertEquals(7,args[1]);
    var args = mHttp.getCallArgsAt('setRequestHeader',1);
    assertEquals('Content-Type',args[0]);
    assertEquals('application/x-www-form-urlencoded; charset=UTF-8',args[1]);
}
//-----------------------------------------------------------------------------
function testOpen() {
    var mHttp = new JPSPan_Util_MockXMLHttpRequest();
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    mEnc.setReturnValue('encode','bar');
    var post = new JPSpan_Request_Post(mEnc);
    post.addArg('foo','bar');
    post.serverurl = 'http://www.google.com';
    post.type = 'sync';
    post.prepare(mHttp);
    post.send();
    var args = mHttp.getLastCallArgs('send');
    assertEquals('foo=bar',args[0]);
}

//-----------------------------------------------------------------------------
-->
</script>
</head>
<body>
<h2>JPSpan_Request_Post</h2>

<p><a href="<?php echo JSUNIT; ?>/testRunner.html?testpage=<?php echo $_SERVER['PHP_SELF']; ?>">Run Tests</a></p>
</body>
</html>
