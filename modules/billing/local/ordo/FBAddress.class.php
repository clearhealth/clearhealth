<?php
/**
 * Object Relational Persistence Mapping Class for table: address
 *
 * @package	com.uversainc.freeb2
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: address
 *
 * @package	com.uversainc.freeb2
 */
class FBAddress extends ORDataObject {

	/**#@+
	 * Fields of table: address mapped to class members
	 */
	var $id			= '';
	var $external_id	= '';
	var $type		= '';
	var $name		= '';
	var $line1		= '';
	var $line2		= '';
	var $city		= '';
	var $state		= '';
	var $zip	= '';
	/**#@-*/
	var $_table = 'fbaddress';

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FBAddress($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
		$this->enumTable = $this->_table;
		$this->addMetaHints("hide",array("external_id","type","name"));
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Address with this
	 */
	function setup($id = 0,$external_id = false) {

		if ($external_id !== false) {
			$this->set('external_id',$external_id);
		}
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('address_id');
	}

	/**#@+
	 * Getters and Setters for Table: address
	 */
	
	/**
	 * Getter for Primary Key: address_id
	 */
	function get_address_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: address_id
	 */
	function set_address_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
	
	/**
	 * Set address  of person
	 *
	 */
	function set_address($array) {
		//echo "jerwerw";
		$this->populate_array($array);
	}

	function set_fbaddress($array) {
		$this->populate_array($array);
	}
	
	/**
	 * Getter for 1 line printable address format
	 */
	function get_print_address() {
		$address = $this->get("line1");
		if (strlen($this->get("line2")) > 0) {
			$address .= " " . $this->get("line2");
		}
		return $address;
	}
	
	function get_print_city_state_zip() {
		$city_state_zip = $this->get('city').', '.$this->get('state').' '.$this->get('zip');
		return $city_state_zip;
	}
	
	function get_print_complete_address() {
		$address = $this->get_print_address().', '.$this->get_print_city_state_zip();
		return $address;
	}
	
	function getTypeList() {
		$list = $this->_load_enum('tyoe',true);
		return array_flip($list);
	}

	function getStateList() {
		$list = $this->_load_enum('state',false);
		return array_flip($list);
	}
}
?>
