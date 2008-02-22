<?php
/**
 * ORDO that makes a person a supervising provider
 *
 * @package	com.clear-health.freeb2
 */

$loader->requireOnce('ordo/FBPerson.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.clear-health.freeb2
 */
class FBSupervisingProvider extends FBPerson {

	var $type = "FBSupervisingProvider";
	
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
		'string' => array( 'identifier_2' => '', 'identifier_type_2' => '')
	);

	function FBSupervisingProvider($db = null){
		parent::FBPerson($db);
		$this->addMetaHints("hide",array("gender","date_of_birth"));
	
	}


	
}
?>
