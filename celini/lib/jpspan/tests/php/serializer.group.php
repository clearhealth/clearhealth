<?php
/**
* @version $Id: serializer.group.php,v 1.2 2004/11/09 15:56:43 harryf Exp $
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
class SerializerGroupTest extends GroupTest {

    function SerializerGroupTest() {
        $this->GroupTest('SerializerGroupTest');
        $this->addTestFile('serializer.test.php');
    }
    
}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new SerializerGroupTest();
    $test->run(new HtmlReporter());
}
?>
