<?php
/**
* @version $Id: httpclient.test.php,v 1.3 2004/12/10 23:32:06 harryf Exp $
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
<title>JPSpan_HttpClient</title>
<?php jsunit_drawHeader();
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('httpclient.js');
JPSpan_Include_Register('request.js');
JPSpan_Include_Register('util/mock.js');
JPSpan_Include_Register('util/mockxmlhttp.js');
?>
<script language="JavaScript" type="text/javascript">
<!--

<?php jsunit_drawUtils(); ?>
<?php JPSpan_Includes_Display(); ?>

function createHttpClientStub(mockxmlhttp) {
    var httpclient = new JPSpan_HttpClient();
    httpclient.init = function() {
        this.xmlhttp = mockxmlhttp;
    }
    return httpclient;
}
//-----------------------------------------------------------------------------
function testCall() {
    var mXmlHttp = new JPSPan_Util_MockXMLHttpRequest();
    mXmlHttp.readyState = 0;
    mXmlHttp.status = 200;
    mXmlHttp.responseText = 'Test response';
    var mReq = new JPSpan_Util_MockCreate(JPSpan_Request);
    var httpClient = createHttpClientStub(mXmlHttp);
    assertEquals('Test response',httpClient.call(mReq));
    assertEquals(1,mReq.getCallCount('prepare'));
    assertEquals(1,mXmlHttp.getCallCount('setRequestHeader'));
    var args = mXmlHttp.getLastCallArgs('setRequestHeader');
    assertEquals('Accept-Charset',args[0]);
    assertEquals('UTF-8',args[1]);
    assertEquals(1,mReq.getCallCount('send'));
}
//-----------------------------------------------------------------------------
function testCallBusy() {
    var mXmlHttp = new JPSPan_Util_MockXMLHttpRequest();
    mXmlHttp.readyState = 2;
    var mReq = new JPSpan_Util_MockCreate(JPSpan_Request);
    var httpClient = createHttpClientStub(mXmlHttp);
    try {
        httpClient.call(mReq);
        fail();
    } catch(e) {
        assertEquals(1001,e.code);
    }
}
//-----------------------------------------------------------------------------
function testInvalidStatus() {
    var mXmlHttp = new JPSPan_Util_MockXMLHttpRequest();
    mXmlHttp.readyState = 0;
    mXmlHttp.status = 404;
    mXmlHttp.statusText = 'Not Found';
    var mReq = new JPSpan_Util_MockCreate(JPSpan_Request);
    var httpClient = createHttpClientStub(mXmlHttp);
    try {
        httpClient.call(mReq);
        fail();
    } catch(e) {
        assertEquals(1002,e.code);
    }
}
//-----------------------------------------------------------------------------
function testAsyncCall() {
    var mXmlHttp = new JPSPan_Util_MockXMLHttpRequest();
    mXmlHttp.readyState = 0;
    mXmlHttp.status = 200;
    mXmlHttp.responseText = 'Test response';
    var mReq = new JPSpan_Util_MockCreate(JPSpan_Request);
    var httpClient = createHttpClientStub(mXmlHttp);
    httpClient.call(mReq);
    assertEquals(1,mReq.getCallCount('prepare'));
    assertEquals(1,mXmlHttp.getCallCount('setRequestHeader'));
    var args = mXmlHttp.getLastCallArgs('setRequestHeader');
    assertEquals('Accept-Charset',args[0]);
    assertEquals('UTF-8',args[1]);
    assertEquals(1,mReq.getCallCount('send'));
}
//-----------------------------------------------------------------------------
function testCallInProgress() {
    var mXmlHttp = new JPSPan_Util_MockXMLHttpRequest();
    var httpClient = createHttpClientStub(mXmlHttp);
    httpClient.init();
    mXmlHttp.readyState = 2;
    assertTrue(httpClient.callInProgress());
}
//-----------------------------------------------------------------------------
function UserHandler(){}
UserHandler.prototype = {
    onInit: function(){},
    onLoad: function(){},
    onError: function(){}
}
//-----------------------------------------------------------------------------
function testAbort() {
    var mXmlHttp = new JPSPan_Util_MockXMLHttpRequest();
    var mUserHandler = new JPSpan_Util_MockCreate(UserHandler);
    var httpClient = createHttpClientStub(mXmlHttp);
    httpClient.userhandler = mUserHandler;
    httpClient.init();
    mXmlHttp.readyState = 2;
    httpClient.abort(httpClient,'testCall');
    assertEquals(1,mXmlHttp.getCallCount('abort'));
    assertEquals(1,mUserHandler.getCallCount('onError'));
    var args = mUserHandler.getLastCallArgs('onError');
    assertEquals(1003,args[0].code);
    assertEquals('testCall',args[1]);
}
//-----------------------------------------------------------------------------
-->
</script>
</head>
<body>
<h2>JPSpan_HttpClient</h2>

<p><a href="<?php echo JSUNIT; ?>/testRunner.html?testpage=<?php echo $_SERVER['PHP_SELF']; ?>">Run Tests</a></p>
</body>
</html>
