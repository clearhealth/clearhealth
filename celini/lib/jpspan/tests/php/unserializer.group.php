<?php
/**
* @version $Id: unserializer.group.php,v 1.4 2004/11/15 10:37:37 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Init
*/
require_once('../config.php');

/**
* @package JPSpan
* @subpackage Tests
*/
class UnserializerGroupTest extends GroupTest {

    function UnserializerGroupTest() {
        $this->GroupTest('UnserializerGroupTest');
        $this->addTestFile('unserializer.test.php');
        $this->addTestFile('unserializer_php.test.php');
        $this->addTestFile('unserializer_xml.test.php');
    }
    
}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new UnserializerGroupTest();
    $test->run(new HtmlReporter());
}
?>
