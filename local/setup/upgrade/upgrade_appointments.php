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

debug("Querying for old appointments...", false);
$oldAppointmentQuery = "
	SELECT 
		id, event_id, start, end, notes, location_id, user.person_id provider_id, last_change_id, 
		external_id, reason_code, timestamp, walkin, group_appointment
	FROM
		{$oldCHDB}.occurences
		inner join user on occurences.user_id = user.user_id
	WHERE
		external_id > 0";

$oldAppointments = $db->execute($oldAppointmentQuery);
debug("done!");

debug("Found " . $oldAppointments->recordCount() . " old appointments\n");
debug("Converting into new format.", false);

$newAppointmentEntries = array();
$newEventEntries = array();


$counter = 0;
$showCounterAt = $oldAppointments->recordCount() / 10;
$tmp = array();
while ($oldAppointments && !$oldAppointments->EOF) {
	if ($counter % $showCounterAt == 0) {
		debug(".", false);
	}
	$counter++;
	
	$oldAppointment = $oldAppointments->fields;
	
	// setup Appointment insert data
	$qAppointmentId = $db->quote($oldAppointment['id']);
	$qTitle = $db->quote($oldAppointment['notes']);
	$qReason = $db->quote($oldAppointment['reason_code']);
	$qWalkin = $db->quote($oldAppointment['walkin']);
	$qGroupAppointment = $db->quote($oldAppointment['group_appointment']);
	$qCreatedDate = $db->quote($oldAppointment['timestamp']);
	$qLastChangeId = $db->quote($oldAppointment['last_change_id']);
	$qCreatorId = $qLastChangeId;
	$qEventId = (int)$oldAppointment['event_id'];
	$qProviderId = $db->quote($oldAppointment['provider_id']);
	$qPatientId = $db->quote($oldAppointment['external_id']);
	$qRoomId = $db->quote($oldAppointment['location_id']);
	$qPracticeId = $db->quote(getPracticeIdByRoomId($oldAppointment['location_id']));
	
	$newAppointmentEntries[] = "
		(
			{$qAppointmentId}, {$qTitle}, {$qReason}, {$qWalkin}, {$qGroupAppointment}, 
			{$qCreatedDate}, {$qLastChangeId}, {$qCreatorId}, {$qEventId}, {$qProviderId}, 
			{$qPatientId}, {$qRoomId}, {$qPracticeId}
		)";
	
		
	// setup Event insert data
	if (!isset($newEventEntries[$oldAppointment['event_id']])) {
		// title and event_id already setup
		$qStart = $db->quote($oldAppointment['start']);
		$qEnd = $db->quote($oldAppointment['end']);
		
		$newEventEntries[$oldAppointment['event_id']] = "({$qEventId}, {$qTitle}, {$qStart}, {$qEnd})";
		if (isset($tmp[$qEventId])) {
			var_dump("({$qEventId}, {$qTitle}, {$qStart}, {$qEnd})");
		}
		$tmp[$qEventId] = $qEventId;
	}
	
	$oldAppointments->moveNext();
}
debug("done.");
//var_dump(count($tmp));
//var_dump(count($newEventEntries));


// insert appointments
$appointmentInsertValues = implode(', ', $newAppointmentEntries);
$appointmentInsertSql = "
	INSERT INTO
		{$newCHDB}.appointment 
	(
		appointment_id, title, reason, walkin, group_appointment, created_date, last_change_id,
		creator_id, event_id, provider_id, patient_id, room_id, practice_id
	)
	VALUES
		{$appointmentInsertValues}";
debug("Inserting " . count($newAppointmentEntries) . " upgraded appointments...", false);
//var_dump(strlen($appointmentInsertSql));
$db->execute($appointmentInsertSql);
debug("done");


// insert events
$eventInsertValues = implode(",\n\t\t", $newEventEntries);
$eventInsertSql = "
	INSERT INTO
		{$newCHDB}.event
	(
		event_id, title, start, end
	)
	VALUES
		{$eventInsertValues}";

debug("Inserting " . count($newEventEntries) . " upgraded events...", false);
//$db->execute($eventInsertSql);
echo $eventInsertSql;
debug("done");

echo "Successfully upgraded " . count($newAppointmentEntries) . " appointments\n";


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