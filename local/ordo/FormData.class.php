<?php
/**
 * Object Relational Persistence Mapping Class for table: form_data
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
$loader->requireOnce('includes/Datasource_sql.class.php');
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: form_data
 *
 * @package	com.uversainc.clearhealth
 */
class FormData extends ORDataObject {

	/**#@+
	 * Fields of table: form_data mapped to class members
	 */
	var $id			= '';
	var $form_id		= '';
	var $external_id	= '';
	var $last_edit		= '';
	/**#@-*/

	var $_table = 'form_data';
	var $_internalName='FormData';

	/**
	 * 
	 * Primary key.
	 * 
	 * @var string
	 * 
	 */
    var $_key = 'form_data_id';
    
	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function FormData($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Form_data with this
	 */
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('form_data_id');
	}

	/**
	 * Get a ds with data about all forms
	 */
	function &dataList($form_id) {
		settype($form_id,'int');
		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "last_edit, form_id, form_data_id, external_id",
				'from' 	=> "$this->_table d",
				'orderby' => 'last_edit DESC',
				'where' => "form_id = $form_id"
			),
			array('last_edit'=>'Time of Last Edit'));
		return $ds;
	}

	/**
	 * Get a ds with data for an external_id
	 */
	function &dataListByExternalId($external_id) {
		assert('$external_id == intval($external_id)');
		$external_id = intval($external_id);
		
		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols'    => "last_edit, f.name, form_data_id, external_id",
				'from'    => "$this->_table d inner join form f using(form_id)",
				'orderby' => 'name, last_edit DESC',
				'where'   => "external_id = $external_id"
			),
			array('name' => 'Form Name','last_edit'=>'Last Edit'));
		return $ds;
	}
	
	
	/**
	 * Returns a {@link Datasource_sql} setup to retrieve form data for a 
	 * patient.
	 *
	 * This differs from {@link dataListByExternalId()} in that it also searches
	 * for matching encounters to link display as part of the list.
	 *
	 * @return {@link Datasource_sql}
	 */
	function &dataListForPatientByExternalId($external_id) {
		assert('$external_id == intval($external_id)');
		$external_id = intval($external_id);
		
		$ds =& new Datasource_sql();
		$ds->setup($this->_db,
			array(
				'cols'    => "last_edit, f.name, form_data_id, external_id",
				'from'    => "{$this->_table} AS d
				             INNER JOIN form AS f USING(form_id)
							LEFT JOIN encounter AS e ON(d.external_id = e.encounter_id)",
				'orderby' => 'name, last_edit DESC',
				'where'   => "external_id = {$external_id} || e.patient_id = {$external_id}"
			),
			array('name' => 'Form Name','last_edit'=>'Last Edit'));
		return $ds;		
	}


	/**#@+
	 * Getters and Setters for Table: form_data
	 */

	
	/**
	 * Getter for Primary Key: form_data_id
	 */
	function get_form_data_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: form_data_id
	 */
	function set_form_data_id($id)  {
		$this->id = $id;
	}

	function get_form_name() {
		$f =& ORDataObject::factory('Form',$this->get('form_id'));
		return $f->get('name');
	}
	/**#@-*/

	/**
	 * Get an array with all the storage data, with type
	 */
	function allData() {
		$ret = array();

		foreach($this->_int_storage->_values as $key => $value) {
			$ret[$key] = array('name'=>$key,'value'=>$value,'type'=>'Integer');
		}
		foreach($this->_string_storage->_values as $key => $value) {
			$ret[$key] = array('name'=>$key,'value'=>$value,'type'=>'String');
		}
		foreach($this->_date_storage->_values as $key => $value) {
			$ret[$key] = array('name'=>$key,'value'=>$value,'type'=>'Date');
		}
		foreach($this->_text_storage->_values as $key => $value) {
			$ret[$key] = array('name'=>$key,'value'=>$value,'type'=>'Text');
		}

		uksort($ret,array(&$this,'_sort'));
		return $ret;

	}

	function _sort($a,$b) {
		return strcmp($a['name'],$b['name']);
	}
}
?>
