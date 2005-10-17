<?php
// fixes record # for patients who have a record id of 0
// all are queried and replaced with the next record # sequence

include "../../Celini/bootstrap.php";


$db = Celini::dbInstance();

$res = $db->execute("select person_id from patient where record_number = 0");

$count = 0;
while($res && !$res->EOF) {
	unset($p);

	$p =& ORDataObject::Factory('Patient',$res->fields['person_id']);

	$p->set('record_number',$p->generate_record_number());
	$p->persist();

	$count++;
	echo "Updated: ".$p->get('person_id')."\n";
	flush();

	$res->MoveNext();
}

echo "\n\n$count Patient's Updated\n";
?>
