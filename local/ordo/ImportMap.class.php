<?php
/**
 * Object Relational Persistence Mapping Class for table: import_map
 *
 * @package	com.uversainc.freestand
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: import_map
 *
 * @package	com.uversainc.freestand
 */
class ImportMap extends ORDataObject {

	/**#@+
	 * Fields of table: import_map mapped to class members
	 */
	var $id		= '';
	var $new_id		= '';
	var $old_table_name		= '';
	var $new_object_name		= '';
	/**#@-*/
	var $_table = 'import_map';
	var $_internalName='ImportMap';


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function ImportMap($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
		//	echo "in constructor \n";
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Import_map with this
	 */
	function setup($id = 0,$old_table_name = "") {
		if ($id > 0) {
			$this->set('id',$id);
			$this->set('old_table_name',$old_table_name);
		//	echo "in setup \n";
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate($id = "id") {
		$sql = "SELECT * from " . $this->_prefix  . $this->_table . " WHERE old_id = '" . mysql_real_escape_string(strval($this->id))  . "' AND old_table_name ='" . mysql_real_escape_string(strval($this->old_table_name))."'";
			//echo "in populate with $sql\n";
		$results = $this->_execute($sql);
		if ($results && !$results->EOF) {
			foreach ($results->fields as $field_name => $field) {
				$this->set($field_name,$field);
				//echo 'field_name '.$field_name.' field'.$field."\n";
			}
			if (is_a($this->_int_storage,'Storage')) {
				$this->_int_storage->foreign_key = $this->id;
				$this->_int_storage->populate();
			}

			if (is_a($this->_date_storage,'Storage')) {
				$this->_date_storage->foreign_key = $this->id;
				$this->_date_storage->populate();
			}
			
			if (is_a($this->_string_storage,'Storage')) {
				$this->_string_storage->foreign_key = $this->id;
				$this->_string_storage->populate();
			}
			$this->_populateMetaData($results);
			$this->_populated = true;
		}
	}



	/**#@+
	 * Getters and Setters for Table: import_map
	 */

	
	/**
	 * Getter for Primary Key: old_id
	 */
	function get_old_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: old_id
	 */
	function set_old_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
