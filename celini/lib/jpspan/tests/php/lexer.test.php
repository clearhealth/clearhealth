<?php
/**
* @version $Id: lexer.test.php,v 1.2 2004/12/10 23:32:35 harryf Exp $
* @package JPSpan
* @subpackage Tests
*/

/**
* Includes
*/
require_once('../config.php');
require_once JPSPAN . 'Lexer.php';
    
/**
* @package JPSpan
* @subpackage Tests
*/
class TestOfLexerParallelRegex extends UnitTestCase {
	function TestOfLexerParallelRegex() {
		$this->UnitTestCase();
	}
	function testNoPatterns() {
		$regex = &new JPSpan_LexerParallelRegex(false);
		$this->assertFalse($regex->match("Hello", $match));
		$this->assertEqual($match, "");
	}
	function testNoSubject() {
		$regex = &new JPSpan_LexerParallelRegex(false);
		$regex->addPattern(".*");
		$this->assertTrue($regex->match("", $match));
		$this->assertEqual($match, "");
	}
	function testMatchAll() {
		$regex = &new JPSpan_LexerParallelRegex(false);
		$regex->addPattern(".*");
		$this->assertTrue($regex->match("Hello", $match));
		$this->assertEqual($match, "Hello");
	}
	function testCaseSensitive() {
		$regex = &new JPSpan_LexerParallelRegex(true);
		$regex->addPattern("abc");
		$this->assertTrue($regex->match("abcdef", $match));
		$this->assertEqual($match, "abc");
		$this->assertTrue($regex->match("AAABCabcdef", $match));
		$this->assertEqual($match, "abc");
	}
	function testCaseInsensitive() {
		$regex = &new JPSpan_LexerParallelRegex(false);
		$regex->addPattern("abc");
		$this->assertTrue($regex->match("abcdef", $match));
		$this->assertEqual($match, "abc");
		$this->assertTrue($regex->match("AAABCabcdef", $match));
		$this->assertEqual($match, "ABC");
	}
	function testMatchMultiple() {
		$regex = &new JPSpan_LexerParallelRegex(true);
		$regex->addPattern("abc");
		$regex->addPattern("ABC");
		$this->assertTrue($regex->match("abcdef", $match));
		$this->assertEqual($match, "abc");
		$this->assertTrue($regex->match("AAABCabcdef", $match));
		$this->assertEqual($match, "ABC");
		$this->assertFalse($regex->match("Hello", $match));
	}
	function testPatternLabels() {
		$regex = &new JPSpan_LexerParallelRegex(false);
		$regex->addPattern("abc", "letter");
		$regex->addPattern("123", "number");
		$this->assertIdentical($regex->match("abcdef", $match), "letter");
		$this->assertEqual($match, "abc");
		$this->assertIdentical($regex->match("0123456789", $match), "number");
		$this->assertEqual($match, "123");
	}
}

/**
* @package JPSpan_TESTS
*/
class TestOfLexerStateStack extends UnitTestCase {
	function TestOfLexerStateStack() {
		$this->UnitTestCase();
	}
	function testStartState() {
		$stack = &new JPSpan_LexerStateStack("one");
		$this->assertEqual($stack->getCurrent(), "one");
	}
	function testExhaustion() {
		$stack = &new JPSpan_LexerStateStack("one");
		$this->assertFalse($stack->leave());
	}
	function testStateMoves() {
		$stack = &new JPSpan_LexerStateStack("one");
		$stack->enter("two");
		$this->assertEqual($stack->getCurrent(), "two");
		$stack->enter("three");
		$this->assertEqual($stack->getCurrent(), "three");
		$this->assertTrue($stack->leave());
		$this->assertEqual($stack->getCurrent(), "two");
		$stack->enter("third");
		$this->assertEqual($stack->getCurrent(), "third");
		$this->assertTrue($stack->leave());
		$this->assertTrue($stack->leave());
		$this->assertEqual($stack->getCurrent(), "one");
	}
}

class TestParser {
	function TestParser() {
	}
	function accept() {
	}
	function a() {
	}
	function b() {
	}
}
Mock::generate('TestParser');

class TestOfLexer extends UnitTestCase {
	function TestOfLexer() {
		$this->UnitTestCase();
	}
	function testNoPatterns() {
		$handler = &new MockTestParser($this);
		$handler->expectNever("accept");
		$handler->setReturnValue("accept", true);
		$lexer = &new JPSpan_Lexer($handler);
		$this->assertFalse($lexer->parse("abcdef"));
	}
	function testEmptyPage() {
		$handler = &new MockTestParser($this);
		$handler->expectNever("accept");
		$handler->setReturnValue("accept", true);
		$handler->expectNever("accept");
		$handler->setReturnValue("accept", true);
		$lexer = &new JPSpan_Lexer($handler);
		$lexer->addPattern("a+");
		$this->assertTrue($lexer->parse(""));
	}
	function testSinglePattern() {
		$handler = &new MockTestParser($this);
		$handler->expectArgumentsAt(0, "accept", array("aaa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(1, "accept", array("x", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(2, "accept", array("a", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(3, "accept", array("yyy", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(4, "accept", array("a", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(5, "accept", array("x", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(6, "accept", array("aaa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(7, "accept", array("z", JPSPAN_LEXER_UNMATCHED));
		$handler->expectCallCount("accept", 8);
		$handler->setReturnValue("accept", true);
		$lexer = &new JPSpan_Lexer($handler);
		$lexer->addPattern("a+");
		$this->assertTrue($lexer->parse("aaaxayyyaxaaaz"));
		$handler->tally();
	}
	function testMultiplePattern() {
		$handler = &new MockTestParser($this);
		$target = array("a", "b", "a", "bb", "x", "b", "a", "xxxxxx", "a", "x");
		for ($i = 0; $i < count($target); $i++) {
			$handler->expectArgumentsAt($i, "accept", array($target[$i], '*'));
		}
		$handler->expectCallCount("accept", count($target));
		$handler->setReturnValue("accept", true);
		$lexer = &new JPSpan_Lexer($handler);
		$lexer->addPattern("a+");
		$lexer->addPattern("b+");
		$this->assertTrue($lexer->parse("ababbxbaxxxxxxax"));
		$handler->tally();
	}
}

class TestOfLexerModes extends UnitTestCase {
	function TestOfLexerModes() {
		$this->UnitTestCase();
	}
	function testIsolatedPattern() {
		$handler = &new MockTestParser($this);
		$handler->expectArgumentsAt(0, "a", array("a", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(1, "a", array("b", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(2, "a", array("aa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(3, "a", array("bxb", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(4, "a", array("aaa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(5, "a", array("x", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(6, "a", array("aaaa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(7, "a", array("x", JPSPAN_LEXER_UNMATCHED));
		$handler->expectCallCount("a", 8);
		$handler->setReturnValue("a", true);
		$lexer = &new JPSpan_Lexer($handler, "a");
		$lexer->addPattern("a+", "a");
		$lexer->addPattern("b+", "b");
		$this->assertTrue($lexer->parse("abaabxbaaaxaaaax"));
		$handler->tally();
	}
	function testModeChange() {
		$handler = &new MockTestParser($this);
		$handler->expectArgumentsAt(0, "a", array("a", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(1, "a", array("b", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(2, "a", array("aa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(3, "a", array("b", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(4, "a", array("aaa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(0, "b", array(":", JPSPAN_LEXER_ENTER));
		$handler->expectArgumentsAt(1, "b", array("a", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(2, "b", array("b", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(3, "b", array("a", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(4, "b", array("bb", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(5, "b", array("a", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(6, "b", array("bbb", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(7, "b", array("a", JPSPAN_LEXER_UNMATCHED));
		$handler->expectCallCount("a", 5);
		$handler->expectCallCount("b", 8);
		$handler->setReturnValue("a", true);
		$handler->setReturnValue("b", true);
		$lexer = &new JPSpan_Lexer($handler, "a");
		$lexer->addPattern("a+", "a");
		$lexer->addEntryPattern(":", "a", "b");
		$lexer->addPattern("b+", "b");
		$this->assertTrue($lexer->parse("abaabaaa:ababbabbba"));
		$handler->tally();
	}
	function testNesting() {
		$handler = &new MockTestParser($this);
		$handler->setReturnValue("a", true);
		$handler->setReturnValue("b", true);
		$handler->expectArgumentsAt(0, "a", array("aa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(1, "a", array("b", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(2, "a", array("aa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(3, "a", array("b", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(0, "b", array("(", JPSPAN_LEXER_ENTER));
		$handler->expectArgumentsAt(1, "b", array("bb", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(2, "b", array("a", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(3, "b", array("bb", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(4, "b", array(")", JPSPAN_LEXER_EXIT));
		$handler->expectArgumentsAt(4, "a", array("aa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(5, "a", array("b", JPSPAN_LEXER_UNMATCHED));
		$handler->expectCallCount("a", 6);
		$handler->expectCallCount("b", 5);
		$lexer = &new JPSpan_Lexer($handler, "a");
		$lexer->addPattern("a+", "a");
		$lexer->addEntryPattern("(", "a", "b");
		$lexer->addPattern("b+", "b");
		$lexer->addExitPattern(")", "b");
		$this->assertTrue($lexer->parse("aabaab(bbabb)aab"));
		$handler->tally();
	}
	function testSingular() {
		$handler = &new MockTestParser($this);
		$handler->setReturnValue("a", true);
		$handler->setReturnValue("b", true);
		$handler->expectArgumentsAt(0, "a", array("aa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(1, "a", array("aa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(2, "a", array("xx", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(3, "a", array("xx", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(0, "b", array("b", JPSPAN_LEXER_SPECIAL));
		$handler->expectArgumentsAt(1, "b", array("bbb", JPSPAN_LEXER_SPECIAL));
		$handler->expectCallCount("a", 4);
		$handler->expectCallCount("b", 2);
		$lexer = &new JPSpan_Lexer($handler, "a");
		$lexer->addPattern("a+", "a");
		$lexer->addSpecialPattern("b+", "a", "b");
		$this->assertTrue($lexer->parse("aabaaxxbbbxx"));
		$handler->tally();
	}
	function testUnwindTooFar() {
		$handler = &new MockTestParser($this);
		$handler->setReturnValue("a", true);
		$handler->expectArgumentsAt(0, "a", array("aa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(1, "a", array(")", JPSPAN_LEXER_EXIT));
		$handler->expectCallCount("a", 2);
		$lexer = &new JPSpan_Lexer($handler, "a");
		$lexer->addPattern("a+", "a");
		$lexer->addExitPattern(")", "a");
		$this->assertFalse($lexer->parse("aa)aa"));
		$handler->tally();
	}
}

class TestOfLexerHandlers extends UnitTestCase {
	function TestOfLexerHandlers() {
		$this->UnitTestCase();
	}
	function testModeMapping() {
		$handler = &new MockTestParser($this);
		$handler->setReturnValue("a", true);
		$handler->expectArgumentsAt(0, "a", array("aa", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(1, "a", array("(", JPSPAN_LEXER_ENTER));
		$handler->expectArgumentsAt(2, "a", array("bb", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(3, "a", array("a", JPSPAN_LEXER_UNMATCHED));
		$handler->expectArgumentsAt(4, "a", array("bb", JPSPAN_LEXER_MATCHED));
		$handler->expectArgumentsAt(5, "a", array(")", JPSPAN_LEXER_EXIT));
		$handler->expectArgumentsAt(6, "a", array("b", JPSPAN_LEXER_UNMATCHED));
		$handler->expectCallCount("a", 7);
		$lexer = &new JPSpan_Lexer($handler, "mode_a");
		$lexer->addPattern("a+", "mode_a");
		$lexer->addEntryPattern("(", "mode_a", "mode_b");
		$lexer->addPattern("b+", "mode_b");
		$lexer->addExitPattern(")", "mode_b");
		$lexer->mapHandler("mode_a", "a");
		$lexer->mapHandler("mode_b", "a");
		$this->assertTrue($lexer->parse("aa(bbabb)b"));
		$handler->tally();
	}
}

/**
* Conditional test runner
*/
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = & new GroupTest('UnserializerGroupTest');
    $test->addTestCase(new TestOfLexerParallelRegex());
    $test->addTestCase(new TestOfLexerStateStack());
    $test->addTestCase(new TestOfLexer());
    $test->addTestCase(new TestOfLexerModes());
    $test->addTestCase(new TestOfLexerHandlers());
    $test->run(new HtmlReporter());
}
?>