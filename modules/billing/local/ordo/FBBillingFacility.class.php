<?php
/**
 * ORDO that makes a company a billing location
 *
 * @package	com.clear-health.freeb2
 */

$loader->requireOnce('ordo/FBCompany.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.clear-health.freeb2
 */
class FBBillingFacility extends FBCompany {

	var $type = "FBBillingFacility";

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
		'string' => array(
			'clia_number' => ''
		)
	);
	
}
?>
