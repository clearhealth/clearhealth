<?php
/**
* @version $Id: request.test.php,v 1.3 2004/12/10 23:32:06 harryf Exp $
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
<title>JPSpan_Request</title>
<?php jsunit_drawHeader();
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('request.js');
JPSpan_Include_Register('encode/xml.js');
JPSpan_Include_Register('util/mock.js');
?>
<script language="JavaScript" type="text/javascript">
<!--

<?php jsunit_drawUtils(); ?>
<?php JPSpan_Includes_Display(); ?>

//-----------------------------------------------------------------------------
function testReset() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var r = new JPSpan_Request(mEnc);
    r.serverurl = 'foo';
    r.request = 'foo';
    r.body = 'foo';
    r.args = [1,2,3];
    r.type = 'async';
    r.http = 'foo';
    r.timeout = 1;
    r.reset();
    assertEquals('',r.serverurl);
    assertEquals('',r.requesturl);
    assertEquals('',r.body);
    assertEquals(null,r.args);
    assertEquals(null,r.type);
    assertEquals(null,r.http);
    assertEquals(20000,r.timeout);
}
//-----------------------------------------------------------------------------
function testArgs() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var r = new JPSpan_Request(mEnc);
    r.addArg('a','x');
    r.addArg(1,'y');
    r.addArg('b','z');
    var test = new Array();
    test['a'] = 'x';
    test[1] = 'y';
    test['b'] = 'z';
    assertEquals('x',r.args['a']);
    assertEquals('y',r.args[1]);
    assertEquals('z',r.args['b']);
}
//-----------------------------------------------------------------------------
function testInvalidArg() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var r = new JPSpan_Request(mEnc);
    try {
        r.addArg('$','%');
        fail('Should have raised exception');
    } catch (e) {
        assertEquals(1004,e.code);
    }
}
//-----------------------------------------------------------------------------
-->
</script>
</head>
<body>
<h2>JPSpan_Request</h2>

<p><a href="<?php echo JSUNIT; ?>/testRunner.html?testpage=<?php echo $_SERVER['PHP_SELF']; ?>">Run Tests</a></p>
</body>
</html>
