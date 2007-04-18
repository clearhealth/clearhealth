<?php
/**
* @version $Id: encode_php.test.php,v 1.5 2004/12/10 23:32:05 harryf Exp $
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
<title>JPSpan_Encode_PHP</title>
<?php jsunit_drawHeader();
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('encode/php.js');
JPSpan_Include_Register('util/mock.js');
JPSpan_Include_Register('util/mockxmlhttp.js');
?>
<script language="JavaScript" type="text/javascript">
<!--

<?php jsunit_drawUtils(); ?>
<?php JPSpan_Includes_Display(); ?>

//-----------------------------------------------------------------------------

function testString() {
    var enc = new JPSpan_Encode_PHP();
    var foo = "Hello World";
    assertEquals('s:11:"Hello World";',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testMbString() {
    var enc = new JPSpan_Encode_PHP();
    var foo = 'Iñtërnâtiônàlizætiøn';
    assertEquals('s:13:"Itrntinliztin";',enc.encode(foo));
}

//-----------------------------------------------------------------------------
function testStringEmpty() {
    var enc = new JPSpan_Encode_PHP();
    var foo = "";
    assertEquals('s:0:"";',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testBooleanTrue() {
    var enc = new JPSpan_Encode_PHP();
    var foo = true;
    assertEquals('b:1;',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testBooleanFalse() {
    var enc = new JPSpan_Encode_PHP();
    var foo = false;
    assertEquals('b:0;',enc.encode(foo));
}

//-----------------------------------------------------------------------------
function testNull() {
    var enc = new JPSpan_Encode_PHP();
    var foo = null;
    assertEquals('N;',enc.encode(foo));
}

//-----------------------------------------------------------------------------
function testUndef() {
    var enc = new JPSpan_Encode_PHP();
    try {
        assertEquals('N;',enc.encode(foo));
        fail('undef should cause reference error');
    } catch(e) {
        assert(true);
    }
}

//-----------------------------------------------------------------------------
function testInteger() {
    var enc = new JPSpan_Encode_PHP();
    var foo = 2;
    assertEquals('i:2;',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testDouble() {
    var enc = new JPSpan_Encode_PHP();
    var foo = 2.2;
    assertEquals('d:2.2;',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testZero() {
    var enc = new JPSpan_Encode_PHP();
    var foo = 0;
    assertEquals('i:0;',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testZeroPointZero() {
    var enc = new JPSpan_Encode_PHP();
    var foo = 0.0;
    assertEquals('i:0;',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testFunction() {
    var enc = new JPSpan_Encode_PHP();
    var foo = new Function("x", "y", "return (x + y)/2");
    assertEquals('N;',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testArrayEmpty() {
    var enc = new JPSpan_Encode_PHP();
    var foo = new Array();
    assertEquals('a:0:{}',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testArray() {
    var enc = new JPSpan_Encode_PHP();
    var foo = [
        'a','b','c'
    ];
    assertEquals('a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testArrayMixed() {
    var enc = new JPSpan_Encode_PHP();
    var foo = new Array();
    foo.push('a');
    foo.push('b');
    foo.push('c');
    foo[1] = 'x';
    foo['2'] = 'y';
    foo.push('b');
    foo.push('c');
    foo.m = 'z';
    assertEquals('a:6:{i:0;s:1:"a";i:1;s:1:"x";i:2;s:1:"y";i:3;s:1:"b";i:4;s:1:"c";s:1:"m";s:1:"z";}',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testObjectEmpty() {
    var enc = new JPSpan_Encode_PHP();
    var foo = new Object();
    assertEquals('O:13:"jpspan_object":0:{}',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testObject() {
    var enc = new JPSpan_Encode_PHP();
    var foo = new Object();
    foo.a = 'x';
    foo.b = 2;
    foo.c = 3.3;
    foo.d = false;
    foo.e = ['a','b','c'];
    assertEquals('O:13:"jpspan_object":5:{s:1:"a";s:1:"x";s:1:"b";i:2;s:1:"c";d:3.3;s:1:"d";b:0;s:1:"e";a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}}',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testObjectProtoType() {
    var enc = new JPSpan_Encode_PHP();
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
    assertEquals('O:13:"jpspan_object":1:{s:9:"prototype";O:13:"jpspan_object":6:{s:1:"a";s:1:"x";s:1:"b";i:2;s:1:"c";i:2;s:1:"d";b:0;s:1:"e";a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}s:1:"f";N;}}',enc.encode(foo));
}

//-----------------------------------------------------------------------------
/**
* Recursive references are bad news. Not supported
*/
function testRecursiveReference() {
    var enc = new JPSpan_Encode_PHP();
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
    var enc = new JPSpan_Encode_PHP();
    var foo = new Date(2004,9,2);
    assertEquals('O:13:"jpspan_object":0:{}',enc.encode(foo));
}

//-----------------------------------------------------------------------------

function testError() {
    var enc = new JPSpan_Encode_PHP();
    var e = new Error('Test');
    assertEquals('O:12:"jpspan_error":2:{s:4:"name";s:5:"Error";s:7:"message";s:4:"Test";}',enc.encode(e));
}

-->
</script>
</head>
<body>
<h2>JPSpan_Encode_PHP</h2>
<p><a href="<?php echo JSUNIT; ?>/testRunner.html?testpage=<?php echo $_SERVER['PHP_SELF']; ?>">Run Tests</a></p>

</body>
</html>
