<?php
/**
 * Creates new style EnumerationDefinitions out of the old Enumerations.
 *
 * This was made to run in the CLI but could easily be modified for use inside 
 * a web-based environment as part of an upgrade script.
 *
 * To run, use the following command:
 *
 * <code>
 *    php -d register_globals=Off -f setup/enum_upgrade.php
 * </code>
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
if (isset($_SERVER['HTTP_HOST'])) {
	die ("This script is meant to be run from the command line.");
}
require_once dirname(__FILE__) . '/../bootstrap.php';

$oldEnum =& Celini::newORDO('Enumeration');
$oldEnumList = $oldEnum->enumeration_factory();

foreach ($oldEnumList as $oldEnum) {
	$newEnum =& Celini::newORDO('EnumerationDefinition');
	$newEnum->set('name', $oldEnum->name);
	if (empty($oldEnum->title)) {
		$newEnum->set('title', ucwords(str_replace('_', ' ', $oldEnum->name)));
	}
	else {
		$newEnum->set('title', $oldEnum->title);
	}
	$newEnum->set('type', 'Default');
	$newEnum->persist();
	
	foreach ($oldEnum->enumeration as $oldEnumValue => $oldEnumKey) {
		$enumValue =& Celini::newORDO('EnumerationValue');
		$enumValue->set('enumeration_id', $newEnum->get('id'));
		$enumValue->set('value', $oldEnumValue);
		$enumValue->set('key', $oldEnumKey);
		$enumValue->persist();
	}
}
