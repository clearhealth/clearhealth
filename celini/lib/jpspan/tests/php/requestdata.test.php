<?php
/**
* @version $Id: requestdata.test.php,v 1.3 2004/11/15 10:45:44 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'RequestData.php';

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_RequestData_RawPost extends UnitTestCase {

    var $initial_rawpost = '';
    
    function TestOfJPSpan_RequestData_RawPost() {
        $this->UnitTestCase('TestOfJPSpan_RequestData_RawPost');
        global $HTTP_RAW_POST_DATA;
        $this->initial_rawpost = $HTTP_RAW_POST_DATA;
    }
    
    function tearDown() {
        global $HTTP_RAW_POST_DATA;
        $HTTP_RAW_POST_DATA = $this->initial_rawpost;
    }

    function testFetch() {
        global $HTTP_RAW_POST_DATA;
        $HTTP_RAW_POST_DATA = 'foo';
        $this->assertEqual(JPSpan_RequestData_RawPost::fetch('php'),'foo');
    }
    
    function testFetchSerialized() {
        global $HTTP_RAW_POST_DATA;
        $HTTP_RAW_POST_DATA = serialize('foo');
        $this->assertEqual(JPSpan_RequestData_RawPost::fetch('php'),'foo');
    }

}

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_RequestData_Post extends UnitTestCase {

    var $initial_post = array();
    
    function TestOfJPSpan_RequestData_Post() {
        $this->UnitTestCase('TestOfJPSpan_RequestData_Post');
        $this->initial_post = $_POST;
    }
    
    function tearDown() {
        $_POST = $this->initial_post;
    }

    function testFetch() {
        $_POST['foo'] = 'bar';
        $this->assertEqual(JPSpan_RequestData_Post::fetch('php'),array('foo'=>'bar'));
    }
    
    function testFetchSerialized() {
        $_POST['foo'] = serialize('bar');
        $this->assertEqual(JPSpan_RequestData_Post::fetch('php'),array('foo'=>'bar'));
    }

}

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_RequestData_Get extends UnitTestCase {

    var $initial_get = array();
    
    function TestOfJPSpan_RequestData_Get() {
        $this->UnitTestCase('TestOfJPSpan_RequestData_Get');
        $this->initial_get = $_GET;
    }
    
    function tearDown() {
        $_GET = $this->initial_get;
    }

    function testFetch() {
        $_GET['foo'] = 'bar';
        $this->assertEqual(JPSpan_RequestData_GET::fetch('php'),array('foo'=>'bar'));
    }
    
    function testFetchSerialized() {
        $_GET['foo'] = serialize('bar');
        $this->assertEqual(JPSpan_RequestData_GET::fetch('php'),array('foo'=>'bar'));
    }

}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new GroupTest('JPSpan_RequestData Tests');
    $test->addTestCase(new TestOfJPSpan_RequestData_RawPost());
    $test->addTestCase(new TestOfJPSpan_RequestData_Post());
    $test->addTestCase(new TestOfJPSpan_RequestData_Get());
    $test->run(new HtmlReporter());
}
?>
