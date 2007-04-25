<?php
require_once dirname(__FILE__) . '/config.php';
$GLOBALS['loader']->requireOnce('includes/X12Tokenizer.class.php');

class TestOf_X12Tokenizer extends UnitTestCase
{
	function testBasicAPI() {
		$reader = new MockX12Reader();
		$reader->expectOnce('readContents');
		$reader->setReturnValue('readContents', 'ISA*01*02~');
		
		$tokenizer = new X12Tokenizer();
		$tokenizer->setReader($reader);
		$result = $tokenizer->parse();
		$this->assertIsA($result, 'X12TokenIterator');
	}
}

if (!defined('X12_IMPORTER_GROUP_TEST')) {
	$test = new TestOf_X12Tokenizer();
	require X12_PARSER_TEST_PATH . '/testRunner.php';
}

?>
