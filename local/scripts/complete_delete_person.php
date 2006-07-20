<?php
die('Comment out this die line to use');
require_once '../../celini/bootstrap.php';

$db = new clniDb();

if (!isset($argv[1])) {
	die('Usage: php complete_delete_person.php id');
}
$id = $argv[1];

delete('storage_int','foreign_key',$id,false);
delete('storage_date','foreign_key',$id,false);
delete('storage_string','foreign_key',$id,false);
delete('storage_text','foreign_key',$id,false);

$aId = deleteRetId('person_address','person_id',$id,'address_id',false);
delete('address','address_id',$aId);

$nId = deleteRetId('person_number','person_id',$id,'number_id');
delete('number','number_id',$nId);

delete('person_person','person_id',$id);
delete('patient_note','patient_id',$id);
delete('patient_statistics','person_id',$id);
delete('patient_chronic_code','patient_id',$id);
delete('person_type','person_id',$id);
delete('secondary_practice','person_id',$id);

$eId = deleteRetId('encounter','patient_id',$id,'encounter_id');
delete('encounter_date','encounter_id',$id);
delete('encounter_person','encounter_id',$id);
delete('encounter_value','encounter_id',$id);

$cId = deleteRetId('clearhealth_claim','encounter_id',$id,'identifier');
delete('fblatest_revision','claim_identifier',$cId);
$fcId = deleteRetIdList('fbclaim','claim_identifier',$cId,'claim_id');
foreach($fcId as $fc) {
	$lines = deleteRedIdList('fbclaimline','claim_id',$fc,'claimline_id');
	foreach($lines as $line) {
		delete('fbdiagnoses','claimline_id',$line);
	}

	delete('fbcompany','claim_id',$fc);
	delete('fbperson','claim_id',$fc);
	delete('fbpractice','claim_id',$fc);

}

$payIds = deleteRetIdList('payment','encounter_id',$eId,'payment_id');
foreach($payIds as $payId) {
	delete('payment_claimline','payment_id',$payId);
}


$eventIds = deleteRetIdList('appointment','patient_id',$id,'event_id');
foreach($eventIds as $eventId) {
	delete('event','event_id',$eventId);
}

delete('person','person_id',$id);

function delete($table,$key,$id,$primary = null) {
	global $db;

	if (is_null($primary)) {
		$primary = $table.'_id';
	}

	if ($primary !== false) {
		$tmp = (int)$db->getOne("select $primary from $table where $key = $id");
		$sql = "delete from ordo_registry where ordo_id = $tmp";
		$db->execute($sql);
	}

	$sql = "delete from $table where $key = $id";
	$db->execute($sql);
}

function deleteRetId($table,$key,$id,$ret,$reg = null) {
	global $db;

	$ret = (int)$db->getOne("select $ret from $table where $key = $id");

	delete($table,$key,$id,$reg);

	return $ret;
}
function deleteRetIdList($table,$key,$id,$ret) {
	global $db;

	$ret = $db->getCol("select $ret from $table where $key = $id");

	delete($table,$key,$id);

	return $ret;
}
?>
