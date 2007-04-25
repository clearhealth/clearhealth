<?php

define('SIMPLETEST_PATH', 'simpletest');
define('X12_PARSER_PATH', realpath(dirname(__FILE__) . '/../../')); 
define('X12_PARSER_TEST_PATH', dirname(__FILE__));
define('APP_ROOT', X12_PARSER_PATH . '/../../..');
define('CELINI_ROOT', APP_ROOT . '/celini');

require_once SIMPLETEST_PATH . '/unit_tester.php';
require_once SIMPLETEST_PATH . '/reporter.php';
require_once SIMPLETEST_PATH . '/ui/colortext_reporter.php';
require_once SIMPLETEST_PATH . '/mock_objects.php';
require_once SIMPLETEST_PATH . '/collector.php';

require_once CELINI_ROOT . '/includes/FileFinder.class.php';
require_once CELINI_ROOT . '/includes/FileLoader.class.php';

$finder =& new FileFinder();
$finder->addPath(X12_PARSER_PATH);
$GLOBALS['loader'] = new FileLoader($finder);

// Generic mock generation
$GLOBALS['loader']->requireOnce('includes/X12Reader.abstract.php');
Mock::generatePartial('X12Reader', 'MockX12Reader', array('readContents'));

?>
