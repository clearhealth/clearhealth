<?php

require_once CELLINI_ROOT . "/ordo/ORDataObject.class.php";
require_once APP_ROOT . "/local/ordo/Event.class.php";

/**
 * 
 */
 
class Patient extends ORDataObject{
	
	/**
	 *	
	 *	@var $id
	 */
	 var $id;

	/**
	 *	
	 *	@var firstname
	 */
	var $firstname;
	
	/**
	 *	
	 *	@var lastname
	 */
	var $lastname;
	
	/**
	 *	
	 *	@var dob
	 */
	var $dob;
	
	/**
	 *	
	 *	@var record_number
	 */
	var $record_number;
	
	/**
	 *	
	 *	@var patient_number
	 */
	var $patient_number;
	
	/**
	 *	
	 *	@var phone
	 */
	var $phone;
	
	
	/**
	 * Constructor sets all attributes to their default value
	 *  
	 */
	function Patient($id = "")	{
		//call the parent constructor so we have a _db to work with
		parent::ORDataObject();

		$this->id = $id;

		$this->firstname = "";
		$this->lastname = "";
		$this->dob = "";
		$this->patient_number = "";
		$this->phone = array();
	
		$this->_table = $GLOBALS['config']['openemr_db'].".patient_data";
		$this->_prefix = "";
		
		if ($id != "") {
			$this->populate();
		}
	}
	
	function persist() {
		parent::persist();
	}
	
	/**
	 * Convenience function to generate string debug data about the object
	 */
	function toString($html = false) {
		$string .= "\n"
		. "ID: " . $this->id."\n"
		."firstname:" . $this->firstname."\n"
		."lastname:" . $this->lastname."\n"
		."dob:" . $this->dob."\n"
		."patient_number:" . $this->patient_number."\n"
		."phone:" . $this->phone."\n"
		. "\n";
		if ($html) {
			return nl2br($string);
		}
		else {
			return $string;
		}
	}

	/**#@+
	*	Getter/Setter methods used by reflection to affect object in persist/poulate operations
	*	@param mixed new value for given attribute
	*/
	function set_id($id) {
		$this->id = $id;
	}
	function get_id() {
		return $this->id;
	}
	
	function set_firstname($value) {
		$this->firstname = $value;
	}
	function get_firstname() {
		return $this->firstname;
	}
	function set_fname($value) {
		$this->firstname = $value;
	}
	
	function set_lastname($value) {
		$this->lastname = $value;
	}
	function get_lastname() {
		return $this->lastname;
	}
	function set_lname($value) {
		$this->lastname = $value;
	}
	
	function set_dob($value) {
		$this->dob = $value;
	}
	function get_dob() {
		return $this->dob;
	}
	function set_record_number($value) {
		$this->record_number = $value;
	}
	function get_record_number() {
		return $this->record_number;
	}
	function set_pid($value) {
		$this->record_number = $value;
	}
	
	function set_patient_number($value) {
		$this->patient_number = $value;
	}
	function get_patient_number() {
		return $this->patient_number;
	}
	function set_pubpid($value) {
		$this->patient_number = $value;
	}
	
	function set_phone($value) {
		$this->phone = $value;
	}
	function get_phone() {
		return $this->phone;
	}
	function set_phone_home($value) {
		$this->phone = $value;
	}
	function get_age() {
		$age = floor(abs((date("Ymd",strtotime($this->get_dob())) - date("Ymd")) / 10000)); 
		return $age; 	
	}
	
} // end of Class

?>
