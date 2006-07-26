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

// initial Celini environment
define('APP_ROOT', realpath(dirname(__FILE__) . '/../../'));
define('CELINI_ROOT', APP_ROOT . '/celini');
define('CELLINI_ROOT', CELINI_ROOT);
require_once CELINI_ROOT . '/bootstrap.php';

$oldCHDB = 'clearhealth_old';
$newCHDB = 'clearhealth';


$db = new clniDB();
$oldAppointmentQuery = "
	SELECT id,
		event_id, start, end, notes, location_id, user_id, last_change_id, 
		external_id, reason_code, timestamp, walkin, group_appointment
	FROM
		{$oldCHDB}.occurences
	WHERE
		external_id > 0";

$oldAppointments = $db->execute($oldAppointmentQuery);
$newAppointmentEntries = array();

while ($oldAppointments && !$oldAppointments->EOF) {
	echo "Found one\n";
	$oldAppointment = $oldAppointments->fields;
	
	// store an array of all of the inserts
	$oldAppointments->moveNext();
}

// genreate insert SQL

?>
