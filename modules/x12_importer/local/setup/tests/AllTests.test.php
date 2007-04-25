<?php
require_once dirname(__FILE__) . '/config.php';
define('X12_IMPORTER_GROUP_TEST', true);

$test = new GroupTest();
$collector = new SimplePatternCollector('/\/TestOf.*\.php$/');
$test->collect(X12_PARSER_TEST_PATH, $collector);

require_once X12_PARSER_TEST_PATH . '/testRunner.php';

?>

