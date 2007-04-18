<?php
/**
* @version $Id: unserializer_php.test.php,v 1.3 2004/12/10 23:27:28 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'Unserializer.php';
require_once JPSPAN . 'Types.php';
require_once JPSPAN . 'Unserializer/PHP.php';

/**
* @package JPSpan
* @subpackage Tests
*/
class Unserializer_TestClass {}

/**
* @package JPSpan
* @subpackage Tests
*/
class Unserializer_TestIllegalClass {}

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Unserializer_PHP extends UnitTestCase {

    function TestOfJPSpan_Unserializer_PHP() {
        $this->UnitTestCase('TestOfJPSpan_Unserializer_PHP');
    }
    
    function setUp() {
        $this->U = & new JPSpan_Unserializer_PHP();
    }
    
    function tearDown() {
        unset($this->U);
    }
    
    function testUnserialize() {
        $var = 'foo';
        $this->assertEqual($this->U->unserialize(serialize($var),'php'),$var);
    }
    
    function testUnserializeWhitespace() {
        $var = 'foo';
        $s = " \r\n ".serialize($var)." \r\n ";
        $this->assertEqual($this->U->unserialize($s,'php'),$var);
    }
    
    function testUnserializeNotSerialized() {
        $var = 'foo';
        $this->assertEqual($this->U->unserialize($var,'php'),$var);
    }
    
    function testUnserializeObject() {
        $var = new JPSpan_Object();
        $var->x = 'foo';
        $var->y = 'bar';
        $this->assertEqual($this->U->unserialize(serialize($var),'php'),$var);
    }
    
    function testUnserializeError() {
        $var = new JPSpan_Error();
        $var->setError(3000,'foo','bar');
        $error = $this->U->unserialize(serialize($var),'php');
        $this->assertEqual($error->code,3000);
        $this->assertEqual($error->name,'foo');
        $this->assertEqual($error->message,'bar');
    }
    
    function testUnserializeUnknownClass() {
        $serialized = 'O:16:"TestUnknownClass":0:{}';
        $this->U->unserialize($serialized,'php');
        $this->assertErrorPattern('/testunknownclass/');
    }
    
    function testIllegalClass() {
        $obj = new Unserializer_TestIllegalClass();
        $this->assertFalse($this->U->unserialize(serialize($obj),'php'));
        $this->assertErrorPattern('/Illegal type: unserializer_testillegalclass/');
    }
    
    function testNestedIllegalClass() {
        $obj = new JPSpan_Object();
        $obj->x = new Unserializer_TestIllegalClass();
        $this->assertFalse($this->U->unserialize(serialize($obj),'php'));
        $this->assertErrorPattern('/Illegal type: unserializer_testillegalclass/');
    }
    
    function testArrayNestedIllegalClass() {
        $array = array();
        $array['x'] = new Unserializer_TestIllegalClass();
        $this->assertFalse($this->U->unserialize(serialize($array),'php'));
        $this->assertErrorPattern('/Illegal type: unserializer_testillegalclass/');
    }
    
    function testRegisteredClass() {
        JPSpan_Unserializer::addType('Unserializer_TestClass');
        $obj = new Unserializer_TestClass();
        $this->assertEqual($this->U->unserialize(serialize($obj),'php'),$obj);
        $this->assertNoErrors();
    }
    
    function testFalse() {
        $var = FALSE;
        $this->assertIdentical($this->U->unserialize(serialize($var),'php'),$var);
    }
    
    function testNull() {
        $var = NULL;
        $this->assertIdentical($this->U->unserialize(serialize($var),'php'),$var);
    }
    
    function testTrue() {
        $var = TRUE;
        $this->assertIdentical($this->U->unserialize(serialize($var),'php'),$var);
    }
    
    function testEmptyString() {
        $var = '';
        $this->assertIdentical($this->U->unserialize(serialize($var),'php'),$var);
    }
    
    function testZero() {
        $var = 0;
        $this->assertIdentical($this->U->unserialize(serialize($var),'php'),$var);
    }
    
    function testStringZero() {
        $var = '0';
        $this->assertIdentical($this->U->unserialize(serialize($var),'php'),$var);
    }
    
}

/**
* @package JPSpan
* @subpackage Tests
*/
class Unserializer_ClassParser_A {}

/**
* @package JPSpan
* @subpackage Tests
*/
class Unserializer_ClassParser_Bar {}

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Unserializer_PHP_getClasses extends UnitTestCase {

    function TestOfJPSpan_Unserializer_PHP_getClasses() {
        $this->UnitTestCase('TestOfJPSpan_Unserializer_PHP_getClasses');
    }
    
    function setUp() {
        $this->U = & new JPSpan_Unserializer_PHP();
    }
    
    function tearDown() {
        unset($this->U);
    }

    function testSingleClass() {
        $parsedClasses = array('Unserializer_ClassParser_A');
        $s = serialize(new Unserializer_ClassParser_A());
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }

    function testArraySingleClass() {
        $data = array(new Unserializer_ClassParser_A());
        $parsedClasses = array('Unserializer_ClassParser_A');
        $s = serialize($data);
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }

    function testArrayTwoClasses() {
        $data = array(
            new Unserializer_ClassParser_A(),
            new Unserializer_ClassParser_Bar()
            );
        $parsedClasses = array(
            'Unserializer_ClassParser_A',
            'Unserializer_ClassParser_Bar',
            );
        $s = serialize($data);
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }

    function testArrayThreeClasses() {
        $data = array(
            new Unserializer_ClassParser_A(),
            new stdClass(),
            new Unserializer_ClassParser_Bar()
            );
        $parsedClasses = array(
            'Unserializer_ClassParser_A',
            'stdClass',
            'Unserializer_ClassParser_Bar',
            );
        $s = serialize($data);
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }

    function testArrayThreeClassesWithString() {
        $data = array(
            new Unserializer_ClassParser_A(),
            'foo;O:bar',
            new stdClass(),
            new Unserializer_ClassParser_Bar()
            );
        $parsedClasses = array(
            'Unserializer_ClassParser_A',
            'stdClass',
            'Unserializer_ClassParser_Bar',
            );
        $s = serialize($data);
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }

    function testHashThreeClassesWithString() {
        $data = array(
            'foo;O:bar' => new Unserializer_ClassParser_A(),
            'a;O:b',
            new stdClass(),
            'c;O:d' => new Unserializer_ClassParser_Bar()
            );
        $parsedClasses = array(
            'Unserializer_ClassParser_A',
            'stdClass',
            'Unserializer_ClassParser_Bar',
            );
        $s = serialize($data);
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }
    
    function testString() {
        $s = serialize('foo;O:3:"Foo"');
        $this->assertEqual($this->U->getClasses($s),array());
    }
    
    function testStringNewLine() {
        $s = serialize("foo;\nO:3:\"Foo\"");
        $this->assertEqual($this->U->getClasses($s),array());
    }
    
    function testObjectWithStrings() {
        $obj = new Unserializer_ClassParser_A();
        $obj->x = 'foo;O:3:"Foo"';
        $obj->y = "foo;\nO:3:\"Foo\"";
        $s = serialize($obj);
        $parsedClasses = array('Unserializer_ClassParser_A');
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }
    
    function testArrayObjectWithStrings() {
        $obj = new Unserializer_ClassParser_A();
        $obj->x = 'foo;O:3:"Foo"';
        $obj->y = "foo;\nO:3:\"Foo\"";
        $data = array (
            $obj,
            'foos:3"Zee"'=>$obj,
            FALSE,
            NULL,
            TRUE,
            1
        );
        $s = serialize($data);
        $parsedClasses = array('Unserializer_ClassParser_A');
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }

    function testArrayStringLikeString() {
        $data = array (
            "foo;\nO:3:\"Foo\";\ns:3:\"bar\";",
            new Unserializer_ClassParser_A(),
            "foo;\nO:3:\"Foo\";\ns:3:\"bar\";" => new Unserializer_ClassParser_A(),
        );
        $parsedClasses = array('Unserializer_ClassParser_A');
        $s = serialize($data);
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }
    
    function testClassWithSerializedName() {
        $s = 'a:4:{i:0;O:26:"Unserializer_ClassParser_A":0:{}i:1;O:8:"O:3:"Foo"";}:0:{}i:2;s:24:"foo;O:3:"Foo";s:3:"bar";"i:4;O:28:"Unserializer_ClassParser_Bar":0:{}}';
        $parsedClasses = array(
            'Unserializer_ClassParser_A',
            'O:3:',
            'Unserializer_ClassParser_Bar'
            );
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }
    
    function testNestedObjects() {
        $obj = new Unserializer_ClassParser_A();
        $obj->x = new Unserializer_ClassParser_A();
        $obj->y = "O:4:\"Test\"";
        $obj->z = new Unserializer_ClassParser_Bar();
        $obj->z->a = new stdClass();
        $parsedClasses = array(
            0=>'Unserializer_ClassParser_A',
            2=>'Unserializer_ClassParser_Bar',
            3=>'stdClass'
            );
        $s = serialize($obj);
        $this->assertEqual(array_map('strtolower',$this->U->getClasses($s)),array_map('strtolower',$parsedClasses));
    }

}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = & new GroupTest('Unserializer_PHP Tests');
    $test->addTestCase(new TestOfJPSpan_Unserializer_PHP());
    $test->addTestCase(new TestOfJPSpan_Unserializer_PHP_getClasses());
    $test->run(new HtmlReporter());
}
?>
