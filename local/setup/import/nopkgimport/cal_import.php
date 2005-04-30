<?php

include("config.php");
require_once("Controller.class.php");
require_once("Me.class.php");

$me = new Me();
$_SESSION['frame']['me'] = serialize($me);


require_once("Event.class.php");
require_once("Occurence.class.php");
$db = $GLOBALS['frame']['adodb']['db'];

$sql = "SELECT id, username from `op-en-hcs`.users";
$users = array();
$result = $db->Execute($sql);

while($result && !$result->EOF) {

	$users[$result->fields['username']] = $result->fields['id'];
	$result->moveNext();
}

$sql = "SELECT  pc_catname, ev.pc_catid, pd.id, pc_informant, DATE_FORMAT(pc_eventDate,'%m/%d/%Y') as pc_eventDate, pc_title, pc_startTime, pc_endTime, 
username
FROM  ".$GLOBALS['frame']['config']['openemr_db'].".`openemr_postcalendar_events` as ev
LEFT JOIN ".$GLOBALS['frame']['config']['openemr_db'].".openemr_postcalendar_categories as cat on cat.pc_catid = ev.pc_catid
LEFT JOIN ".$GLOBALS['frame']['config']['openemr_db'].".users as u on u.id = ev.pc_aid
LEFT JOIN ".$GLOBALS['frame']['config']['openemr_db'].".patient_data as pd on pd.pid = ev.pc_pid
WHERE pc_recurrtype =0 and (ev.pc_catid = 9 OR ev.pc_catid =10 OR ev.pc_catid =11) and ev.pc_eventstatus = 1";
echo $sql . "\n\n";
echo $db->ErrorMsg();
//exit;
$result = $db->Execute($sql);

while ($result && !$result->EOF) {
	$e = new Event();
	$oc = new Occurence();
	$oc->set_external_id($result->fields['id']);
	$oc->set_date($result->fields['pc_eventDate']);
	$oc->set_start_time($result->fields['pc_startTime']);	
	$oc->set_end_time($result->fields['pc_endTime']);	
	$oc->set_notes($result->fields['pc_title']);
	if ($users[$result->fields['username']] > 0) {
		$oc->set_user_id($users[$result->fields['username']]);
		if ($result->fields['pc_catname'] == "MM Visit") {
			$oc->set_location_id("1628");
		}
		elseif($result->fields['pc_catname'] == "NC Visit"){
			$oc->set_location_id("1629");
		}
		elseif($result->fields['pc_catname'] == "UWC Visit") {
			$oc->set_location_id("1630");
		}
		$e->persist();
		$oc->set_event_id($e->get_id());
		$oc->persist();
	}
//	echo $oc->toString();	
	$result->MoveNext();
}
?>
