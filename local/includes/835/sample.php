<?php
/**
 * Example of using the parser on 2 sample files
 *
 * @package	com.clear-health.x12
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

require_once 'includes/X12Objects.class.php';
require_once 'includes/X12Util.class.php';
require_once 'includes/FileReader.class.php';
require_once 'includes/X12MapParser.class.php';
require_once 'includes/X12Tokenizer.class.php';

error_reporting(E_ALL);
$parser = new X12MapParser();

if (isset($_GET['debug'])) {
	$parser->debug = $_GET['debug'];
}
$parser->loadMap('maps/835.map.php');
$parser->loadInput(new FileReader('samples/sample1.x12'));
$parser->parse();

echo "<h1>Sample 1</h1>";
X12Util::printTree($parser->getTree());

$parser = new X12MapParser();
$parser->loadMap('maps/835.map.php');
$parser->loadInput(new FileReader('samples/sample2.x12'));
$parser->parse();

echo "<h1>Sample 2</h1>";
X12Util::printTree($parser->getTree());
?>
