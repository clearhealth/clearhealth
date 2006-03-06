<?php
/**
 * Object Relational Persistence Mapping Class for table: group_occurence
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: group_occurence
 *
 * @package	com.uversainc.clearhealth
 */
class GroupOccurence extends ORDataObject {

	/**#@+
	 * Fields of table: group_occurence mapped to class members
	 */
	var $id			= '';
	var $occurence_id	= '';
	var $patient_id		= '';
	/**#@-*/

	var $_table = 'group_occurence';

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function GroupOccurence($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Group_occurence with this
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
		parent::populate('group_occurence_id');
	}

	/**#@+
	 * Getters and Setters for Table: group_occurence
	 */

	
	/**
	 * Getter for Primary Key: group_occurence_id
	 */
	function get_group_occurence_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: group_occurence_id
	 */
	function set_group_occurence_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	function quickAdd($occurence_id,$patient_id) {
		settype($occurence_id,'int');
		settype($patient_id,'int');
		$sql = "select group_occurence_id from  $this->_table where occurence_id = $occurence_id and patient_id = $patient_id";
		$res = $this->_execute($sql);
		if ($res && !$res->EOF) {
			return;
		}
		$this->set('id',0);
		$this->set('occurence_id',$occurence_id);
		$this->set('patient_id',$patient_id);
		$this->persist();
	}

	function quickDrop($occurence_id,$patient_id) {
		settype($occurence_id,'int');
		settype($patient_id,'int');

		$sql = "delete from  $this->_table where occurence_id = $occurence_id and patient_id = $patient_id";
		$res = $this->_Execute($sql);
	}

	function getPatientList($occurence_id) {
		settype($occurence_id,'int');
		$sql = "select patient_id id, concat(last_name, ', ', first_name, ' ', middle_name, ' #',record_number) name 
				from $this->_table go 
				inner join person p on p.person_id = go.patient_id
				inner join patient pt on pt.person_id = go.patient_id
				where go.occurence_id = $occurence_id";
		$res = $this->_execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[] = $res->fields;
			$res->MoveNext();
		}
		return $ret;
	}
}
?>
