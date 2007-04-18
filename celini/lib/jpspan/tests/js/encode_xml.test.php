<?php
/**
* @version $Id: encode_xml.test.php,v 1.5 2005/04/28 13:30:21 harryf Exp $
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
<title>JPSpan_Encode_Xml</title>
<?php jsunit_drawHeader();
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('encode/xml.js');
JPSpan_Include_Register('util/mock.js');
JPSpan_Include_Register('util/mockxmlhttp.js');
?>
<script language="JavaScript" type="text/javascript">
<!--

<?php jsunit_drawUtils(); ?>
<?php JPSpan_Includes_Display(); ?>
var xmlOpen = String.fromCharCode(60);
//-----------------------------------------------------------------------------

function testString() {
    var enc = new JPSpan_Encode_Xml();
    var foo = "Hello World";
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><s>Hello World</s></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testMbString() {
    var enc = new JPSpan_Encode_Xml();
    var foo = 'Iñtërnâtiônàlizætiøn';
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><s>Iñtërnâtiônàlizætiøn</s></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------
function testStringEmpty() {
    var enc = new JPSpan_Encode_Xml();
    var foo = "";
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><s></s></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testBooleanTrue() {
    var enc = new JPSpan_Encode_Xml();
    var foo = true;
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><b v=\"1\"/></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testBooleanFalse() {
    var enc = new JPSpan_Encode_Xml();
    var foo = false;
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><b v=\"0\"/></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------
function testNull() {
    var enc = new JPSpan_Encode_Xml();
    var foo = null;
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><n/></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------
function testUndef() {
    var enc = new JPSpan_Encode_Xml();
    try {
        assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><n/></r>",enc.encode(foo));
        fail('undef should cause reference error');
    } catch(e) {
        assert(true);
    }
}

//-----------------------------------------------------------------------------
function testInteger() {
    var enc = new JPSpan_Encode_Xml();
    var foo = 2;
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><i v=\"2\"/></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testDouble() {
    var enc = new JPSpan_Encode_Xml();
    var foo = 2.2;
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><d v=\"2.2\"/></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testZero() {
    var enc = new JPSpan_Encode_Xml();
    var foo = 0;
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><i v=\"0\"/></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testZeroPointZero() {
    var enc = new JPSpan_Encode_Xml();
    var foo = 0.0;
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><i v=\"0\"/></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testFunction() {
    var enc = new JPSpan_Encode_Xml();
    var foo = new Function("x", "y", "return (x + y)/2");
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><n/></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testArrayEmpty() {
    var enc = new JPSpan_Encode_Xml();
    var foo = new Array();
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><a></a></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testArray() {
    var enc = new JPSpan_Encode_Xml();
    var foo = [
        'a','b','c'
    ];
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><a><e k=\"0\"><s>a</s></e><e k=\"1\"><s>b</s></e><e k=\"2\"><s>c</s></e></a></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testArrayMixed() {
    var enc = new JPSpan_Encode_Xml();
    var foo = new Array();
    foo.push('a');
    foo.push('b');
    foo.push('c');
    foo[1] = 'x';
    foo['2'] = 'y';
    foo.push('b');
    foo.push('c');
    foo.m = 'z';
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><a><e k=\"0\"><s>a</s></e><e k=\"1\"><s>x</s></e><e k=\"2\"><s>y</s></e><e k=\"3\"><s>b</s></e><e k=\"4\"><s>c</s></e><e k=\"m\"><s>z</s></e></a></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testObjectEmpty() {
    var enc = new JPSpan_Encode_Xml();
    var foo = new Object();
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><o c=\"jpspan_object\"></o></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testObject() {
    var enc = new JPSpan_Encode_Xml();
    var foo = new Object();
    foo.a = 'x';
    foo.b = 2;
    foo.c = 3.3;
    foo.d = false;
    foo.e = ['a','b','c'];
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><o c=\"jpspan_object\"><e k=\"a\"><s>x</s></e><e k=\"b\"><i v=\"2\"/></e><e k=\"c\"><d v=\"3.3\"/></e><e k=\"d\"><b v=\"0\"/></e><e k=\"e\"><a><e k=\"0\"><s>a</s></e><e k=\"1\"><s>b</s></e><e k=\"2\"><s>c</s></e></a></e></o></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testObjectProtoType() {
    var enc = new JPSpan_Encode_Xml();
    var foo = new Object();
    foo.prototype = {
        a: 'x',
        b: 2,
        c: 2,
        d: false,
        e: ['a','b','c'],
        f: function(param) {
            return false;
        }
    }
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><o c=\"jpspan_object\"><e k=\"prototype\"><o c=\"jpspan_object\"><e k=\"a\"><s>x</s></e><e k=\"b\"><i v=\"2\"/></e><e k=\"c\"><i v=\"2\"/></e><e k=\"d\"><b v=\"0\"/></e><e k=\"e\"><a><e k=\"0\"><s>a</s></e><e k=\"1\"><s>b</s></e><e k=\"2\"><s>c</s></e></a></e><e k=\"f\"><n/></e></o></e></o></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------
/**
* Recursive references are bad news. Not supported
*/

function testRecursiveReference() {
    var enc = new JPSpan_Encode_Xml();
    var a = ['arrayA'];
    var b = ['arrayB',a];
    a.push(b);
    try {
        enc.encode(a);
        fail('Recursive references not supported. Should not have got here');
    } catch(e) {
        assert(true);
    }
}
//-----------------------------------------------------------------------------
/**
* Date not yet supported
*/

function testDate() {
    var enc = new JPSpan_Encode_Xml();
    var foo = new Date(2004,9,2);
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><o c=\"jpspan_object\"></o></r>",enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testError() {
var enc = new JPSpan_Encode_Xml();
    var e = new Error('Test');
    assertEquals(xmlOpen+"?xml version=\"1.0\" encoding=\"UTF-8\"?><r><o c=\"jpspan_error\"><e k=\"name\"><s>Error</s></e><e k=\"message\"><s>Test</s></e></o></r>",enc.encode(e));
}

-->
</script>
</head>
<body>
<h2>JPSpan_Encode_Xml</h2>
<p><a href="<?php echo JSUNIT; ?>/testRunner.html?testpage=<?php echo $_SERVER['PHP_SELF']; ?>">Run Tests</a></p>

</body>
</html>
