<?php
/**
 * Object Relational Persistence Mapping Class for table: Patient
 *
 * @package	com.uversainc.freestand
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once APP_ROOT.'/local/ordo/Person.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: Patient
 *
 * @package	com.uversainc.freestand
 */
class Patient extends ORDataObject {

	/**#@+
	 * Fields of table: Patient mapped to class members
	 */
	var $id			= '';
	var $ssn		= '';
	var $date_hired		= '';
	var $date_terminated	= '';
	var $date_approved	= null;
	var $approved_by	= null;
	var $num_complaints	= '';
	var $num_warnings	= '';
	var $department		= '';
	/**#@-*/

	/**
	 * The base Person instance that this patient is extending
	 */
	var $person;


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Patient($db = null) {
		parent::ORDataObject($db);
		$this->_table = 'patient';
		$this->_sequence_name = 'sequences';
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Patient with this
	 */
	function setup($id = 0) {
		$this->set('id',$id);
		$this->person =& ORDataObject::factory('Person',$id);
		if ($id > 0) {
			$this->populate();
		}
	}

	/**
	 * Persist the data
	 */
	function persist() {
		if (isset($this->person)) {
			$this->person->persist();
			$this->id = $this->person->get('person_id');
		}
		//parent::persist();
	}

	/**
	 * Load the data from the db
	 */
	function populate() {
		parent::populate('person_id');
		$this->person->populate();
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

	/**
	 * In this app we only have one type
	 */
	function get_type() {
		$types = $this->get('types');
		if (count($types) > 0) {
			return array_shift($types);
		}
	}

	/**
	 * set the single type
	 */
	function set_type($type) {
		$this->person->types = array();
		$this->set('types',array($type=>$type));
	}

	/**
	 * format date
	 */
	function set_date_hired($date) {
		$this->date_hired = $this->_mysqlDate($date);
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
	/**#@-*/

	/**
	 * main get method that hits person as well
	 */
	function get($key) {
		if ($this->exists($key)) {
			return parent::get($key);
		}
		else {
			return $this->person->get($key);
		}
	}

	/**
	 * main set method that hits person as well
	 */
	function set($key,$value) {
		if ($this->exists($key)) {
			return parent::set($key,$value);
		}
		else {
			return $this->person->set($key,$value);
		}
	}

	/**
	 * Return a datasource of all patients
	 *
	 */
	function &patientList($branch_id = false) {
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
			'orderby' => 'last_name, first_name'
			);
		$cols = array('name' => 'Name','phone' => 'Phone','cell'=>'Cell', 'date_hired' => 'Date Hired');

		if ($branch_id > 0) {
			$sql['where'] = "pc.company_id = $branch_id";
		}
		$ds->setup($this->_db,$sql,$cols);
		return $ds;
	}

	/**
	 * Return a datasource of all patients who haven't been approved
	 *
	 */
	function &approveList() {
		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "p.person_id, concat_ws(' ',first_name,last_name) name",
				'from' 	=> "$this->_table p inner join person e using(person_id) ",
				'orderby' => 'last_name, first_name',
				'where' => 'date_approved is null'
			),
			array('name' => 'Name'));
		return $ds;
	}

	/**
	 * Approve the current account
	 */
	function approve() {
		$this->set('date_approved',date('Y-m-d'));
		$me =& Me::getInstance();
		$this->set('approved_by',$me->get_id());
		$this->persist();
	}

	/**
	 * Get the manager of the current patient
	 */
	function getManagerId() {
		$lookup = array_flip($this->getTypeList());

		$type = "Branch Manager";
		
		$type_id = 0;
		if (isset($lookup[$type])) {
			$type_id = $lookup[$type];
		}
		$res = $this->_execute("select m.person_id from person e 
					inner join person_company epc using(person_id) 
					inner join person_company mpc using(company_id)
					inner join person m using(person_id)
					inner join person_type mpt using(person_id)
					where mpt.person_type = $type_id");
		if (isset($res->fields['person_id'])) {
			return $res->fields['person_id'];
		}
		return 1;
	}

	/**
	 * Get the manager of a branch
	 */
	function &managerFromBranchId($branch_id) {
		settype($branch_id,'int');

		$manager =& ORDataObject::factory('Patient');
		$lookup = array_flip($manager->getTypeList());

		$type = "Branch Manager";
		
		$type_id = 0;
		if (isset($lookup[$type])) {
			$type_id = $lookup[$type];
		}
		$res = $manager->_execute("select m.person_id from person m inner join person_company pc using(person_id) inner join person_type pt using(person_id)
						where company_id = $branch_id and pt.person_type = $type_id");
		if (isset($res->fields['person_id'])) {
			$manager->setup($res->fields['person_id']);
		}
		return $manager;
	}
}
?>
