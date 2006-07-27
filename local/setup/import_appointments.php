<?php
/**
 * An importer to handle upgrading all old (pre 1.0RC3) appointments to the new (1.0RC3) 
 * appointments.
*
* @access private
 */

// comment out if you want to run via the web
/* if (isset($_SERVER['HTTP_HOST'])) {
	die('Unauthorized access prohibited');
} */

// hide error message about starting sessions after output is generated
session_start();

echo "\nInitializing Celini...";
// initial Celini environment
define('APP_ROOT', realpath(dirname(__FILE__) . '/../../'));
define('CELINI_ROOT', APP_ROOT . '/celini');
define('CELLINI_ROOT', CELINI_ROOT);
require_once CELINI_ROOT . '/bootstrap.php';

$GLOBALS['oldCHDB'] = 'clearhealth_old';
$GLOBALS['newCHDB'] = 'clearhealth';
$db = new clniDB();
echo "done\n";

echo "Querying for old appointments...";
$oldAppointmentQuery = "
	SELECT 
		id, event_id, start, end, notes, location_id, user_id, last_change_id, 
		external_id, reason_code, timestamp, walkin, group_appointment
	FROM
		{$oldCHDB}.occurences
	WHERE
		external_id > 0";

$oldAppointments = $db->execute($oldAppointmentQuery);
echo "done\n";

echo "Found " . $oldAppointments->recordCount() . " old appointments\n";
echo "Converting into new format.";

$newAppointmentEntries = array();
$newEventEntries = array();

$counter = 0;
while ($oldAppointments && !$oldAppointments->EOF) {
	if (($counter % 100) == 0) {
		echo "$counter<br />";flush();
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
	$qEventId = $db->quote($oldAppointment['event_id']);
	$qProviderId = $db->quote($oldAppointment['user_id']);
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
	}
	
	$oldAppointments->moveNext();
}
echo "done.\n";

// insert appointments
$appointmentInsertValues = implode(', ', $newAppointmentEntries);
$appointmentInsertSql = "
	INSERT INTO
		{$oldCHDB}.appointment 
	(
		appointment_id, title, reason, walkin, group_appointment, created_date, last_change_id,
		creator_id, event_id, provider_id, patient_id, room_id, practice_id
	)
	VALUES
		{$appointmentInsertValues}";
echo "Inserting " . count($newAppointmentEntries) . " upgraded appointments...";
$db->execute($appointmentInsertSql);
echo "done\n";


// insert events
$eventInsertValues = implode(', ', $newEventEntries);
$eventInsertSql = "
	INSERT INTO
		{$oldCHDB}.event
	(
		event_id, title, start, end
	)
	VALUES
		{$eventInsertValues}";

echo "Inserting " . count($newEventEntries) . " upgraded events...";
$db->execute($eventInsertSql);
echo "done\n";


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

?>
