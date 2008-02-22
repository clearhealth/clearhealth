<?php
/**
 * ORDO that makes a person a provider
 *
 * @package	com.clear-health.freeb2
 */

$loader->requireOnce('ordo/FBPerson.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.clear-health.freeb2
 */
class FBProvider extends FBPerson {
	
	var $type = "FBProvider";

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
		'string' => array('signature_on_file' => "Y",'accepts_assignment' => "A",
			'identifier_2' => '', 'identifier_type_2' => 'Default')
	);

	function FBProvider($db = null){
		parent::FBPerson($db);
		$this->addMetaHints("hide",array("gender","date_of_birth"));
	
	}
	
	function is($field_name,$char_to_return = "") {
	$field_name = strtolower($field_name);
	if($char_to_return == ""){
			$rc = false;
	}else{		$rc = true;}

		if($rc){
			return "X";
		}else{
			return(true);
		}
	}
	
	function is_not($field_name, $char_to_return = "") {
	if($char_to_return == ""){
			$rc = false;
	}else{		$rc = true;}

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
	
	
	/**
	 * Mutator for {@link $identifier_type_2}
	 *
	 * @param string|int
	 */
	function set_identifier_type_2($value) {
		$this->_string_storage->set('identifier_type_2', $value);
	}
	
	
	/**
	 * Determines the payer identifier type according to the X12 spec
	 *
	 * @param mixed
	 * @access protected
	 *
	 * @todo Add full support for all identifier_types
	 * @todo Refractor all of this code - these values should be stored as part of an enumeration
	 */
	function _determineIDType($value) {
		//echo "I'm trying to determine $value<br />";
		switch (strtolower($value)) {	
			case 'bcbs' :
				return '1B';
				
			case 'medicare' :
				return "1C";
			
			case 'medical'  :
			case 'medicaid' :
				return "1D";
			
			case 'ssn' :
				return "34";
	
			case 'ein' :
				return "24";
	
			default :
				return $value;
		}
	}
}
?>
