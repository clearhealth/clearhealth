<?php
require_once dirname(__FILE__) . '/config.php';
$GLOBALS['loader']->requireOnce('includes/X12TokenIterator.class.php');
$GLOBALS['loader']->requireOnce('includes/X12Tokenizer.class.php');

class TestOf_X12TokenIterator extends UnitTestCase
{
	function testBasicAPI() {
		$values = array('ISA', '*', '01', '*', '02', '~');
		
		$iterator = new X12TokenIterator($values);
		$this->assertTrue($iterator->valid());
		
		$i = 0;
		for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
			$this->assertEqual($values[$i], $iterator->current());
			$i++;
		}
		
		$this->assertFalse($iterator->valid());
		$iterator->rewind();
		$this->assertTrue($iterator->valid()); 
	}
	
	function testBasicViaTokenizer() {
		$values = array('ISA', '*', '01', '*', '02', '~');
		
		$reader = new MockX12Reader();
		$reader->setReturnValue('readContents', 'ISA*01*02~');
		
		$tokenizer = new X12Tokenizer();
		$tokenizer->setReader($reader);
		
		$iterator = $tokenizer->parse();
		$i = 0;
		for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
			$this->assertEqual($values[$i], $iterator->current());
			$i++;
		}
		
		$this->assertFalse($iterator->valid());
		$iterator->rewind();
		$this->assertTrue($iterator->valid());
	}
}

if (!defined('X12_IMPORTER_GROUP_TEST')) {
	$test = new TestOf_X12TokenIterator();
	require X12_PARSER_TEST_PATH . '/testRunner.php';
}

?>
