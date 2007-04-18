<?php
/**
* @version $Id: server.group.php,v 1.2 2004/11/09 15:56:43 harryf Exp $
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
class ServerGroupTest extends GroupTest {

    function ServerGroupTest() {
        $this->GroupTest('ServerGroupTest');
        $this->addTestFile('server.test.php');
        $this->addTestFile('postoffice.test.php');
    }
    
}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new ServerGroupTest();
    $test->run(new HtmlReporter());
}
?>
