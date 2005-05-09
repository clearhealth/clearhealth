<?php
/**
 * Object Relational Persistence Mapping Class for table: provider
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/MergeDecorator.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: provider
 *
 * @package	com.uversainc.clearhealth
 */
class Provider extends MergeDecorator {

	/**#@+
	 * Fields of table: provider mapped to class members
	 */
	var $id				= '';
	var $state_license_number	= '';
	var $clia_number		= '';
	var $dea_number			= '';
	var $bill_as			= '';
	var $report_as			= '';
	/**#@-*/

	var $patient;


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Provider($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'provider';
		$this->_sequence_name = 'sequences';	
		$this->merge('person',ORDataObject::factory('Person'));
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Provider with this
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
		parent::populate('person_id');
		$this->mergePopulate('person_id');
	}

	/**
	 * Persist the data
	 */
	function persist() {
		$this->mergePersist('person_id');
		if ($this->get('id') == 0) {
			$this->set('id',$this->person->get('id'));
		}
		parent::persist();
	}

	/**#@+
	 * Getters and Setters for Table: provider
	 */

	
	/**
	 * Getter for Primary Key: person_id
	 */
	function get_person_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: person_id
	 */
	function set_person_id($id)  {
		$this->id = $id;
	}

	function get_bill_as() {
		if (empty($this->bill_as)) {
			$this->set('bill_as',$this->get('id'));
		}
		return $this->bill_as;
	}

	/**#@-*/

	function toArray() {
		$ret = $this->person->toArray();
		$ret['identifier'] = $this->get('state_license_number');

		return $ret;
	}

	function getProviderList() {
		$res = $this->_execute("select p.person_id, concat_ws(', ',last_name,first_name) name from user u inner join person p using(person_id) order by name");
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['person_id']] = $res->fields['name'];
			$res->moveNext();
		}
		return $ret;
	}
}
?>
