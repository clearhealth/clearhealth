<?php
/**
 * ORDO that makes a person a subscriber
 *
 * @package	com.clear-health.freeb2
 */

$loader->requireOnce('ordo/FBPerson.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.clear-health.freeb2
 */
class FBSubscriber extends FBPerson {

	var $type = "FBSubscriber";

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
		'string' => array("relationship" => "","group_number" => "","group_name" => "")
	);
	
	function get_relationship_code() {
		if (strtolower($this->get("relationship")) === "self") {
			return "18"; //value for when patient and subscriber are the same	
		}
		return ""; //if they are not the same nothing should be returned
	}
/*
	function bis($field_name){ // boolean is
		if($this->is($field_name)!=" "){// then is returned something so it is true!!
			return(true);
		}else{ // then it returned blank so it is false!!
			return(false);
		}
	}

	function bis_not($field_name){ // boolean is_not
		return(!$this->bis($field_name));// return the opposite of whatever bis returns
	}
*/

	function is($field_name,$char_to_return = "") {
	$field_name = strtolower($field_name);
	if($char_to_return == ""){
			$rc = false;
	}else{		$rc = true;}
	
		switch($field_name){


			case "male":
				if (strtolower($this->get("gender"))=="m"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;	

			case "female":
				if (strtolower($this->get("gender"))=="f"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;	


			case "ins_self":
				if (strtolower($this->get("relationship"))=="self"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;	

			case "ins_spouse":
				if (strtolower($this->get("relationship"))=="spouse"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;	


			case "ins_child":
				if (strtolower($this->get("relationship"))=="child"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;

			case "ins_other":
				if (strtolower($this->get("relationship"))=="other"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;
			case "accepts_assignment":
					return $char_to_return;
			break;
			default:
					if($rc){
						return " ";
					}else{
						return(false);
					}


		}
	}

	function is_not($field_name,$char_to_return = "") {
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

	
}
?>
