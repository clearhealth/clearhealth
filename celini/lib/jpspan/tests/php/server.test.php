<?php
/**
* @version $Id: server.test.php,v 1.4 2005/04/20 14:48:45 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'Server.php';

/**
* @package JPSpan
* @subpackage Tests
*/
class JPSpan_TestHandler {
    function test() {
        return 'test';
    }
}

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Server extends UnitTestCase {

    var $server_name;
    var $script_name;
    var $path_info;
    var $request_uri;

    function TestOfJPSpan_Server() {
        $this->UnitTestCase('TestOfJPSpan_Server');
        $this->server_name = $_SERVER['SERVER_NAME'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
        if ( isset($_SERVER['PATH_INFO']) )  {
            $this->path_info = $_SERVER['PATH_INFO'];
        }
        $this->request_uri = $_SERVER['REQUEST_URI'];
    }
    
    function tearDown() {
        $_SERVER['SERVER_NAME'] = $this->server_name;
        $_SERVER['SCRIPT_NAME'] = $this->script_name;
        if ( isset($_SERVER['PATH_INFO']) )  {
            $_SERVER['PATH_INFO'] = $this->path_info;
        }
        $_SERVER['REQUEST_URI'] = $this->request_uri;
    }

    function testServerUrl() {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        if ( isset($_SERVER['PATH_INFO']) )  {
            $_SERVER['PATH_INFO'] = '/index.php';
        }
        $S = & new JPSpan_Server();
        $this->assertEqual($S->getServerUrl(),'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
    }
    
    function testSetServerUrl() {
        $S = & new JPSpan_Server();
        $S->setServerUrl('foo');
        $this->assertEqual($S->getServerUrl(),'foo');
    }

    function testAddHandler() {
        $S = & new JPSpan_Server();
        $H = 'JPSpan_TestHandler';
        $S->addHandler($H);
        $D = $S->getDescription($H);
        $this->assertEqual($D->Class,strtolower($H));
        $this->assertEqual($D->methods,array('test'));
    }
    
    function testAddHandlerWithDescription() {
        $S = & new JPSpan_Server();
        $H = 'JPSpan_TestHandler';
        $D = new stdClass();
        $D->Class = $H;
        $D->methods = array('Test');
        $S->addHandler($H,$D);
        $D = $S->getDescription(strtolower($H));
        $this->assertEqual($D->Class,strtolower($H));
        $this->assertEqual($D->methods,array('test'));
    }
    
    function testInvalidHandle() {
        $S = & new JPSpan_Server();
        $H = 'doesnotexist';
        $S->addHandler($H);
        $this->assertErrorPattern('/Invalid handle/');
    }
    
    function testInvalidDescription() {
        $S = & new JPSpan_Server();
        $H = 'JPSpan_TestHandler';
        $D = new stdClass();
        $S->addHandler($H,$D);
        $this->assertErrorPattern('/Invalid description/');
    }
    
    function testGetUriPath() {
        $S = & new JPSpan_Server();
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/index.php/foo/bar/';
        $this->assertEqual($S->getUriPath(),'foo/bar');
    }
    
    function testGetUriPath1() {
        $S = & new JPSpan_Server();
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/index.php/foo/?bar=1/x/y';
        $this->assertEqual($S->getUriPath(),'foo');
    }
    
    function testGetUriPath2() {
        $S = & new JPSpan_Server();
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/index.php/';
        $this->assertEqual($S->getUriPath(),'');
    }
    
    function testGetUriPath3() {
        $S = & new JPSpan_Server();
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/index.php?/foo/bar/';
        $this->assertEqual($S->getUriPath(),'');
    }
    
    function testGetHandler() {
        $S = & new JPSpan_Server();
        $H = 'JPSpan_TestHandler';
        $S->addHandler($H);
        $this->assertEqual($S->getHandler('JPSpan_TestHandler'),new JPSpan_TestHandler());
    }
}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfJPSpan_Server();
    $test->run(new HtmlReporter());
}
?>
