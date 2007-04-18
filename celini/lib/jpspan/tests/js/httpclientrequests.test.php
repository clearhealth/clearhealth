<?php
/**
* @version $Id: httpclientrequests.test.php,v 1.4 2004/12/10 23:32:06 harryf Exp $
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
<title>JPSpan_HttpClient requests</title>
<?php jsunit_drawHeader();
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('serialize.js');
JPSpan_Include_Register('encode/xml.js');
JPSpan_Include_Register('encode/php.js');
JPSpan_Include_Register('request/get.js');
JPSpan_Include_Register('request/post.js');
JPSpan_Include_Register('request/rawpost.js');
?>
<script language="JavaScript" type="text/javascript">
<!--

<?php jsunit_drawUtils(); ?>
<?php JPSpan_Includes_Display(); ?>

var loopback = "<?php echo JPSPAN_TESTS; ?>/testpages/loopback.php";
var sleep = "<?php echo JPSPAN_TESTS; ?>/testpages/sleep.php";
//-----------------------------------------------------------------------------

function setUpPage() {
    runAsyncGet();
    runAsyncPost();
    runAsyncRawPost();
    var intId = window.setInterval(function() {
            if ( isAsyncComplete() ) {
                setUpPageStatus = "complete";
                window.clearInterval(intId);
            }
        },1000);
}

function isAsyncComplete() {
    if ( asyncGetComplete && asyncPostComplete && asyncRawPostComplete ) {
        return true;
    }
    return false;
}

//-----------------------------------------------------------------------------

function runAsyncGet() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(new JPSpan_Encode_PHP());
    r.serverurl = loopback;
    r.addArg('x','foo');
    c.asyncCall(r,AsyncGetHandler);
}

var asyncGetComplete = false;

var AsyncGetHandler = {
    onLoad: function(result) {
        document.getElementById('asyncGetResult').value = result;
        asyncGetComplete = true;
    },
    onError: function(e) {
        document.getElementById('asyncGetResult').value = e.message;
        asyncGetComplete = true;
    }
}

function testAsyncGet() {
    assertEquals('a:1:{s:1:"x";s:3:"foo";}',document.getElementById('asyncGetResult').value);
}

//-----------------------------------------------------------------------------

function testAsyncGetTimeout() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(new JPSpan_Encode_PHP());
    r.timeout = 1;
    r.serverurl = sleep;
    try {
        c.asyncCall(r,new Object());
    } catch (e) {
        assertTrue('Test passed',true);
    }
    
}

//-----------------------------------------------------------------------------

function runAsyncPost() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Post(new JPSpan_Encode_PHP());
    r.serverurl = loopback;
    r.addArg('x','foo');
    c.asyncCall(r,AsyncPostHandler);
}

var asyncPostComplete = false;

var AsyncPostHandler = {
    onLoad: function(result) {
        document.getElementById('asyncPostResult').value = result;
        asyncPostComplete = true;
    },
    onError: function(e) {
        document.getElementById('asyncPostResult').value = e.message;
        asyncPostComplete = true;
    }
}

function testAsyncPost() {
    assertEquals('a:1:{s:1:"x";s:3:"foo";}',document.getElementById('asyncPostResult').value);
}

//-----------------------------------------------------------------------------

function testAsyncPostTimeout() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Post(new JPSpan_Encode_PHP());
    r.timeout = 1;
    r.serverurl = sleep;
    try {
        c.asyncCall(r,new Object());
        fail('This request should have timed out');
    } catch (e) {
        assertTrue('Test passed',true);
    }
    
}

//-----------------------------------------------------------------------------

function runAsyncRawPost() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_RawPost(new JPSpan_Encode_PHP());
    r.serverurl = loopback;
    r.addArg('x','foo');
    c.asyncCall(r,AsyncRawPostHandler);
}

var asyncRawPostComplete = false;

var AsyncRawPostHandler = {
    onLoad: function(result) {
        document.getElementById('asyncRawPostResult').value = result;
        asyncRawPostComplete = true;
    },
    onError: function(e) {
        document.getElementById('asyncRawPostResult').value = e.message;
        asyncRawPostComplete = true;
    }
}

function testAsyncRawPost() {
    assertEquals('a:1:{s:1:"x";s:3:"foo";}',document.getElementById('asyncRawPostResult').value);
}

//-----------------------------------------------------------------------------

function testAsyncRawPostTimeout() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_RawPost(new JPSpan_Encode_PHP());
    r.timeout = 1;
    r.serverurl = sleep;
    try {
        c.asyncCall(r,new Object());
    } catch (e) {
        assertTrue('Test passed',true);
    }
    
}

//-----------------------------------------------------------------------------

function testSyncGet() {
    clear();
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(new JPSpan_Encode_PHP());
    r.serverurl = loopback;
    r.addArg('x','foo');
    echo(c.call(r));
    assertEquals('a:1:{s:1:"x";s:3:"foo";}',result());
}

//-----------------------------------------------------------------------------

function testSyncPost() {
    clear();
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Post(new JPSpan_Encode_PHP());
    r.serverurl = loopback;
    r.addArg('x','foo');
    echo(c.call(r));
    assertEquals('a:1:{s:1:"x";s:3:"foo";}',result());
}

//-----------------------------------------------------------------------------

function testSyncRawPost() {
    clear();
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_RawPost(new JPSpan_Encode_PHP());
    r.serverurl = loopback;
    r.addArg('x','foo');
    echo(c.call(r));
    assertEquals('a:1:{s:1:"x";s:3:"foo";}',result());
}

//-----------------------------------------------------------------------------
-->
</script>
</head>
<body>
<h2>JPSpan_HttpClient requests</h2>
<p>The async tests here will usually fail when there's a higher latency to the server</p>
<p><b>Warning:</b> it also runs synchronous requests which may hang your browser for a while, if server is slow</p>
<p><a href="<?php echo JSUNIT; ?>/testRunner.html?testpage=<?php echo $_SERVER['PHP_SELF']; ?>">Run Tests</a></p>

<?php jsunit_drawResults(); ?>

<div id="asyncGetResult"></div>
<div id="asyncPostResult"></div>
<div id="asyncRawPostResult"></div>

</body>
</html>
