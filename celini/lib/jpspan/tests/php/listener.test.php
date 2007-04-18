<?php
/**
* @version $Id: listener.test.php,v 1.2 2004/11/09 15:56:43 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'Listener.php';

/**
* Generate Mock responder
*/
Mock::Generate('JPSpan_NullResponder','MockResponder');

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Listener extends UnitTestCase {

    var $initial_request_method;
    var $initial_get = array();
    var $initial_post = array();
    var $initial_rawpost = '';
    var $Listener;
    var $Responder;
    
    function TestOfJPSpan_Listener() {
        $this->UnitTestCase('TestOfJPSpan_Listener');
        $this->initial_request_method = $_SERVER['REQUEST_METHOD'];
        $this->initial_get = $_GET;
        $this->initial_post = $_POST;
        global $HTTP_RAW_POST_DATA;
        $this->initial_rawpost = $HTTP_RAW_POST_DATA;
    }
    
    function setUp() {
        $this->Listener = & new JPSpan_Listener();
        $this->Responder = & new MockResponder($this);
        $this->Listener->setResponder($this->Responder);
    }
    
    function tearDown() {
        unset($this->Listener);
        unset($this->Responder);
        $_SERVER['REQUEST_METHOD'] = $this->initial_request_method;
        $_GET = $this->initial_get;
        $_POST = $this->initial_post;
        global $HTTP_RAW_POST_DATA;
        $HTTP_RAW_POST_DATA = $this->initial_rawpost;
    }

    function testGet() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = array();
        $_GET['foo'] = 'bar';
        $this->Responder->expectOnce('execute',array(array('foo'=>'bar')));
        $this->Listener->serve();
        $this->Responder->tally();
    }
    
    function testPost() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array();
        $_POST['foo'] = 'bar';
        $this->Responder->expectOnce('execute',array(array('foo'=>'bar')));
        $this->Listener->serve();
        $this->Responder->tally();
    }
    
    function testRawPost() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        global $HTTP_RAW_POST_DATA;
        $HTTP_RAW_POST_DATA = 'Hello World!';
        $this->Responder->expectOnce('execute',array('Hello World!'));
        $this->Listener->serve();
        $this->Responder->tally();
    }

}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfJPSpan_Listener();
    $test->run(new HtmlReporter());
}
?>
