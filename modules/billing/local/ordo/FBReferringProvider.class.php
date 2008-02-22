<?php
/**
 * ORDO that makes a person a referring provider
 *
 * @package	com.clear-health.freeb2
 */

$loader->requireOnce('ordo/FBPerson.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.clear-health.freeb2
 */
class FBReferringProvider extends FBPerson {

	var $type = "FBReferringProvider";

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
		'string' => array("taxonomy_code" => "", "referral_type" => "DN",
				'identifier_2' => '', 'identifier_type_2' => 'Default') //DN is referring provider, P3 is primary care physician
	);

	function FBReferringProvider($db = null){
		parent::FBPerson($db);
		$this->addMetaHints("hide",array("gender","date_of_birth"));
	
	}

	
	
	
}
?>
