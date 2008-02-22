<?php
/**
 * ORDO that makes a company a trating location
 *
 * @package	com.clear-health.freeb2
 */

$loader->requireOnce('ordo/FBCompany.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.clear-health.freeb2
 */
class FBTreatingFacility extends FBCompany {
	
	var $type = "FBTreatingFacility";
	
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
		'string' => array('facility_code' => "11")
	);
	
}
?>
