<?php
/**
 * An importer to handle upgrading all old (pre 1.0RC3) appointments to the new (1.0RC3) 
 * appointments.
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

debug("Querying for old schedules...", false);
$sql = "
	SELECT 
		s.*,o.*,s.id AS schedule_id,oldu.person_id
	FROM
		{$oldCHDB}.schedules s
		left join {$oldCHDB}.occurences o using(user_id)
		left join {$oldCHDB}.user AS oldu ON (s.user_id=oldu.user_id)
	WHERE
		o.external_id = 0";

$res = $db->execute($sql);
debug("done!");

debug("Found " . $oldAppointments->recordCount() . " old schedule events\n");
debug("Converting into new format.", false);

$scheds = array();
$sqls = array();
$eventInsertValues = array();
// Get the max event_id to build from
$sql = "SELECT MAX(event_id) AS event_id FROM {$newCHDB}.event";
$idres = $db->execute($sql);
$event_id = $idres->fields['event_id'];
if($event_id < 1) {
	$event_id = 1;
}
while($res && !$res->EOF) {
	if(!isset($scheds[$res->fields['schedule_id']])) {
		// Create the schedule
		$sql = "
		INSERT INTO {$newCHDB}.schedule
		(schedule_id,title,description_long,description_short,schedule_code,provider_id)
		VALUES({$res->fields['schedule_id']},".$db->quote($res->fields['name']).",".
		$db->quote($res->fields['description_long']).",".$db->quote($res->fields['description_short']).",".
		$db->quote($res->fields['schedule_code']).",".$res->fields['person_id'].")";
		$db->execute($sql);
		// Create a default event group
		$sql = "
		INSERT INTO {$newCHDB}.event_group
		(event_group_id,title,room_id,schedule_id)
		VALUES({$res->fields['schedule_id']},".$db->quote($res->fields['name']).",{$res->fields['room_id']},{$res->fields['schedule_id']})";
		$db->execute($sql);
		$scheds[$res->fields['schedule_id']] = true;
		$currentSched = $res->fields['schedule_id'];
	}
	$eventInsertValues[] = "(".$event_id.','.$db->quote($res->fields['name']).','.$db->quote($res->fields['start']).','.$db->quote($res->fields['end']).')';
	$sevents[] = "({$res->fields['schedule_id']},{$event_id})";
	$event_id++;
	if(count($eventInsertValues) > 50) {
		insertSchedEvents($eventInsertValues,$sevents);
		$eventInsertValues = array();
		$sevents = array();
	}
	$res->MoveNext();
}

if(count($eventInsertValues) > 0) {
	insertSchedEvents($eventInsertValues,$sevents);
}

function insertSchedEvents(&$sqls,&$sevents) {
	global $db;
	global $newCHDB;
	$eventInsertValues = implode(',',$sqls);
	$eventInsertSql = "
	INSERT INTO
		{$newCHDB}.event
	(
		event_id, title, start, end
	)
	VALUES
		{$eventInsertValues}";

	$db->execute($eventInsertSql);
	$sqls = array();
	$sevInsertSql = "
	INSERT INTO
	{$newCHDB}.schedule_event (event_group_id,event_id)
	VALUES ".implode(',',$sevents);
	$db->execute($sevInsertSql);
	$sevents = array();
}

debug("done");



/**
 * Grabs the practice id based on a room id
 *
 * @param int
 * @access private
 */
function getPracticeIdByRoomId($roomId) {
	static $roomCache = array();
	global $oldCHDB;
	
	if (!isset($roomCache[$roomId])) {
		$db = new clniDB();
		$qRoomId = $db->quote($roomId);
		$sql = "
			SELECT 
				p.id
			FROM
				{$oldCHDB}.practices AS p
				INNER JOIN {$oldCHDB}.buildings AS b ON(p.id = b.practice_id)
				INNER JOIN {$oldCHDB}.rooms AS r ON(b.id = r.building_id)
			WHERE
				r.id = {$qRoomId}";
		$roomCache[$roomId] = $db->getOne($sql);
	}
	
	return $roomCache[$roomId];
}


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