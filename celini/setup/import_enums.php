<?php
/**
 * At the moment this is a simple implementation and only missing enums are created, if the enum exists we don't do anything even if the values are different
 */
require_once dirname(__FILE__) . '/../bootstrap.php';

if (isset($_GET['file'])) {
	$file = $_GET['file'];
}

// comment out code below to next marker to run over a browser
if (isset($_SERVER['HTTP_HOST'])) {
	die ("This script is meant to be run from the command line.");
}

if (!isset($argv[1])) {
	die ("syntax: php import_enums.php enumerations.xml\n");
}
else {
	$file = $argv[1];
}
// comment out to here

$loader->requireOnce('lib/PEAR/XML/Unserializer.php');

$us = new XML_Unserializer(array('parseAttributes'=>true,'attributesArray'=>'_attrs'));
$us->unserialize(file_get_contents($file));
$payload = $us->getUnserializedData();

$em =& Celini::enumManagerInstance();

if (is_array($payload['enumeration'])) {
}
else {
	$payload['enumeration'] = array($payload['enumeration']);
}

foreach($payload['enumeration'] as $enum) {

	if ($em->enumExists($enum['_attrs']['name'])) {
		// goto update code
	}
	else {
		if (isset($enum['enum'][0])) {
			$values = $enum['enum'];
		}
		else {
			$values = array($enum['enum']);
		}
		
		createNewEnum($enum['_attrs'],$values);
	}
}


function createNewEnum($enumDef,$values) {
	global $em;

	$ed = Celini::newOrdo('EnumerationDefinition',$enumDef['name'],'ByName');
	foreach($enumDef as $key => $val) {
		$ed->set($key,$val);
	}
	$ed->persist();

	$enum =& $em->enumList($enumDef['name']);
	$enum->type->editing = true;
	$enum->updateValues($values);
	$c = count($values);

	echo "Created a new enum: {$enumDef['name']} with $c values\n";
}
