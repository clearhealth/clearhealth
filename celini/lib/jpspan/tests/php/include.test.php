<?php
/**
* @version $Id: include.test.php,v 1.1 2004/11/18 14:43:45 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'Include.php';

Mock::generate('JPSpan_Include_File','MockJPSpan_Include_File');

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Include_Parser extends UnitTestCase {

    function TestOfJPSpan_Include_Parser() {
        $this->UnitTestCase('TestOfJPSpan_Include_Parser');
    }
    
    function setUp() {
        $this->IF = & new MockJPSpan_Include_File($this);
        $this->IF->setReturnValue('script',TRUE);
        $this->IF->setReturnValue('declaration',TRUE);
        $this->IF->setReturnValue('inc',TRUE);
    }
    
    function tearDown() {
        unset($this->IF);
    }
    
    function testNoDeclaration() {
        $this->IF->expectOnce('script',array('Test',3));
        $P = & new JPSpan_Include_Parser($this->IF);
        $P->parse('Test');
        $this->IF->tally();
    }
    
    function testDeclaration() {
        $this->IF->expectCallCount('declaration',3);
        $this->IF->expectOnce('script');
        $P = & new JPSpan_Include_Parser($this->IF);
        $script = <<<EOD
/**@
*
*/
Test
EOD;
        $P->parse($script);
        $this->IF->tally();
    }
    
    
    function testInclude() {
        $this->IF->expectCallCount('inc',6);
        $P = & new JPSpan_Include_Parser($this->IF);
        $script = <<<EOD
/**@
* include 'foo.js';
* include 'bar.js';
*/
Test
EOD;
        $P->parse($script);
        $this->IF->tally();
    }

}

class TestOfJPSpan_Include_File extends UnitTestCase {
    function TestOfJPSpan_Include_File() {
        $this->UnitTestCase('TestOfJPSpan_Include_File');
    }
    
    function testScript() {
        $F = & new JPSpan_Include_File();
        $script = <<<EOD
/**@
* include 'foo.js';
* include 'bar.js';
*/
Test
EOD;
        $F->parse($script);
        $this->assertEqual($F->includes,array(JPSPAN . 'js/' . 'foo.js',JPSPAN . 'js/' . 'bar.js'));
        $this->assertWantedPattern('/Test/',$F->src);
    }
    
}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new GroupTest('Includes Tests');
    $test->addTestCase(new TestOfJPSpan_Include_Parser());
    $test->addTestCase(new TestOfJPSpan_Include_File());
    $test->run(new HtmlReporter());
}
?>
