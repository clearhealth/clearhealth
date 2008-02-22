<?php
/**
 * ORDO that makes a company a clearing house
 *
 * @package	com.clear-health.freeb2
 */

$loader->requireOnce('ordo/FBCompany.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.clear-health.freeb2
 */
class FBClearingHouse extends FBCompany {

	var $type = "FBClearingHouse";

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
