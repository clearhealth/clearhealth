<?php
/**
* @version $Id: lexer.group.php,v 1.1 2004/11/10 16:03:10 harryf Exp $
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
class LexerGroupTest extends GroupTest {

    function LexerGroupTest() {
        $this->GroupTest('LexerGroupTest');
        $this->addTestFile('lexer.test.php');
    }
    
}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new LexerGroupTest();
    $test->run(new HtmlReporter());
}
?>
