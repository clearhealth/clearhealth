<?php
require_once dirname(__FILE__) . '/config.php';
$GLOBALS['loader']->requireOnce('includes/X12Browser.class.php');
$GLOBALS['loader']->requireOnce('includes/X12Objects.class.php');

class TestOf_X12Browser extends UnitTestCase
{
	function testBasicGetPath() {
		$childObject = new X12Block();
		$browsableChildObject = new X12BlockBrowser($childObject);
		$childObject->code = 'code';
		$tree = array(
			'parent' => array(
				'child' => array(
					$childObject
				)
			)
		);
		
		$browser = new X12Browser();
		$browser->setTree($tree);
		$treeBlock = $browser->getBlockByPath('/parent/child/code');
		$this->assertIsA($treeBlock, 'X12BlockBrowser');
		$this->assertEqual($treeBlock, $browsableChildObject);
	}
	
	function testGetPathAtTopLevel() {
		$childObject = new X12Block();
		$childObject->code = 'code';
		$browsableChildObject = new X12BlockBrowser($childObject);
		
		$tree = array(
			'code' => $childObject
		);
		
		$browser = new X12Browser();
		$browser->setTree($tree);
		$this->assertEqual($browser->getBlockByPath('/code'), $browsableChildObject);
	}
	
	function testFailedGetPath() {
		$this->expectError('/unable to find/i');
		$childObject = new X12Block();
		$browsableChildObject = new X12BlockBrowser($childObject);
		$childObject->code = 'code';
		$tree = array(
			'parent' => array(
				'child' => array(
					$childObject
				)
			)
		);
		
		$browser = new X12Browser();
		$browser->setTree($tree);
		$this->assertFalse($browser->getBlockByPath('/code'));
		$this->assertFalse($browser->getBlockByPath('/parent/child/unknownCode'));
		
		$this->expectError('/unable to find path/i');
		$this->assertFalse($browser->getBlockByPath('/parent/unknownChild/code'));
	}
	
	function testGetPathOnComplexTree() {
		$childObject = new X12Block();
		$childObject->code = 'code';
		$browsableChildObject = new X12BlockBrowser($childObject);
		
		$stepChildObject = new X12Block();
		$stepChildObject->code = 'step';
		
		$tree = array(
			'parent' => array(
				array(
					'child' => array(
						array(
							$childObject
						),
						array(
							$stepChildObject
						)
					)
				),
				array(
					'child' => array(
						array(
							$childObject
						),
						array(
							$stepChildObject
						)
					)
				),
			)
		);
		
		$browser = new X12Browser();
		$browser->setTree($tree);
		$treeBlock = $browser->getBlockByPath('/parent/child/code');
		$this->assertIsA($treeBlock, 'X12BlockBrowser');
		$this->assertEqual($treeBlock, $browsableChildObject);		
	}
}

if (!defined('X12_IMPORTER_GROUP_TEST')) {
	$test = new TestOf_X12Browser();
	require X12_PARSER_TEST_PATH . '/testRunner.php';
}

?>
