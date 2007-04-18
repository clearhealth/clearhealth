<?php
/**
* @version $Id: handle.test.php,v 1.2 2004/11/09 15:56:43 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'Handle.php';

/**
* @package JPSpan
* @subpackage Tests
*/
class UnaffectedObject {
    var $testVar = 'default';
    
    function UnaffectedObject() {}
    
    function methodX() {}
    
    function methodY() {}
    
    function _methodZ() {}
    
}

/**
* @package JPSpan
* @subpackage Tests
*/
class DeclaredInSameFile {
    var $testVar = 'default';
    function DeclaredInSameFile($Var = 'constructionDefault') {
        $this->testVar = $Var;
    }
    
    function methodX() {}
    
    function methodY() {}
    
    function _methodZ() {}
}

/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfJPSpan_Handle extends UnitTestCase {

    function TestOfJPSpan_Handle() {
        $this->UnitTestCase('TestOfJPSpan_Handle');
    }
    
    function testNullHandle() {
        $Handle = NULL;
        JPSpan_Handle::resolve($Handle);
        $this->assertNull($Handle);
    }
    
    function testObjectUnaffected() {
        $Handle =& new UnaffectedObject();
        $Obj =& $Handle;
        $Obj->testVar = 'Changed';
        JPSpan_Handle::resolve($Handle);
        $this->assertIsA($Handle, 'UnaffectedObject');
        $this->assertIdentical($Handle, $Obj);
        $this->assertEqual($Handle->testVar, 'Changed');
    }
    
    function testClassDeclaredInSameFile() {
        $Handle = 'DeclaredInSameFile';
        JPSpan_Handle::resolve($Handle);
        $this->assertIsA($Handle, 'DeclaredInSameFile');
    }
    
    function testLoadClassFile() {
        $this->assertFalse(class_exists('LoadedHandleClass'));
        $Handle = dirname(__FILE__) . '/handle.inc.php|LoadedHandleClass';
        JPSpan_Handle::resolve($Handle);
        $this->assertIsA($Handle, 'LoadedHandleClass');
        $this->assertTrue(class_exists('LoadedHandleClass'));
    }
    
    function testConstructor() {
        $Handle = array('DeclaredInSameFile', 'ConstructionParameter');
        JPSpan_Handle::resolve($Handle);
        $this->assertIsA($Handle, 'DeclaredInSameFile');
        $this->assertEqual($Handle->testVar, 'ConstructionParameter');
    }

    function testExamineObjectUnaffected() {
        $Handle =& new UnaffectedObject();
        $Description = JPSpan_Handle::examine($Handle);
        $this->assertEqual($Description->Class,strtolower(get_class($Handle)));
        $methods = array('methodx','methody');
        $this->assertEqual($Description->methods,$methods);
    }
    
    function testExamineClassDeclaredInSameFile() {
        $Handle = 'DeclaredInSameFile';
        $Description = JPSpan_Handle::examine($Handle);
        $this->assertEqual($Description->Class, strtolower($Handle));
        $methods = array('methodx','methody');
        $this->assertEqual($Description->methods,$methods);
    }
    
    function testExamineLoadClassFile() {
        $Handle = dirname(__FILE__) . '/handle.inc.php|LoadedHandleClass';
        $Description = JPSpan_Handle::examine($Handle);
        $this->assertEqual($Description->Class, strtolower('LoadedHandleClass'));
        $methods = array('methodx','methody');
        $this->assertEqual($Description->methods,$methods);
    }
    
}


/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfJPSpan_Handle();
    $test->run(new HtmlReporter());
}
?>
