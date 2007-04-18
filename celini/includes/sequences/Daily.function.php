<?php

/**
 * Returns a sequence that is reset nightly.
 *
 * Should never be called directly, see {@link clniDB::customNextId()}
 *
 * @see    clniDB::customNextId()
 * @return int
 */
function Sequences_Daily() {
	$db =& new clniDB();
	$sql = 'SELECT updated_on FROM sequences_daily';
	$lastUpdated = $db->getOne($sql);
	
	$currentDate = date('Y-m-d');
	if ($lastUpdated != $currentDate) {
		$updateSql = '1';
	}
	else {
		$updateSql = 'counter + 1';
	}
	
	$sql = "UPDATE sequences_daily SET counter = {$updateSql}, updated_on = " . $db->quote($currentDate);
	$db->execute($sql);
	
	return $db->getOne('SELECT counter FROM sequences_daily');
}
