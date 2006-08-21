<?php
/**
 * Upgrades users and patients to RC3
 *
 * @access private
 */

// comment out if you want to run via the web
if (isset($_SERVER['HTTP_HOST'])) {
	die('Unauthorized access prohibited');
}

// setup php environment to use a lot of ram and run forever
set_time_limit(0);
ini_set('memory_limit','1024M');

// hide error message about starting sessions after output is generated
session_start();

include('config.php');


debug("\nInitializing Celini...", false);
// initial Celini environment
define('APP_ROOT', realpath(dirname(__FILE__) . '/../../../'));
define('CELINI_ROOT', APP_ROOT . '/celini');
define('CELLINI_ROOT', CELINI_ROOT);
require_once CELINI_ROOT . '/bootstrap.php';

$db = new clniDB();
debug("done!");
$oldCHDB = $GLOBALS['oldCHDB'];
debug("Querying Users...",false);

// Get primary_practice_id based on default location
$sql = "
	SELECT
		u.person_id,
		b.practice_id
	FROM
		{$newCHDB}.user u
		INNER JOIN {$newCHDB}.rooms r ON (u.default_location_id=r.id)
		INNER JOIN {$newCHDB}.buildings b ON (r.building_id=b.id)
	WHERE
		u.person_id > 0";
//debug($sql);
$oldres = $db->execute($sql);

debug("done!");
debug("Found " . $oldres->recordCount() . " users\n");
debug("Setting primary practice for users...");

while($oldres && !$oldres->EOF) {
	$sql = "
		UPDATE {$newCHDB}.person
		SET primary_practice_id = {$oldres->fields['practice_id']}
		WHERE person_id = {$oldres->fields['person_id']}";
	$db->execute($sql);
	$oldres->MoveNext();
}
debug("done!");

debug("Finding secondary practices...");
$sql = "
	SELECT
		a.patient_id,
		a.practice_id
	FROM
		{$newCHDB}.appointment a
		INNER JOIN {$newCHDB}.person p ON a.provider_id=p.person_id
	WHERE
		a.practice_id != p.primary_practice_id
	GROUP BY
		a.practice_id
	";
$oldres = $db->execute($sql);
debug("Setting secondary practices...");
$i=1;

while($oldres && !$oldres->EOF) {
	$sql = "
	INSERT INTO {$newCHDB}.secondary_practice (secondary_practice_id,person_id,practice_id)
	VALUES({$i},{$oldres->fields['patient_id']},{$oldres->fields['practice_id']})
	";
	$db->execute($sql);
	$i++;
	$oldres->MoveNext();
}
debug('Done!');

debug('Updating primary practice for patients...');
$sql = "
UPDATE 
	{$newCHDB}.person p
	,{$newCHDB}.appointment a
SET 
	p.primary_practice_id = a.practice_id 
WHERE 
	a.patient_id=p.person_id
";
$db->execute($sql);

// Now again only using encounter (in case there never was an appointment)
$sql = "
UPDATE {$newCHDB}.person p 
	,{$newCHDB}.encounter e
	,{$newCHDB}.buildings b
SET
	p.primary_practice_id=b.practice_id
WHERE
	e.patient_id=p.person_id AND
	e.building_id=b.id AND
	p.primary_practice_id = 0
";

$db->execute($sql);
debug('Done!');


/**
 * Outputs debugging code if debugging is turned on.
 *
 * @param string
 * @access private
 */
function debug($string, $lineEnd = true) {
	if ($GLOBALS['debug']) {
		echo $string;
		if ($lineEnd) {
			echo $GLOBALS['eol-style'];
		}
	}
}

?>
