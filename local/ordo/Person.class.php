<?php
/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.uversainc.freestand
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
require_once CELLINI_ROOT.'/includes/Datasource_sql.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.uversainc.freestand
 */
class Person extends ORDataObject {

	/**#@+
	 * Fields of table: Person mapped to class members
	 */
	var $id			= '';
	var $salutation		= '';
	var $last_name		= '';
	var $first_name		= '';
	var $middle_name	= '';
	var $gender		= '';
	var $initials		= '';
	var $date_of_birth	= '';
	var $summary		= '';
	var $title		= '';
	var $notes		= '';
	var $email		= '';
	var $secondary_email	= '';
	var $has_photo		= '';
	var $identifier		= '';
	var $identifier_type	= '';
	/**#@-*/

	var $nameHistory = false;

	/**#@+
	 * Lookup cache
	 */
	var $_lookup	= false;
	var $_lookupi	= false;
	/**#@-*/

	/**
	 * Person type
	 */
	var $_types	= false;


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Person($db = null) {
		parent::ORDataObject($db);
		$this->_table = 'person';
		$this->_sequence_name = 'sequences';
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Person with this
	 */
	function setup($id = 0) {
		$this->set('id',$id);
		if ($id > 0) {
			$this->populate();
		}
	}

	/**
	 * Pull data for this record from the database
	 */
	function populate() {
		parent::populate('person_id');
	}

	/**
	* Store data to the database
	*/
	function persist() {
		parent::persist();

		if (is_array($this->_types)) {
			$curr_types = $this->_db->getAssoc("select person_type,person_type t from person_type where person_id = ".(int)$this->id);
			foreach($curr_types as $curr) {
				if (!isset($this->_types[$curr])) {
					// delete
					$this->_db->execute("delete from person_type where person_id =".(int)$this->id . " and person_type = $curr");
				}
			}
			foreach($this->_types as $type) {
				if (!isset($curr_types[$type])) {
					// add
					$this->_db->execute("insert into person_type values(".(int)$this->id . ",".(int)$type.")"); 
				}
			}
			$this->_types = false;

		}

		if ($this->nameHistory !== false) {
			$this->nameHistory->persist();
		}
	}

	/**
	 * helper function for lookup, matchn an id with initials
	 */
	function _lookupInitials($key) {
		if ($this->_lookupi === false) {
			$res = $this->_execute("select person_id, initials from $this->_prefix$this->_table");
			$this->_lookupi = $res->getAssoc();
		}
		if (isseT($this->_lookupi[$key])) {
			return $this->_lookupi[$key];
		}
		return "";
	}


	/**
	* Generic lookup function, match an id to a first_name last_name
	*/
	function lookup($key,$initials = false) {
		if ($initials) {
			return $this->_lookupInitials($key);
		}
		if ($this->_lookup === false) {
			$res = $this->_execute("select person_id, concat_ws(' ',first_name,last_name) name from $this->_prefix$this->_table");
			$this->_lookup = $res->getAssoc();
		}
		if (isseT($this->_lookup[$key])) {
			return $this->_lookup[$key];
		}
		return "";
	}

	/**#@+
	 * Getters and Setters for Table: Person
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
	 * Set date of birth, formating it correctly
	 */
	function set_date_of_birth($date) {
		$this->date_of_birth = $this->_mysqlDate($date);
	}

	/**
	 * When we only have a single type use this
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
	/**#@-*/

	/**#@+
	 * Create a name history record when the name is changed
	 */
	function set_last_name($name) {
		$this->_nameHistory('last_name',$name);
		$this->last_name = $name;
	}
	function set_first_name($name) {
		$this->_nameHistory('first_name',$name);
		$this->first_name = $name;
	}
	function set_middle_name($name) {
		$this->_nameHistory('middle_name',$name);
		$this->middle_name = $name;
	}
	/**#@-*/

	function _nameHistory($field,$newValue) {
		if ($this->_populated && !empty($this->$field) && $this->$field !== $newValue) {
			if ($this->nameHistory === false) {
				$this->nameHistory =& ORDataObject::factory('NameHistory');
				$this->nameHistory->set('update_date',date('Y-m-d'));
				$this->nameHistory->set('person_id',$this->get('person_id'));
			}
			if (strlen($this->nameHistory->get("first_name")) == 0) $this->nameHistory->set("first_name",$this->get("first_name"));
			if (strlen($this->nameHistory->get("middle_name")) == 0) $this->nameHistory->set("middle_name",$this->get("middle_name"));
			if (strlen($this->nameHistory->get("last_name")) == 0) $this->nameHistory->set("last_name",$this->get("last_name"));
		}
	}


	/**#@+
	 * Enumeration getters
	 */
	function getTypeList() {
		$list = $this->_load_enum('person_type',false);
		return array_flip($list);
	}

	function getIdentifierTypeList($index = "") {
		$list = $this->_load_enum('identifier_type',false);
		return array_flip($list);
	}
	
	function get_print_identifier_type() {
		$list = array_flip($this->_load_enum('identifier_type',false));
		if(isset($list[$this->get("identifier_type")])) {
			return $list[$this->get("identifier_type")];
		}
	}

	function getGenderList() {
		$list = $this->_load_enum('gender',false);
		return array_flip($list);
	}
	
	function get_print_gender() {
		$list = array_flip($this->_load_enum('gender',false));
		if(isset($list[$this->get("gender")])) {
			return $list[$this->get("gender")];
		}
	}

	/**#@-*/

	/**
	 * Return a datasource of people of a specific type
	 *
	 * @param	string|array	$type
	 */
	function &peopleByType($type,$includeUser = false) {
		$type_id = array();

		if (!is_array($type)) {
			$type = array($type);
		}
		// lookup id for $type
		$lookup = array_flip($this->getTypeList());

		foreach($type as $t) {
			if (isset($lookup[$t])) {
				$type_id[] = $lookup[$t];
			}
			else {
				$type_id[] = 0;
			}
		}
		
		$from = "$this->_table p inner join person_type pt using(person_id) ";
		$cols = "p.person_id, last_name, first_name, pt.person_type";
		$labels = array('last_name' => 'Last Name', 'first_name' => 'First Name', 'person_type' => 'Type');
		if ($includeUser) {
			$from .= " inner join user u using(person_id)";
			$cols .= ", u.username";
			$labels['username'] = 'Username';
		}

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> $cols,
				'from' 	=> $from,
				'where'	=> "pt.person_type in(".implode(',',$type_id).")",
				'orderby' => 'last_name, first_name'
			),
			$labels);
		return $ds;
	}

	/**
	 * Return a datasource, or array of people of a specific type
	 *
	 * @param	int	$company_id
	 * @param	string	$type	datasource|array|full_array
	 */
	function &peopleByCompany($company_id,$type="datasource") {
		settype($company_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "p.person_id, concat_ws(' ',first_name,last_name) name, email, number phone",
				'from' 	=> "$this->_table p inner join person_company pc using(person_id) left join person_number pn using(person_id) 
						left join number n using(number_id)",
				'where'	=> "pc.company_id = $company_id and n.number_type = 1",
				'groupby' => 'person_id',
				'orderby' => 'last_name, first_name'
			),
			array('name' => 'Name','email'=>'Email','phone'=>'Phone'));

		switch($type) {
			case "datasource":
				return $ds;
				break;
			case "array":
				return $ds->toArray('person_id','name');
				break;
			case "full_array":
				return $ds->toArray();
				break;
		}
	}


	
	/**
	 * Get an array of people (person_id => name) for a specific type
	 * 
	 * @param	string	$type The string value of the wanted type from the person type enumeration
	 */
	function getPersonList($type) {
		//$this->nameHistory->set('person_id',$this->get('person_id'));

		$types = $this->getTypeList();
		$id = (int)array_search($type,$types);
		$res = $this->_execute("select p.person_id, concat_ws(' ',first_name,last_name) name from person p 
					inner join person_type ct using(person_id) where person_type = $id order by last_name, first_name");
		$ret = array(" " => " ");
		while(!$res->EOF) {
			$ret[$res->fields['person_id']] = $res->fields['name'];
			$res->MoveNext();
		}
		return $ret;
	}


	/**
	 * Get an array of types for this user
	 */
	function get_types() {
		if ($this->_types === false) {
			$res = $this->_execute("select person_type, person_type t from person_type where person_id = ".(int)$this->id);
			$this->_types = $res->getAssoc();
		}
		return $this->_types;
	}

	/**
	 * Set the types for this user
	 */
	function set_types($types) {
		if (is_array($types)) {
			$this->_types = $types;
		}
	}

	function get_numbers() {
		$p = new PersonNumber();
		return $p->numberList($this->id);
	}

	
	function get_addresses() {
		$a = new PersonAddress();
		return $a->addressList($this->id);
	}

	/**
	* Relate a person to a company
	*/
	function relate($company_id,$type) {
		$sql = "replace into person_company values(".(int)$this->id.",".(int)$company_id.",".(int)$type.")";
		$this->_execute($sql);
	}

	/**
	* Remove a relation between a person and a company
	*/
	function dropRelation($company_id,$type) {
		$sql = "delete from person_company where person_id =".(int)$this->id." and company_id = ".(int)$company_id." and person_type = ".(int)$type."";
		$this->_execute($sql);
	}

	/**
	 * Get address of person
	 *
	 * If you have more then 1 address 1 will be returned
	 * If you have none a new/empty Address will be returned
	 */
	function &address() {
		settype($this->id,'int');
		$res = $this->_execute("select address_id from person_address where person_id = $this->id");
		$address_id = 0;
		if (isset($res->fields['address_id'])) {
			$address_id = $res->fields['address_id'];
		}
		$addr =& ORDataObject::factory('PersonAddress',$address_id,$this->id);
		return $addr;
	}

	/**
	 * Get number related to this person
	 *
	 * If you have more then 1 number of type then 1 will be returned
	 * If you have none a new/empty PersonNumber will be returned
	 */
	function &numberByType($type,$value = false) {
		settype($this->id,'int');

		$type_id = 0;
		$lookup = array_flip($this->_load_enum('number_type'));
		if (isset($lookup[$type])) {
			$type_id = $lookup[$type];
		}

		$res = $this->_execute("select pn.number_id from person_number pn inner join number using(number_id) where person_id = $this->id and number_type = $type_id");
		$number_id = 0;
		if (isset($res->fields['number_id'])) {
			$number_id = $res->fields['number_id'];
		}
		$addr =& ORDataObject::factory('PersonNumber',$number_id,$this->id);
		if ($value) {
			return $addr->get('number');
		}
		return $addr;
	}

	
	function &nameHistoryList() {
		$nh =& ORDataOBject::factory('NameHistory');
		return $nh->nameHistoryList($this->get('id'));
	}

	function &identifierList() {
		$i =& ORDataOBject::factory('Identifier');
		return $i->identifierList($this->get('id'));
	}

	function &insuredRelationshipList() {
		$ir =& ORDataObject::Factory('InsuredRelationship');
		return $ir->insuredRelationshipList($this->get('id'));
	}
}
?>
