<?php
/**
 * Object Relational Persistence Mapping Class for table: insured_relationship
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: insured_relationship
 *
 * @package	com.uversainc.clearhealth
 */
class InsuredRelationship extends ORDataObject {

	/**#@+
	 * Fields of table: insured_relationship mapped to class members
	 */
	var $id					= '';
	var $insurance_program_id		= '';
	var $person_id				= '';
	var $subsciber_id			= '';
	var $subscriber_to_patient_relationship	= '';
	var $copay				= '';
	var $assigning				= '';
	var $group_name				= '';
	var $group_number			= '';
	var $default_provider			= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function InsuredRelationship($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'insured_relationship';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Insured_relationship with this
	 */
	function setup($id = 0,$person_id = false) {
		if ($person_id > 0) {
			$this->set('person_id',$person_id);
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
		parent::populate('insured_relationship_id');
	}

	/**#@+
	 * Getters and Setters for Table: insured_relationship
	 */

	
	/**
	 * Getter for Primary Key: insured_relationshp_id
	 */
	function get_insured_relationship_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: insured_relationshp_id
	 */
	function set_insured_relationship_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	function insuredRelationshipList($person_id) {
		settype($person_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "ir.insured_relationship_id, ir.insurance_program_id, group_name, group_number, copay, ip.name as program, c.name as company",
				'from' 	=> "$this->_table ir left join insurance_program ip using (insurance_program_id) left join company c using (company_id)",
				'where' => " person_id = $person_id"
			),
			array('company'=> 'Company', 'program' => "Program", 'group_name' => 'Group Name','group_number'=> 'Group Number', 'copay' => 'Co-pay'));
		return $ds;
	}

	function getInsurerList() {
		$res = $this->_execute("select company_id, name from company");
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['company_id']] = $res->fields['name'];
			$res->MoveNext();
		}
		return $ret;
	}

	function getAssigningList() {
		$list = $this->_load_enum('assigning',false);
		return array_flip($list);
	}

	function getSubscriberToPatientRelationshiplist() {
		$list = $this->_load_enum('subscriber_to_patient_relationship',false);
		return array_flip($list);
	}
}
?>
