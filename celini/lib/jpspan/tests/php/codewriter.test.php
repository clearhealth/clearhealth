<?php
/**
* @version $Id: codewriter.test.php,v 1.2 2004/11/09 15:56:43 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'CodeWriter.php';

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_CodeWriter extends UnitTestCase {

    var $Code;

    function TestOfJPSpan_CodeWriter() {
        $this->UnitTestCase('TestOfJPSpan_CodeWriter');
    }
    
    function setUp() {
        $this->Code = & new JPSpan_CodeWriter();
    }
    
    function tearDown() {
        unset($this->Code);
    }

    function testWrite() {
        $this->Code->write('Foo');
        $this->assertEqual('Foo',$this->Code->toString());
    }
    
    function testWriteDisabled() {
        $this->Code->write('Foo');
        $this->Code->enabled = FALSE;
        $this->Code->write('Bar');
        $this->assertEqual('Foo',$this->Code->toString());
    }
    
    function testAppend() {
        $this->Code->append('Foo');
        $this->Code->append('Bar');
        $this->assertEqual('FooBar',$this->Code->toString());
    }
    
    function testAppendDisabled() {
        $this->Code->append('Foo');
        $this->Code->append('Bar');
        $this->Code->enabled = FALSE;
        $this->Code->append('Foo');
        $this->assertEqual('FooBar',$this->Code->toString());
    }

}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfJPSpan_CodeWriter();
    $test->run(new HtmlReporter());
}
?>
