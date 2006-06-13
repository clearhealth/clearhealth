<?php
/**
 * Object Relational Persistence Mapping Class for table: Person
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
 * Object Relational Persistence Mapping Class for table: Person
 *
 * @package	com.uversainc.clearhealth
 */
class Person extends ORDataObject {

	/**#@+
	 * Fields of table: Person mapped to class members
	 */
	var $person_id		= '';
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
	var $marital_status	= '';
	var $inactive = '';
	var $primary_practice_id = '';
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
	var $_internalName='Person';
	
	
	/**#@+
	 * {@inheritdoc}
	 */
	var $_table = 'person';
	var $_key = 'person_id';
	var $_foreignKeyList = array(
		'primary_practice_id' => 'Practice'
	);
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Person($db = null) {
		parent::ORDataObject($db);
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
			$curr_types = $this->_db->getAssoc("select person_type,person_type t from person_type where person_id = ".(int)$this->get('id'));
			foreach($curr_types as $curr) {
				if (!isset($this->_types[$curr])) {
					// delete
					$this->_db->execute("delete from person_type where person_id =".(int)$this->get('id') . " and person_type = $curr");
				}
			}
			foreach($this->_types as $type) {
				if (!isset($curr_types[$type])) {
					// add
					$this->_db->execute("insert into person_type values(".(int)$this->get('id') . ",".(int)$type.")"); 
				}
			}
			$this->_types = false;

		}

		if ($this->nameHistory !== false) {
			$this->nameHistory->persist();
		}
	}

	/**
	 * Helper function for lookup, match an id with initials
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
	 * Set date of birth, formating it correctly
	 */
	function set_date_of_birth($date) {
		$this->_setDate('date_of_birth', $date);
	}
	
	/**
	 * Returns date of birth as an English date instead of ISO
	 *
	 * @return string
	 * @access protected
	 */
	function get_date_of_birth() {
		return $this->_getDate('date_of_birth');
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
		return array_pop($list);
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
		$cols = "p.person_id, last_name, first_name, pt.person_type, IF(inactive = 0, 'Yes', 'No') AS active";
		$labels = array('last_name' => 'Last Name', 'first_name' => 'First Name', 'person_type' => 'Type');
		if ($includeUser) {
			$from .= " inner join user u using(person_id)";
			$cols .= ", u.username, u.user_id";
			$labels['username'] = 'Username';
		}
		// Put active flag column at the end
		$labels['active'] = 'Active';

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> $cols,
				'from' 	=> $from,
				'where'	=> "pt.person_type in(".implode(',',$type_id).")",
				'orderby' => 'last_name, first_name'
			),
			$labels);
		$ds->registerFilter('person_type',array(&$this,'lookupType'));
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
	 * Type 0 will pull all users (not patients)
	 * 
	 * @param	string	$type The string value of the wanted type from the person type enumeration
	 */
	function getPersonList($type,$blank=true) {
		//$this->nameHistory->set('person_id',$this->get('person_id'));
		$sqlPersonTypes = array();
		$sqlPersonTypeCol = 'person_type';
		$types = $this->getTypeList();
		
		if(is_int($type)) {
			$sqlPersonTypes[] = "{$sqlPersonTypeCol} = {$type}";
		} else {
			$sqlPersonTypes[] = $sqlPersonTypeCol . ' = '.(int)array_search($type,$types);
		}
		if ($type == 'Provider') {
			$em =& Celini::enumManagerInstance();
			$typeList =& $em->enumList('person_type');
			for ($typeList->rewind(); $typeList->valid(); $typeList->next()) {
				$typeValue = $typeList->current();
				if ($typeValue->extra1 == 1) {
					$sqlPersonTypes[] = 'person_type = ' . $this->dbHelper->quote($typeValue->key);
				}
			}
		}
		elseif($type==0) {
			$sqlPersonTypes[] = $sqlPersonTypeCol . ' > 1';
		}
		
		$sql = "select p.person_id, concat_ws(' ',first_name,last_name) name from person p 
					inner join person_type ct using(person_id) where " . implode(' OR ', $sqlPersonTypes) . " order by last_name, first_name";
					
		$returnArray = ($blank) ? array(" " => " ") : array();
		$returnArray = array_merge($returnArray, $this->dbHelper->getAssoc($sql));
		return $returnArray;
	}


	/**
	 * Get an array of types for this user
	 */
	function get_types() {
		if ($this->_types === false) {
			$sql = "select person_type, person_type t from person_type where person_id = ".(int)$this->get('id');
			$res = $this->_execute($sql);
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
		$p =& Celini::newORDO('PersonNumber');
		return $p->numberList($this->get('id'));
	}

	
	function get_addresses() {
		$a = new PersonAddress();
		return $a->addressList($this->get('id'));
	}

	/**
	* Relate a person to a company
	*/
	function relate($company_id,$type) {
		$sql = "replace into person_company values(".(int)$this->get('id').",".(int)$company_id.",".(int)$type.")";
		$this->_execute($sql);
	}

	/**
	* Remove a relation between a person and a company
	*/
	function dropRelation($company_id,$type) {
		$sql = "delete from person_company where person_id =".(int)$this->get('id')." and company_id = ".(int)$company_id." and person_type = ".(int)$type."";
		$this->_execute($sql);
	}

	/**
	 * Get address of person
	 *
	 * If you have more then 1 address 1 will be returned
	 * If you have none a new/empty Address will be returned
	 */
	function &address() {
		$id = EnforceType::int($this->get('id'));
		$res = $this->_execute("select address_id from person_address where person_id = ".$id);
		$address_id = 0;
		if (isset($res->fields['address_id'])) {
			$address_id = $res->fields['address_id'];
		}
		$addr =& ORDataObject::factory('PersonAddress',$address_id,$this->get('id'));
		return $addr;
	}

	/**
	 * Get number related to this person
	 *
	 * If you have more then 1 number of type then 1 will be returned
	 * If you have none a new/empty PersonNumber will be returned
	 */
	function &numberByType($type) {
		if ($this->get('id') <= 0) {
			$ordo =& Celini::newORDO('PersonNumber');
			return $ordo;
		}
		$type_id = 0;
		$lookup = $this->_load_enum('number_type');
		if (isset($lookup[$type])) {
			$type_id = $lookup[$type];
		}

		$res = $this->_execute("select pn.number_id from person_number pn inner join number using(number_id) where person_id = ".$this->get('id')." and number_type = $type_id");
		$number_id = 0;
		if (isset($res->fields['number_id'])) {
			$number_id = $res->fields['number_id'];
		}
		$addr =& ORDataObject::factory('PersonNumber',$number_id,$this->get('id'));
		$addr->set('number_type',$type_id);
		return $addr;
	}
	
	function numberValueByType($type) {
		$number =& $this->numberByType($type);
		return $number->get('number');
	}

	
	function &nameHistoryList() {
		$nh =& ORDataOBject::factory('NameHistory');
		$return =& $nh->nameHistoryList($this->get('id'));
		return $return;
	}

	function &identifierList() {
		$i =& ORDataOBject::factory('Identifier');
		$return =& $i->identifierList($this->get('id'));
		return $return;
	}

	function &insuredRelationshipList() {
		$ir =& ORDataObject::Factory('InsuredRelationship');
		$return =& $ir->insuredRelationshipList($this->get('id'));
		return $return;
	}

	var $_tCache = false;
	function lookupType($id) {
		if ($this->_tCache === false) {
			$this->_tCache = $this->getTypeList();
		}
		if (isset($this->_tCache[$id])) {
			return $this->_tCache[$id];
		}
	}
	var $_gCache = false;
	function lookupGender($id) {
		if ($this->_gCache === false) {
			$this->_gCache = $this->getGenderList();
		}
		if (isset($this->_gCache[$id])) {
			return $this->_gCache[$id];
		}
	}
	var $_itCache = false;
	function lookupIdentifierType($id) {
		if ($this->_itCache === false) {
			$this->_itCache = $this->getIdentifierTypeList();
		}
		if (isset($this->_itCache[$id])) {
			return $this->_itCache[$id];
		}
	}


	function idFromType($type) {
		if ($this->_tCache === false) {
			$this->_tCache = $this->getTypeList();
		}
		return array_search($type,$this->_tCache);
	}

	function toArray() {
		$fields = array('person_id','salutation','first_name','middle_name','last_name','gender','date_of_birth','identifier','identifier_type','type');
		$ret = array();
		foreach($fields as $field) {
			$ret[$field] = $this->get($field);
		}
		$address =& $this->address();
		$ret['address'] = $address->toArray();
		$ret['identifier_type'] = $this->lookupIdentifierType($ret['identifier_type']);
		$ret['gender'] = $this->lookupGender($ret['gender']);
		$number =& $this->numberByType('Home');
		$ret['home_phone'] = $number->get('number');
		return $ret;
	}

	
	/**
	 * Get the age of the person based on date of birth
	 */
	function get_age() {
		$sql =  "select DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(date_of_birth, '%Y') - 
			(DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(date_of_birth, '00-%m-%d')) AS age
			from $this->_table where person_id = ".(int)$this->get('id');
		$res = $this->_execute($sql);
		if ($res && !$res->EOF) {
			return $res->fields['age'];
		}
	}
	
	function value_age() {
		$age = $this->get('age');
		if ($age > 0) {
			return $age;
		}
		else {
			$sql = '
				SELECT
					 ROUND(DATEDIFF(NOW(), date_of_birth) / 7) AS age
				FROM 
					' . $this->tableName() . '
				WHERE
					person_id = ' . $this->dbHelper->quote($this->get('id'));
			$result = $this->dbHelper->execute($sql);
			return $result->fields['age'] . ' wk.';
		}
	}

	function value_name() {
		$f = $this->get('first_name');
		$l = $this->get('last_name');
		$m = $this->get('middle_name');

		$name = $l;
		if (!empty($l)) {
			$name .= ', ';
		}
		$name .= "$f $m";
		return $name;
		
	}

	function getMaritalStatusList() {
		$list = $this->_load_enum('marital_status',false);
		return array_flip($list);
	}

	function get_print_marital_status() {
		$list = array_flip($this->_load_enum('marital_status',false));
		if(isset($list[$this->get("marital_status")])) {
			return $list[$this->get("marital_status")];
		}
	}
	
	function getVisitQueues() {
		$pat =& $this;
		if($this->name() != 'Patient') {
			$pat =& Celini::newORDO('Patient',$this->get('id'));
}
		$queues =& $pat->getParents('VisitQueue');
		return $queues;
	}
	
	function &getProvider() {
		$patient =& Celini::newORDO('Patient',$this->get('id'));
		$prov =& $patient->getProvider();
		return $prov;
	}
	
	function &getPatient() {
		$patient =& Celini::newORDO('Patient',$this->get('id'));
		return $patient;
	}
}
?>
