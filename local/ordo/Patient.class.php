<?php
/**
 * Object Relational Persistence Mapping Class for table: Patient
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
 * Object Relational Persistence Mapping Class for table: Patient
 *
 * @package	com.uversainc.clearhealth
 * @todo: add release_of_information_code
 */
class Patient extends MergeDecorator {

	/**#@+
	 * Fields of table: Patient mapped to class members
	 */
	var $id = "";
	/**#@-*/

	/**
	 * The base Person instance that this patient is extending
	 */
	var $person;
	var $record_number = "";
	var $employer_name = "";
	var $default_provider = "";


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Patient($db = null) {
		parent::ORDataObject($db);
		$this->_table = 'patient';
		$this->_sequence_name = 'sequences';
		$this->merge('person',ORDataObject::factory('Person'));
		
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Patient with this
	 */
	function setup($id = 0) {
		$this->person->set('id',$id);
		$this->set('id',$id);
		if ($id > 0) {
			$this->populate();
		}
	}

	/**
	 * Generate new record number, currently uses database sequence
	 */
	function generate_record_number() {
		
		$rn = $this->_db->GenID("record_sequence");
		return $rn;
	}

	/**
	 * Persist the data
	 */
	function persist() {
		if (strlen($this->get("record_number")) == 0) {
			$this->record_number = $this->generate_record_number();	
		}
		$this->mergePersist('person_id');
		if ($this->get('id') == 0) {
			$this->set('id',$this->person->get('id'));
		}
		parent::persist();
	}

	/**
	 * Load the data from the db
	 */
	function populate() {
		parent::populate('person_id');
		$this->mergePopulate('person_id');
	}

	/**#@+
	 * Getters and Setters for Table: Patient
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

	function get_search_name() {
		if ($this->get('id') > 0) {
			return $this->get('last_name').", ".$this->get('first_name')." #".$this->get('record_number');
		}
	}


	/**
	 * Setup employer relationship
	 */
	function set_employer($branch_id) {
		if ($branch_id > 0) {
			if ($this->id > 0) {
				$this->person->relate($branch_id,0);
			}
		}
	}

	function get_employer() {
		if ($this->id > 0) {
			$res = $this->_execute("select c.company_id from company c inner join person_company using(company_id) where person_id = $this->id");
			return $res->fields['company_id'];
		}
		return 0;
	}

	/**#@-*/

	/**#@+
	 * Proxy methods to the person class were decorating
	 */

	function getGenderList() {
		return $this->person->getGenderList();
	}
	function getTypeList() {
		return $this->person->getTypeList();
	}
	function getIdentifierTypeList() {
		return $this->person->getIdentifierTypeList();
	}
	function get_numbers() {
		return $this->person->get_numbers();
	}
	function get_addresses() {
		return $this->person->get_addresses();
	}
	function peopleByCompany($company_id,$type) {
		return $this->person->peopleByCompany($company_id,$type);
	}
	function &numberByType($type,$value = false) {
		return $this->person->numberByType($type,$value);
	}
	function &address() {
		return $this->person->address();
	}
	function &nameHistoryList() {
		return $this->person->nameHistoryList();
	}
	function &identifierList() {
		return $this->person->identifierList();
	}
	function &insuredRelationshipList() {
		return $this->person->insuredRelationshipList();
	}
	function lookupType($id) {
		return $this->person->lookupType($id);
	}
	function getMaritalStatusList() {
		return $this->person->getMaritalStatusList();
	}
	/**#@-*/

	/**
	 * Return a datasource of all patients
	 *
	 */
	function &patientList($company_id = 0) {
		settype($company_id,'int');
		$ds =& new Datasource_sql();
		$sql = array(
			'cols' 	=> "p.person_id, concat_ws(' ',first_name,last_name) name, n.number phone, c.number cell, email, 'link' link ",
			'from' 	=> "$this->_table p inner join person e using(person_id) left join person_company pc using(person_id)
					left join person_address pa using(person_id) left join address a using(address_id)
					left join person_number pn on p.person_id = pn.person_id
					left join number n on pn.number_id = n.number_id and n.number_type = 1
					left join number c on pn.number_id = c.number_id and c.number_type = 2
					",
			'groupby' => 'person_id',
			'orderby' => 'name'
			);
		$cols = array('name' => 'Name','phone' => 'Phone');

		if ($company_id > 0) {
			$sql['where'] = "pc.company_id = $company_id";
		}
		$ds->setup($this->_db,$sql,$cols);
		return $ds;
	}

	function toArray() {
		$ret = $this->person->toArray();
		$ret['record_number'] = $this->get('record_number');
		return $ret;
	}

	function get_print_default_provider() {
		$u =& User::fromId($this->get('default_provider'));
		return $u->get('username');	
	}

	function get_print_registration_location() {
		$ps =& ORDataObject::Factory('PatientStatistics',$this->get('id'));

		$b =& ORDataObject::Factory('Building');
		$list = $b->getBuildingList();

		$loc = $ps->get('registration_location');
		if (isset($list[$loc])) {
			return $list[$loc];
		}
	}
}
?>
