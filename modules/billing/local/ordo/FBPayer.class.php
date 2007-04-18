<?php
/**
 * ORDO that makes a company a payer
 *
 * @package	com.uversainc.freeb2
 */

$loader->requireOnce('ordo/FBCompany.class.php');


/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.uversainc.freeb2
 */
class FBPayer extends FBCompany {


	var $type = "FBPayer";
	var $identifier_type = "46";	
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
			"responsibility" => "P", 
			"claim_filing_code" => "10",
			'program_name' => '',
			'payer_type' => 'private'
		)
	);
	
	function is($field_name,$char_to_return = "") {
		$field_name = strtolower($field_name);
		$rc = ($char_to_return == "") ? false : true;
		
		if (strtolower($field_name) == strtolower($this->get('payer_type'))) {
			return $rc ? $char_to_return : true;
		}
	}

	function is_not($field_name, $char_to_return = "") {
		$rc = $char_to_return != "";

		if($this->is($field_name)){
			return $rc ? " " : false;
		}
		else {
			return $rc ? $char_to_return : true;
			
		}	
	}// end is_not
}
?>
