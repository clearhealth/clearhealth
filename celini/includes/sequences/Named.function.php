<?php

/**
 * Returns a sequence from a named sequence
 *
 * Should never be called directly, see {@link clniDB::customNextId()}
 *
 * @see    clniDB::customNextId()
 * @return int
 *
 * @todo Add table locking to remove possible race issues
 */
function Sequences_Named($name) {
	$db =& new clniDB(); 
	$qName = $db->quote($name);
	// create if it doesn't exist
	$result = $db->execute("SELECT counter FROM sequences_named WHERE name = {$qName} LIMIT 1");
	if (count($result->fields) <= 0) {
		$db->execute("INSERT INTO sequences_named (name, counter) VALUES ({$qName}, 0)");
	}
	$db->execute("UPDATE sequences_named SET counter = counter + 1 WHERE name = {$qName} LIMIT 1");
	$result = $db->execute("SELECT counter FROM sequences_named WHERE name = {$qName}");
	return $result->fields['counter'];
}
