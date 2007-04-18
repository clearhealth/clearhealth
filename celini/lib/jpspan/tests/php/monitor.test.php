<?php
/**
* @version $Id: monitor.test.php,v 1.1 2004/11/18 14:58:46 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'Monitor.php';

class TestMonitorObserver {

    var $S;
    var $E;
    
    function success($Data) {
        $this->S = $Data;
    }
    
    function error($Data) {
        $this->E = $Data;
    }
    
    function & instance() {
        static $I = NULL;
        if ( !$I ) {
            $I = new TestMonitorObserver();
        }
        return $I;
    }
}

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Monitor extends UnitTestCase {

    function TestOfJPSpan_Monitor() {
        $this->UnitTestCase('TestOfJPSpan_Monitor');
    }
    
    function setUp() {
        $M = & JPSpan_Monitor::instance(TRUE);
        $M->addObserver(TestMonitorObserver::instance());
    }
    
    function testSuccess() {
        $M = & JPSpan_Monitor::instance(TRUE);
        $M->setRequestInfo('class','Foo');
        $M->setRequestInfo('method','Bar');
        $M->announceSuccess();
        $O = & TestMonitorObserver::instance();
        $this->assertEqual($O->S['requestInfo']['class'],'Foo');
        $this->assertEqual($O->S['requestInfo']['method'],'Bar');
    }
    
    function testFail() {
        $M = & JPSpan_Monitor::instance(TRUE);
        $M->announceError('Foo',123,'Bar','file','line');
        $O = & TestMonitorObserver::instance();
        $this->assertEqual($O->E['errorName'],'Foo');
        $this->assertEqual($O->E['errorCode'],123);
        $this->assertEqual($O->E['errorMsg'],'Bar');
        $this->assertEqual($O->E['errorFile'],'file');
        $this->assertEqual($O->E['errorLine'],'line');
    }
    
    function testError() {
    
    }
}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfJPSpan_Monitor();
    $test->run(new HtmlReporter());
}
?>
