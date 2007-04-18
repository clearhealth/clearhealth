<?php
/**
* @version $Id: remoteobject.test.php,v 1.3 2004/12/10 23:32:06 harryf Exp $
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
<title>JPSpan_RemoteObject</title>
<?php jsunit_drawHeader();
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('httpclient.js');
JPSpan_Include_Register('remoteobject.js');
JPSpan_Include_Register('request.js');
JPSpan_Include_Register('util/mock.js');
?>
<script language="JavaScript" type="text/javascript">
<!--

<?php jsunit_drawUtils(); ?>
<?php JPSpan_Includes_Display(); ?>

function createRemoteObjectStub(mhttp) {
    var robj = new JPSpan_RemoteObject();
    robj.__initClient = function() {
        this.__client = mhttp;
    }
    return robj;
}

function TestUserHandler() {}
TestUserHandler.prototype = {
    testmethod: function(){},
    testmethodError: function(){}
}

//-----------------------------------------------------------------------------
function testSync() {
    var mhttp = JPSpan_Util_MockCreate(JPSpan_HttpClient);
    var robj = createRemoteObjectStub(mhttp);
    robj.Async(TestUserHandler);
    robj.Sync();
    assertEquals('sync',robj.__callState);
    assertEquals(null,robj.__responseHandler);
}
//-----------------------------------------------------------------------------
function testAsync() {
    var mhttp = JPSpan_Util_MockCreate(JPSpan_HttpClient);
    var robj = createRemoteObjectStub(mhttp);
    robj.Sync();
    robj.Async(TestUserHandler);
    assertEquals('async',robj.__callState);
    assertEquals(TestUserHandler,robj.__responseHandler.userHandler);
}
//-----------------------------------------------------------------------------
function testSyncCallClientError() {
    var mhttp = JPSpan_Util_MockCreate(JPSpan_HttpClient);
    mhttp.setReturnException('call',"Test fail");
    var mreq = JPSpan_Util_MockCreate(JPSpan_Request);
    var robj = createRemoteObjectStub(mhttp);
    robj.clientErrorFunc = function(e) {
        throw (e);
    }
    robj.__initClient();
    try {
        robj.__syncCall(mreq);
        fail("Client_Error expected");
    } catch(e) {
        assert('Passed',true);
    }
}
//-----------------------------------------------------------------------------
function testSyncCallServerError() {
    var mhttp = JPSpan_Util_MockCreate(JPSpan_HttpClient);
    mhttp.setReturnValue('call',"Not valid javascript");
    var mreq = JPSpan_Util_MockCreate(JPSpan_Request);
    var robj = createRemoteObjectStub(mhttp);
    robj.serverErrorFunc = function(e) {
        throw (e);
    }
    // Nesting not so good....
    robj.clientErrorFunc = function(e) {
        throw(e);
    }
    robj.__initClient();
    try {
        robj.__syncCall(mreq);
        fail("Server_Error expected");
    } catch(e) {
        assert('Passed',true);
    }
}
//-----------------------------------------------------------------------------
function testSyncCallAppError() {
    var mhttp = JPSpan_Util_MockCreate(JPSpan_HttpClient);
    mhttp.setReturnValue('call',"new Function(\"throw 'Test app error';\");");
    var mreq = JPSpan_Util_MockCreate(JPSpan_Request);
    var robj = createRemoteObjectStub(mhttp);
    robj.applicationErrorFunc = function(e) {
        throw (e);
    }
    robj.serverErrorFunc = function(e) {
        throw (e);
    }
    // Nesting not so good....
    robj.clientErrorFunc = function(e) {
        throw(e);
    }
    robj.__initClient();
    try {
        robj.__syncCall(mreq);
        fail("Application_Error expected");
    } catch(e) {
        assert('Passed',true);
    }
}
//-----------------------------------------------------------------------------
function testSyncCallValid() {
    var mhttp = JPSpan_Util_MockCreate(JPSpan_HttpClient);
    mhttp.setReturnValue('call',"new Function(\"return 'Valid response';\");");
    var mreq = JPSpan_Util_MockCreate(JPSpan_Request);
    var robj = createRemoteObjectStub(mhttp);
    robj.__initClient();
    assertEquals('Valid response',robj.__syncCall(mreq));
}
//-----------------------------------------------------------------------------
function testAsyncCallClientError() {
    var mhttp = JPSpan_Util_MockCreate(JPSpan_HttpClient);
    mhttp.setReturnException('asyncCall',"Test fail");
    var mreq = JPSpan_Util_MockCreate(JPSpan_Request);
    var robj = createRemoteObjectStub(mhttp);
    robj.clientErrorFunc = function(e) {
        throw (e);
    }
    robj.__initClient();
    try {
        robj.__asyncCall(mreq,'Test');
        fail("Client_Error expected");
    } catch(e) {
        assert('Passed',true);
    }
}
//-----------------------------------------------------------------------------
function testAsyncCallServerError() {
    var mhttp = JPSpan_Util_MockCreate(JPSpan_HttpClient);
    var mreq = JPSpan_Util_MockCreate(JPSpan_Request);
    var muh = JPSpan_Util_MockCreate(TestUserHandler);
    var robj = createRemoteObjectStub(mhttp);
    robj.__initResponseHandler(robj,muh);
    robj.serverErrorFunc = function(e) {
        throw (e);
    }
    robj.clientErrorFunc = function(e) {
        throw(e);
    }
    robj.__initClient();
    try {
        robj.__responseHandler.onLoad("Not valid javascript",'testmethod');
        fail("Server_Error expected");
    } catch(e) {
        assert('Passed',true);
    }
}
//-----------------------------------------------------------------------------
function testAsyncCallAppError() {
    var mhttp = JPSpan_Util_MockCreate(JPSpan_HttpClient);
    var mreq = JPSpan_Util_MockCreate(JPSpan_Request);
    var muh = JPSpan_Util_MockCreate(TestUserHandler);
    var robj = createRemoteObjectStub(mhttp);
    robj.__initResponseHandler(robj,muh);
    robj.applicationErrorFunc = function(e) {
        throw (e);
    }
    robj.serverErrorFunc = function(e) {
        throw (e);
    }
    robj.clientErrorFunc = function(e) {
        throw(e);
    }
    robj.__initClient();
    try {
        robj.__responseHandler.onLoad("new Function(\"throw 'Test app error';\");",'testmethod');
        fail("Application_Error expected");
    } catch(e) {
        assert('Passed',true);
    }
}
//-----------------------------------------------------------------------------
function testAsyncCallValid() {
    var mhttp = JPSpan_Util_MockCreate(JPSpan_HttpClient);
    var mreq = JPSpan_Util_MockCreate(JPSpan_Request);
    var muh = JPSpan_Util_MockCreate(TestUserHandler);
    var robj = createRemoteObjectStub(mhttp);
    robj.__initResponseHandler(robj,muh);
    robj.__initClient();
    robj.__responseHandler.onLoad("new Function(\"return 'Valid response';\");",'testmethod');
    assertEquals(1,muh.getCallCount('testmethod'));
}
//-----------------------------------------------------------------------------
-->
</script>
</head>
<body>
<h2>JPSpan_RemoteObject</h2>

<p><a href="<?php echo JSUNIT; ?>/testRunner.html?testpage=<?php echo $_SERVER['PHP_SELF']; ?>">Run Tests</a></p>
</body>
</html>
