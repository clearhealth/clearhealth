<?php
/**
 * ORDO that makes a company a practice
 *
 * @package	com.clear-health.freeb2
 */

$loader->requireOnce('ordo/FBCompany.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.clear-health.freeb2
 */
class FBPractice extends FBCompany {

	var $type = "FBPractice";

	/**
	 * Metadata for storage variables
	 *
	 * format is
	 *
	 * [type][key] = key
	 */
	var $storage_metadata = array(
		'int' => array(
			'practice_id' => ''
		), 
		'date' => array(),
		'string' => array('sender_id' => "",'receiver_id' => "",
			'x12_version' => "004010X098A1", //default for production and most systems, some system require 004010X098DA1 when testing
			"pos_code" => "110" //standard code for outpatient clinics
			)
	);
	
	function is($field_name,$char_to_return = "") {
	$field_name = strtolower($field_name);
	if($char_to_return == ""){
			$rc = false;
	}else{		$rc = true;}

		if($rc){
			return "X";
		}else{
			return(" ");
		}
	}
	
	function is_not($field_name, $char_to_return = "") {
		if($char_to_return == "") {
			$rc = false;
		}
		else{		
			$rc = true;
		}

		if($this->is($field_name)){
			if($rc){
				return " ";
			}else{
				return(false);
			}
		}
		else{
			if($rc){
				return $char_to_return;
			}else{
				return(true);
			}
			
		}	
	}

	
}
?>
