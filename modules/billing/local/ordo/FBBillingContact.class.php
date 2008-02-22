<?php
/**
 * ORDO that makes a person a billing contact
 *
 * @package	com.clear-health.freeb2
 */

$loader->requireOnce('ordo/FBCompany.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Company
 *
 * @package	com.clear-health.freeb2
 */
class FBBillingContact extends FBCompany {

	var $type = "FBBillingContact";

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
	
	var $name = 'Billing Administrator';
}
?>
