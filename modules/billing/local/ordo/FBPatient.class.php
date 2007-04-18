<?php
/**
 * ORDO that makes a person a patient
 *
 * @package	com.uversainc.freeb2
 */

$loader->requireOnce('ordo/FBPerson.class.php');

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.uversainc.freeb2
 */
class FBPatient extends FBPerson {

	/**
	 * Metadata for storage variables
	 *
	 * format is
	 *
	 * [type][key] = key
	 */
	var $storage_metadata = array(
		'int' => array(), 
		'date' => array("date_of_death" => "",
				"date_last_seen" => "", 
				"date_of_onset" => "",
				"date_of_initial_treatment" => "",
				"date_of_cant_work_start" => "",
				"date_of_cant_work_end" => "", 
				"date_of_hospitalization_start" => "", 
				"date_of_hospitalization_end" => ""),
		'string' => array("marital_status" => "single",
				"employment_status" => "" ,
				"weight" => "",
				"comment_type" => "ADD",
				"pregnant" => "no")
	);
	var $type = "FBPatient";


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
					}				}
			break;	

			case "pregnant":
				if ((strtolower($this->get("pregnant"))=="yes")||
				(strtolower($this->get("pregnant"))=="y")){
					if ($this->is("female")){//just checking...
						if($rc){
							return $char_to_return;
						}else{
							return(true);
						}
					}
				}
			break;	


			case "single":
				if (strtolower($this->get("marital_status"))=="single"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;

			case "married":
				if (strtolower($this->get("marital_status"))=="married"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;

			case "stat_other":
				if (strtolower($this->get("marital_status"))=="other"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;

			case "employed":
				if (strtolower($this->get("employment_status"))=="employed"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;


			case "ftstudent":
				if ((strtolower($this->get("employment_status"))=="ftstudent")||
				    (strtolower($this->get("employment_status"))=="full time student")||
				    (strtolower($this->get("employment_status"))=="fulltime student")||
				    (strtolower($this->get("employment_status"))=="student")||
				    (strtolower($this->get("employment_status"))=="full-time student"))
					{
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;

			case "ptstudent":
				if ((strtolower($this->get("employment_status"))=="ptstudent")||
				    (strtolower($this->get("employment_status"))=="part time student")||
				    (strtolower($this->get("employment_status"))=="parttime student")||
				    (strtolower($this->get("employment_status"))=="part-time student"))
					{
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;

			case "dead":
				if (strtolower($this->get("date_of_death"))!=""){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;

			case "employed":
				if (strtolower($this->get("employment_status"))=="employed"){
					if($rc){
						return $char_to_return;
					}else{
						return(true);
					}
				}
			break;
			default: 
					if($rc){
						return " ";
					}else{
						return(false);
					}
		
		}//switch


		if($rc){
			return " ";
		}else{
			return(false);
		}	

	}
	
}
?>
