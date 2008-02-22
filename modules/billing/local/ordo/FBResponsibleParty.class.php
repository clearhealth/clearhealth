<?php
/**
 * ORDO that makes a person a responsible party
 *
 * @package	com.clear-health.freeb2
 */

$loader->requireOnce('ordo/FBPerson.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.clear-health.freeb2
 */
class FBResponsibleParty extends FBPerson {

	var $type = "FBResponsibleParty";

	/**
	 * Metadata for storage variables
	 *
	 * format is
	 *
	 * [type][key] = key
	 */
	var $storage_metadata = array(
		'int' => array(), 
		'date' => array(),
		'string' => array()
	);
	
}
?>
