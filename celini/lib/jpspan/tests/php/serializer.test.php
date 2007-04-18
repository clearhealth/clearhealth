<?php
/**
* @version $Id: serializer.test.php,v 1.3 2004/11/23 12:09:25 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'Serializer.php';
require_once JPSPAN . 'Types.php';

/**
* Used to test
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Serializer_Custom {}

/**
* A sort-of Mock object
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Serializer_Generate_Custom extends JPSpan_SerializedElement {
    function generate(&$code) {
        $code->write('// Test;');
        $code->enabled = FALSE;
    }
}

/**
* Most of these tests are not just testing the <i>unit</i> but
* also numerous related JS code generation classes. Such is the
* way with code generation
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Serializer extends UnitTestCase {

    function TestOfJPSpan_Serializer() {
        $this->UnitTestCase('TestOfJPSpan_Serializer');
    }
    
    function testString() {
        $js = 'new Function("var t1 = \\\'Hello World\\\';return t1;");';
        $res = JPSpan_Serializer::serialize('Hello World');
        $this->assertEqual($res,$js);
    }
    
    function testStringEmpty() {
        $js = 'new Function("var t1 = \\\'\\\';return t1;");';
        $res = JPSpan_Serializer::serialize('');
        $this->assertEqual($res,$js);
    }
    
    function testStringZero() {
        $js = 'new Function("var t1 = \\\'0\\\';return t1;");';
        $res = JPSpan_Serializer::serialize('0');
        $this->assertEqual($res,$js);
    }
    
    function testStringQuote() {
        $js = 'new Function("var t1 = \\\'Foo\\\\\\"Bar\\\\\\"Foo\\\';return t1;");';
        $res = JPSpan_Serializer::serialize('Foo"Bar"Foo');
        $this->assertEqual($res,$js);
    }
    
    function testStringApostrophe() {
        $js = 'new Function("var t1 = \\\'Foo\\\\\\\'Bar\\\\\\\'Foo\\\';return t1;");';
        $res = JPSpan_Serializer::serialize('Foo\'Bar\'Foo');
        $this->assertEqual($res,$js);
    }
    
    function testStringLinefeed() {
        $js = 'new Function("var t1 = \\\'Foo\\\nBar\\\';return t1;");';
        $res = JPSpan_Serializer::serialize("Foo\nBar");
        $this->assertEqual($res,$js);
    }
    
    function testStringReturn() {
        $js = 'new Function("var t1 = \\\'Foo\\\nBar\\\';return t1;");';
        $res = JPSpan_Serializer::serialize("Foo\r\nBar");
        $this->assertEqual($res,$js);
    }
    
    function testStringTab() {
        $js = 'new Function("var t1 = \\\'Foo\\\tBar\\\';return t1;");';
        $res = JPSpan_Serializer::serialize("Foo\tBar");
        $this->assertEqual($res,$js);
    }
    
    function testBooleanTrue() {
        $js = 'new Function("var t1 = true;return t1;");';
        $res = JPSpan_Serializer::serialize(TRUE);
        $this->assertEqual($res,$js);
    }
    
    function testBooleanFalse() {
        $js = 'new Function("var t1 = false;return t1;");';
        $res = JPSpan_Serializer::serialize(FALSE);
        $this->assertEqual($res,$js);
    }
    
    function testInteger() {
        $js = 'new Function("var t1 = parseInt(\\\'1\\\');return t1;");';
        $res = JPSpan_Serializer::serialize(1);
        $this->assertEqual($res,$js);
    }
    
    function testIntegerZero() {
        $js = 'new Function("var t1 = parseInt(\\\'0\\\');return t1;");';
        $res = JPSpan_Serializer::serialize(0);
        $this->assertEqual($res,$js);
    }
    
    function testFloat() {
        $js = 'new Function("var t1 = parseFloat(\\\'2.2\\\');return t1;");';
        $res = JPSpan_Serializer::serialize(2.2);
        $this->assertEqual($res,$js);
    }
    
    function testFloatZeroPointZero() {
        // 0.0 is a float in PHP (!)
        $js = 'new Function("var t1 = parseFloat(\\\'0\\\');return t1;");';
        $res = JPSpan_Serializer::serialize(0.0);
        $this->assertEqual($res,$js);
    }
    
    function testOfNull() {
        $js = 'new Function("var t1 = null;return t1;");';
        $res = JPSpan_Serializer::serialize(NULL);
        $this->assertEqual($res,$js);
    }
    
    function testArrayEmpty() {
        $js = 'new Function("var t1 = new Array();t1.toString = function() { var str = \\\'[\\\';var sep = \\\'\\\';for (var prop in this) { if (prop == \\\'toString\\\') { continue; }str+=sep+prop+\\\': \\\'+this[prop];sep = \\\', \\\';} return str+\\\']\\\';};return t1;");';
        $res = JPSpan_Serializer::serialize(array());
        $this->assertEqual($res,$js);
    }

    function testArrayIndexed() {
        $js = 'new Function("var t1 = new Array();var t2 = \\\'a\\\';t1[0] = t2;var t3 = parseInt(\\\'2\\\');t1[1] = t3;var t4 = \\\'b\\\';t1[2] = t4;t1.toString = function() { var str = \\\'[\\\';var sep = \\\'\\\';for (var prop in this) { if (prop == \\\'toString\\\') { continue; }str+=sep+prop+\\\': \\\'+this[prop];sep = \\\', \\\';} return str+\\\']\\\';};return t1;");';
        $res = JPSpan_Serializer::serialize(array('a',2,'b'));
        $this->assertEqual($res,$js);
    }
    
    function testArrayAssociative() {
        $js = 'new Function("var t1 = new Array();var t2 = parseInt(\\\'1\\\');t1[\\\'a\\\'] = t2;var t3 = parseInt(\\\'2\\\');t1[\\\'b\\\'] = t3;var t4 = parseInt(\\\'3\\\');t1[\\\'c\\\'] = t4;t1.toString = function() { var str = \\\'[\\\';var sep = \\\'\\\';for (var prop in this) { if (prop == \\\'toString\\\') { continue; }str+=sep+prop+\\\': \\\'+this[prop];sep = \\\', \\\';} return str+\\\']\\\';};return t1;");';
        $res = JPSpan_Serializer::serialize(array('a'=>1,'b'=>2,'c'=>3));
        $this->assertEqual($res,$js);
    }
    
    function testArrayMixed() {
        $js = 'new Function("var t1 = new Array();var t2 = parseInt(\\\'1\\\');t1[\\\'a\\\'] = t2;var t3 = \\\'b\\\';t1[0] = t3;var t4 = parseInt(\\\'3\\\');t1[\\\'c\\\'] = t4;t1.toString = function() { var str = \\\'[\\\';var sep = \\\'\\\';for (var prop in this) { if (prop == \\\'toString\\\') { continue; }str+=sep+prop+\\\': \\\'+this[prop];sep = \\\', \\\';} return str+\\\']\\\';};return t1;");';
        $res = JPSpan_Serializer::serialize(array('a'=>1,'b','c'=>3));
        $this->assertEqual($res,$js);
    }
    
    function testObjectStdClass() {
        $js = 'new Function("var t1 = new Object();return t1;");';
        $res = JPSpan_Serializer::serialize(new stdClass());
        $this->assertEqual($res,$js);
    }
    
    function testObjectStdClassProperties() {
        $obj = new stdClass();
        $obj->a = 0;
        $obj->b = FALSE;
        $obj->c = NULL;
        $obj->d = '';
        $js = 'new Function("var t1 = new Object();var t2 = parseInt(\\\'0\\\');t1.a = t2;var t3 = false;t1.b = t3;var t4 = null;t1.c = t4;var t5 = \\\'\\\';t1.d = t5;return t1;");';
        $res = JPSpan_Serializer::serialize($obj);
        $this->assertEqual($res,$js);
    }
    
    function testObjectCustom() {
        $obj = new TestOfJPSpan_Serializer_Custom();
        JPSpan_Serializer::addType('TestOfJPSpan_Serializer_Custom','TestOfJPSpan_Serializer_Generate_Custom');
        $js = '// Test;';
        $res = JPSpan_Serializer::serialize($obj);
        $this->assertEqual($res,$js);
    }
    
    function testResource() {
        if ( $d = opendir('.') ) {
            $js = 'new Function("var t1 = null;return t1;");';
            $res = JPSpan_Serializer::serialize($d);
            $this->assertEqual($res,$js);
            closedir($d);
        }
    }
    
    function testError() {
        $obj = new JPSpan_Error();
        $obj->setError(3000,'TestError','testing');
        $obj->foo = 'bar';
        $js = 'new Function("var e = new Error(\\\'testing\\\');e.name = \\\'TestError\\\';e.code = \\\'3000\\\';throw e;");';
        $res = JPSpan_Serializer::serialize($obj);
        $this->assertEqual($res,$js);
    }
    
    function testErrorNested() {
        $obj = new JPSpan_Error();
        $obj->setError(3000,'TestError','testing');
        $array = array('a',$obj,'b');
        $js = 'new Function("var e = new Error(\\\'testing\\\');e.name = \\\'TestError\\\';e.code = \\\'3000\\\';throw e;");';
        $res = JPSpan_Serializer::serialize($array);
        $this->assertEqual($res,$js);
    }
    
}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfJPSpan_Serializer();
    $test->run(new HtmlReporter());
}
?>
