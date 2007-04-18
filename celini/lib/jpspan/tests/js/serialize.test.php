<?php
/**
* @version $Id: serialize.test.php,v 1.5 2004/12/10 23:32:06 harryf Exp $
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
<title>JPSpan_Serialize</title>
<?php jsunit_drawHeader();
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('serialize.js');
JPSpan_Include_Register('encode/xml.js');
JPSpan_Include_Register('util/mock.js');
?>
<script language="JavaScript" type="text/javascript">
<!--

<?php jsunit_drawUtils(); ?>
<?php JPSpan_Includes_Display(); ?>

//-----------------------------------------------------------------------------
function testNull() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = null;
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeNull'));
}
//-----------------------------------------------------------------------------
function testTrue() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = true;
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeTrue'));
}
//-----------------------------------------------------------------------------
function testFalse() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = false;
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeFalse'));
}
//-----------------------------------------------------------------------------
function testInteger() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = 3;
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeInteger'));
    var args = mEnc.getLastCallArgs('encodeInteger')
    assertEquals(3, args[0]);
}
//-----------------------------------------------------------------------------
function testDouble() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = 3.3;
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeDouble'));
    var args = mEnc.getLastCallArgs('encodeDouble')
    assertEquals(3.3, args[0]);
}
//-----------------------------------------------------------------------------
function testString() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = 'Foo';
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeString'));
    var args = mEnc.getLastCallArgs('encodeString')
    assertEquals('Foo', args[0]);
}
//-----------------------------------------------------------------------------
function testFunction() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = function(){};
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeNull'));
}
//-----------------------------------------------------------------------------
function testFunctionObject() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = new Function("x", "y", "return (x + y)/2");
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeNull'));
}
//-----------------------------------------------------------------------------
function testFunctionConstructor() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = new function() {
        this.x = 1;
        this.y = 2;
    };
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeObject'));
    var args = mEnc.getLastCallArgs('encodeObject')
    assertEquals(foo, args[0]);
    assertEquals(s, args[1]);
    assertEquals('JPSpan_Object', args[2]);
}
//-----------------------------------------------------------------------------
function testArrayLiteral() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = [1,2,3];
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeArray'));
    var args = mEnc.getLastCallArgs('encodeArray')
    assertEquals(foo, args[0]);
    assertEquals(s, args[1]);
}
//-----------------------------------------------------------------------------
function testArrayObject() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = new Array();
    foo.push(1);
    foo.push(2);
    foo.push(3);
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeArray'));
    var args = mEnc.getLastCallArgs('encodeArray')
    assertEquals(foo, args[0]);
    assertEquals(s, args[1]);
}
//-----------------------------------------------------------------------------
function testObject() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = new Object();
    foo.x = 1;
    foo.y = 2;
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeObject'));
    var args = mEnc.getLastCallArgs('encodeObject')
    assertEquals(foo, args[0]);
    assertEquals(s, args[1]);
    assertEquals('JPSpan_Object', args[2]);
}
//-----------------------------------------------------------------------------
function MyObject(){}
MyObject.prototype.x = 1;
MyObject.prototype.y = 2;

function testMyObject() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = new MyObject();
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeObject'));
    var args = mEnc.getLastCallArgs('encodeObject')
    assertEquals(foo, args[0]);
    assertEquals(s, args[1]);
    assertEquals('JPSpan_Object', args[2]);
}

function encodeMyObject(v,s,cname) {
    return 'Got '+cname;
}
function testMyObjectAddType() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    s.addType('MyObject',encodeMyObject);
    var foo = new MyObject();
    assertEquals('Got MyObject',s.serialize(foo));
}

//-----------------------------------------------------------------------------
function testRegexpLiteral() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = /[\W_]/;
    s.serialize(foo);
    assertEquals('Expected fail on IE',1, mEnc.getCallCount('encodeNull'));
}
//-----------------------------------------------------------------------------
function testRegexpObject() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = new RegExp("[\W_]");
    s.serialize(foo);
    assertEquals('Expected fail on IE',1, mEnc.getCallCount('encodeNull'));
}
//-----------------------------------------------------------------------------
function testDate() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = new Date();
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeObject'));
    var args = mEnc.getLastCallArgs('encodeObject')
    assertEquals(foo, args[0]);
    assertEquals(s, args[1]);
    assertEquals('JPSpan_Object', args[2]);
}
//-----------------------------------------------------------------------------
function testWindow() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = window;
    s.serialize(foo);
    assertEquals('Expected fail on IE',1, mEnc.getCallCount('encodeObject'));
    var args = mEnc.getLastCallArgs('encodeObject')
    assertEquals(foo, args[0]);
    assertEquals(s, args[1]);
    assertEquals('JPSpan_Object', args[2]);
}
//-----------------------------------------------------------------------------
var MyHash = {
    Set : function(foo,bar) {this[foo] = bar;},
    Get : function(foo) {return this[foo];}
}
function testClosure() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    MyHash.Set('x',1);
    var foo = MyHash;
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeObject'));
    var args = mEnc.getLastCallArgs('encodeObject')
    assertEquals(foo, args[0]);
    assertEquals(s, args[1]);
    assertEquals('JPSpan_Object', args[2]);
}
//-----------------------------------------------------------------------------
function testError() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    var foo = new Error('Test');
    s.serialize(foo);
    assertEquals(1, mEnc.getCallCount('encodeError'));
    var args = mEnc.getLastCallArgs('encodeError')
    assertEquals(foo, args[0]);
    assertEquals(s, args[1]);
    assertEquals('JPSpan_Error', args[2]);
}
//-----------------------------------------------------------------------------
function testReferenceError() {
    var mEnc = JPSpan_Util_MockCreate(JPSpan_Encode_Xml);
    var s = new JPSpan_Serialize(mEnc);
    try {
        doesNotExist();
    } catch (e) {
        var foo = e;
    }
    s.serialize(foo);
    assertEquals('Expected fail on IE',1, mEnc.getCallCount('encodeError'));
    var args = mEnc.getLastCallArgs('encodeError')
    assertEquals(foo, args[0]);
    assertEquals(s, args[1]);
    assertEquals('JPSpan_Error', args[2]);
}
//-----------------------------------------------------------------------------
-->
</script>
</head>
<body>
<h2>JPSpan_Serialize</h2>

<p><a href="<?php echo JSUNIT; ?>/testRunner.html?testpage=<?php echo $_SERVER['PHP_SELF']; ?>">Run Tests</a></p>
</body>
</html>
