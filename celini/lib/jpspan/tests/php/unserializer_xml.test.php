<?php
/**
* @version $Id: unserializer_xml.test.php,v 1.4 2004/12/10 23:27:28 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'Unserializer.php';
require_once JPSPAN . 'Types.php';
require_once JPSPAN . 'Unserializer/XML.php';


/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Unserializer_XML extends UnitTestCase {

    function TestOfJPSpan_Unserializer_XML() {
        $this->UnitTestCase('TestOfJPSpan_Unserializer_XML');
    }
    
    function setUp() {
        $this->U = & new JPSpan_Unserializer_XML();
    }
    
    function tearDown() {
        unset($this->U);
    }

    function testUnserialize() {
        $var = 'foo';
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><s>foo</s></r>';
        $this->assertEqual($this->U->unserialize($xml),$var);
    }
    
    function testUnserializeWhitespace() {
        $var = 'foo';
        $xml = "\r\n".' <?xml version="1.0" encoding="UTF-8"?><r><s>foo</s></r> '."\r\n";
        $this->assertEqual($this->U->unserialize($xml),$var);
    }

    function testUnserializeNotSerializedString() {
        $var = 'foo';
        $this->assertEqual($this->U->unserialize($var),$var);
    }
    
    function testUnserializeNotSerializedArray() {
        $var = array('<?xml version="1.0" encoding="UTF-8"?><r><s>foo</s></r>');
        $this->assertEqual($this->U->unserialize($var),$var);
    }
    
    function testRootEmpty() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r />';
        $this->assertNull($this->U->unserialize($xml));
    }
    
    function testNull() {
        $var = NULL;
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><n/></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }

    function testTrue() {
        $var = TRUE;
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><b v="1"/></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    
    function testFalse() {
        $var = FALSE;
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><b v="0"/></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    
    function testInteger() {
        $var = 1;
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><i v="1"/></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    
    function testIntegerZero() {
        $var = 0;
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><i v="0"/></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    
    function testDouble() {
        $var = 1.1;
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><d v="1.1"/></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    
    function testDoubleZero() {
        $var = 0.0;
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><d v="0.0"/></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    

    function testEmptyString() {
        $var = '';
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><s/></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    
    function testZero() {
        $var = 0;
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><i v="0"/></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    
    function testStringZero() {
        $var = '0';
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><s>0</s></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    
    function testStringWithEntities() {
        $var = 'x > y & y < z';
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><s>x > y &amp; y &lt; z</s></r>';
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    
    function testStringWhiteSpace() {
        $var = "foo\n\tbar";
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><s>foo'."\r\n\t".'bar</s></r>'; // \r get's removed
        $this->assertIdentical($this->U->unserialize($xml),$var);
    }
    
    function testArrayEmpty() {
        $var = array();
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><a/></r>';
        $this->assertEqual($this->U->unserialize($xml), $var);
    }
    
    function testArrayIndexed() {
        $var = array('a','b');
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><a><e k="0"><s>a</s>'.
            '</e><e k="1"><s>b</s></e></a></r>';
        $this->assertEqual($this->U->unserialize($xml), $var);
    }
    
    function testArrayAssoc() {
        $var = array('x'=>'a','y'=>'b');
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><a><e k="x"><s>a</s>'.
            '</e><e k="y"><s>b</s></e></a></r>';
        $this->assertEqual($this->U->unserialize($xml), $var);
    }
    
    function testArrayMixed() {
        $var = array('x'=>'a',0=>'b','y'=>'c');
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><a><e k="x"><s>a</s>'.
            '</e><e k="0"><s>b</s></e><e k="y"><s>c</s></e></a></r>';
        $this->assertEqual($this->U->unserialize($xml), $var);
    }
    
    function testArrayInArray() {
        $var = array('x'=>'a',0=>array(0=>'b','y'=>'c'));
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><a><e k="x"><s>a</s>'.
            '</e><e k="0"><a><e k="0"><s>b</s></e><e k="y"><s>c</s></e></a></e></a></r>';
        $this->assertEqual($this->U->unserialize($xml), $var);
    }
    
    function testObjectInArray() {
        $obj = new JPSpan_Object();
        $obj->x= 'b';
        $obj->y= 'c';
        $var = array('x'=>'a',0=>$obj);
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><a><e k="x"><s>a</s></e><e k="0">'.
            '<o c="JPSpan_Object"><e k="x"><s>b</s></e><e k="y"><s>c</s></e></o></e></a></r>';
        $this->assertEqual($this->U->unserialize($xml), $var);
    }
    
    function testUnserializeObject() {
        $var = new JPSpan_Object();
        $var->x = 'foo';
        $var->y = 'bar';
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><o c="JPSpan_Object"><e k="x"><s>foo</s></e><e k="y"><s>bar</s></e></o></r>';
        $this->assertEqual($this->U->unserialize($xml),$var);
    }
    
    function testObjectEmpty() {
        $var = new JPSpan_Object();
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><o c="JPSpan_Object"/></r>';
        $this->assertEqual($this->U->unserialize($xml), $var);
    }
    
    function testObjectWithNumericProperty() {
        $prop = 1;
        $var = new JPSpan_Object();
        // Things that make you go hmmm...
        $var->{$prop} = TRUE;
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><o c="JPSpan_Object"><e k="1"><b v="1"/></e></o></r>';
        $this->assertEqual($this->U->unserialize($xml),$var);
    }
    
    function testUnserializeError() {
        $var = new JPSpan_Error();
        $var->setError(3000,'foo','bar');
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><o c="JPSpan_Error"><e k="code"><i k="3000"/></e><e k="name"><s>foo</s></e><e k="message"><s>bar</s></e></o></r>';
        $error = $this->U->unserialize($var);
        $this->assertEqual($error->code,3000);
        $this->assertEqual($error->name,'foo');
        $this->assertEqual($error->message,'bar');
    }
    
}

class TestOfJPSpan_Unserializer_XML_Errors extends UnitTestCase {

    function TestOfJPSpan_Unserializer_XML_Errors() {
        $this->UnitTestCase('TestOfJPSpan_Unserializer_XML_Errors');
    }
    
    function setUp() {
        $this->U = & new JPSpan_Unserializer_XML();
    }
    
    function tearDown() {
        unset($this->U);
    }

    function testIllegalTag() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><foo />';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Illegal tag/');
    }
    
    function testBadlyFormed() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Badly formed XML/');
    }
    
    
    function testRootWithMultipleChildren() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><n/><b v="1"/></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Root node can only contain a single child node/');
    }
    
    function testNullWithChild() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><n><b v="1"/></n></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Scalar nodes cannot have children/');
    }
    
    function testBooleanWithChild() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><b v="1"><n/></b></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Scalar nodes cannot have children/');
    }
    
    function testBooleanNoValue() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><b/></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Value required for boolean/');
    }
    
    function testIntegerWithChild() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><i v="1"><n/></i></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Scalar nodes cannot have children/');
    }
    
    function testIntegerNoValue() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><i/></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Value required for integer/');
    }
    
    function testDoubleWithChild() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><d v="1.1"><n/></d></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Scalar nodes cannot have children/');
    }
    
    function testDoubleNoValue() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><d/></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Value required for double/');
    }
    
    function testStringWithChild() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><s>Foo<n/></s></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Scalar nodes cannot have children/');
    }
    
    function testArrayWithIllegalChild() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><a><n/></a></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Array nodes can only contain element nodes/');
    }
    
    function testObjectNoClass() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><o/></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Object node requires class attribute/');
    }
    
    function testObjectWithUnknownClass() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><o c="TestUnknownClass"/></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/testunknownclass/');
    }
    
    function testObjectWithIllegalChild() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><o c="JPSpan_Object"><n/></o></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Object nodes can only contain element nodes/');
    }
    
    function testElementNoKey() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><a><e/></a></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Element node requires key attribute/');
        $this->assertErrorPattern('/Array nodes can only contain element nodes/');
    }
    
    function testElementInElement() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><r><a><e k="0"><e k="1"/></e></a></r>';
        $this->assertFalse($this->U->unserialize($xml));
        $this->assertErrorPattern('/Element nodes can only be placed inside array or object nodes/');
    }
    
}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new GroupTest('JPSpan_Unserializer_XML Test Cases');
    $test->addTestCase(new TestOfJPSpan_Unserializer_XML());
    $test->addTestCase(new TestOfJPSpan_Unserializer_XML_Errors());
    $test->run(new HtmlReporter());
}
?>
