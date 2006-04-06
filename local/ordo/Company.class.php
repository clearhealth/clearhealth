<?php
/**
 * Object Relational Persistence Mapping Class for table: company
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Object Relational Persistence Mapping Class for table: company
 *
 * @package	com.uversainc.clearhealth
 */
class Company extends ORDataObject {

	var $id			= '';
	var $name		= '';
	var $description	= '';
	var $notes		= '';
	var $initials		= '';
	var $url		= '';
	var $_phone_numbers 	= false;
	var $_addresses 	= false;
	var $_types 		= false;

	var $_lookup 		= false;

	function Company($db = null) {
		parent::ORDataObject($db);	
		$this->_table = "company";
		$this->_sequence_name = "sequences";	
		$this->groups = array();

		$this->storage_metadata['string']['email'] = 'email';
	}

	function setup($id = 0) {
		if ($id !== 0) {
			$this->id = $id;
			$this->populate();
		}
	}

	/**
	 * Return a datasource of companies of a specific type
	 *
	 * @param	string	$type
	 */
	function &companyListForType($type) {
		$type_id = 0;
		// lookup id for $type
		$lookup = array_flip($this->getTypeList());
		if (isset($lookup[$type])) {
			$type_id = $lookup[$type];
		}

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> 'c.company_id,c.name',
				'from' 	=> "$this->_table c inner join company_type ct using(company_id)",
				'where'	=> "ct.company_type = $type_id",
			),
			array('name' => 'Name'));
		return $ds;
	}

	/**
 	 * Return an assoc array, id => name
	 *
	 * @param	string	$type
	 */
	function &companyListByType($type) {
		$type_id = 0;
		// lookup id for $type
		$lookup = array_flip($this->getTypeList());
		if (isset($lookup[$type])) {
			$type_id = $lookup[$type];
		}
		

		$res = $this->_execute("select c.company_id,c.name from $this->_table c inner join company_type ct using(company_id) where ct.company_type = $type_id");
		$ret = $res->getAssoc();
		return $ret;
	}
	/**
	* Search for companies returning an array 
	*/
	function search($items,$base_table = "company") {
		if (isset($items['name'])) {
			$items['c.initials'] = $items['name'];
		}

		$wheres = array();
		foreach($items as $key => $item) {
			if (method_exists($this,"get_$key") && !empty($item)) {
				$wheres[] = " $key like '%".mysql_escape_string($item)."%'";
			}
		}
		$where = "where (pn.number_type = 1 or pn.number_type is null)";
		if (count($wheres) > 0) {
			$where .= " and ".implode(' or ',$wheres);
		}

		if ($base_table === "company") {
			$sql = $this->_buildSearchSql($where);
		}
		else
		{
			$sql = "select c.company_id, c.name, number, number_type, cr.company_relation_type company_company_relation
			from $this->_prefix$this->_table c
			left join company_relation cr on c.company_id = cr.related_company_id
			left join company_phone cp on c.company_id = cp.company_id
			left join phone_numbers pn using(phone_id) 
			$where
			order by c.name
			"; 
		}
		$res = $this->_execute($sql);

		$ret = $res->getAll();
		for($i =0; $i < count($ret); $i++) {
			$ret[$i]['index'] = $i+1;
		}
		return $ret;
	}

	/**
	* Build an sql query for searching people
	* @access private
	*/
	function _buildSearchSql($where = "",$groupby = "group by c.company_id",$extra_fields="", $extra_tables = "") {
		$sql = "select c.company_id, c.name, number, number_type, r.person_type company_person_relation $extra_fields 
				from $this->_table c 
				left join company_phone cp using(company_id) 
				left join phone_numbers pn using(phone_id) 
				left join person_company r on c.company_id = r.company_id 
				left join person p using(person_id)
				$extra_tables
				$where $groupby order by c.name";
		return $sql;
	}

	/**
	* Return a list of companies who are related to a particular person
	*/
	function companyList($person_id) {
		settype($person_id,'int');
		$sql = $this->_buildSearchSql("where pn.number_type = 1 and p.person_id = $person_id","");
		$res = $this->_execute($sql);
		$ret = $res->getAll();

		$lookup = $this->getPersonRelateList();

		foreach(array_keys($ret) as $key) {
			$ret[$key]['relation_type'] = $lookup[$ret[$key]['company_person_relation']];
		}
		return $ret;
	}

	/**
	* Return a list of companies who are related to a particular company
	*/
	function companyCompanyList($company_id) {
		settype($company_id,'int');
		$sql = "select c.company_id, c.name, number, number_type, cr.company_relation_type company_company_relation
			from {$this->_prefix}company_relation cr
			inner join $this->_prefix$this->_table c on c.company_id = cr.related_company_id
			left join company_phone cp using(company_id) 
			left join phone_numbers pn using(phone_id) 
			where (pn.number_type = 1 or pn.number_type is null) and cr.company_id = $company_id order by c.name";
		
		$res = $this->_execute($sql);
		$ret = $res->getAll();

		$lookup = $this->getCompanyRelateList();

		foreach(array_keys($ret) as $key) {
			$ret[$key]['relation_type'] = $lookup[$ret[$key]['company_company_relation']];
		}
		return $ret;
	}

	/**
	* Relate a company to another company
	*/
	function relate($company_id,$type) {
		$sql = "replace into company_relation values(".(int)$this->id.",".(int)$company_id.",".(int)$type.")";
		$this->_execute($sql);
	}

	/**
	* Remove a relation between a company and another company
	*/
	function dropRelation($company_id,$type) {
		$sql = "delete from company_relation where company_id =".(int)$this->id." and related_company_id = ".(int)$company_id." and company_relation_type = ".(int)$type."";
		$this->_execute($sql);
	}

	/**
	* Store data to the database
	*/
	function persist() {
		parent::persist();

		if (is_array($this->_types)) {
			$curr_types = $this->_db->getAssoc("select company_type,company_type from company_type where company_id = ".(int)$this->id);
			foreach($curr_types as $curr) {
				if (!isset($this->_types[$curr])) {
					// delete
					$this->_execute("delete from company_type where company_id =".(int)$this->id
					. " and company_type = ".(int)$curr); 
				}
			}
			foreach($this->_types as $type) {
				if (!isset($curr_types[$type])) {
					// add
					$this->_execute("insert into company_type values(".(int)$this->id
					. ",".(int)$type.")"); 
				}
			}
			$this->_types = false;

		}
	}

	function checkForSimilar($input) {
		$check = array('name','initials','email','website');
		$where = "";
		foreach($check as $field) {
			if (isset($input[$field]) && !empty($input[$field])) {
				$v = $input[$field];
				if ($field == 'email') {
					$field = 'ss.value';
				}
				$where .= 
				" or $field like ".$this->dbHelper->quote('%'.$v.'%').
				" or soundex($field) = soundex(".$this->dbHelper->quote($v).')';
			}
		}
		$where = substr($where,3);
		$sql = 'select * from '.$this->tableName()." where $where";

		$query = array('cols'=>'*, ss.value email','from'=>$this->tableName()." c inner join storage_string ss on ss.foreign_key = c.company_id and ss.value_key = 'email'",'where'=>$where);
		$ds = new Datasource_sql();
		$ds->setup(Celini::dbInstance(),$query,array('name'=>'Name','description'=>'Description'));

		return $ds;
	}

	function people_factory($limit = "") {
	}

	/**
	* Generic lookup function, match an id to a Name - Intials
	*/
	function lookup($key) {
		if ($this->_lookup === false) {
			$res = $this->_execute("select company_id, concat_ws(' - ',name,initials) name from $this->_prefix$this->_table");
			$this->_lookup = $res->getAssoc();
		}
		if (isseT($this->_lookup[$key])) {
			return $this->_lookup[$key];
		}
		return "";
	}

    /**#@+
    *	Getter/Setter method used as part of object model for populate, persist, and form_poulate operations
    */
    
    function get_company_id() {
    	return $this->id;	
    }
    function set_company_id($id) {
    	
    	if (is_numeric($id) && $id != 0) {
    		$this->id = $id;
    	}	
    }

	function getTypeList() {
		$list = $this->_load_enum('company_type',false);
		return array_flip($list);
	}

	function getPhoneTypeList() {
		$list = $this->_load_enum('phone_type',false);
		return array_flip($list);
	}

	function get_types() {
		if ($this->_types === false) {
			$this->_types = $this->_db->getCol("select company_type from company_type where company_id = ".(int)$this->id);
		}
		return $this->_types;
	}

	function set_types($types) {
		if (is_array($types)) {
			$this->_types = $types;
		}
	}


	function get_numbers() {
		$p =& ORdataObject::factory('CompanyNumber');
		return $p->numberList($this->id);
	}

	function get_addresses() {
		$a =& ORDataObject::factory('CompanyAddress');
		return $a->addressList($this->id);
	}

	function get_people() {
		$p = &ORDataObject::factory('Person');
		return $p->peopleByCompany($this->id,'full_array');
	}

	function get_companies() {
		return $this->companyCompanyList($this->id);
	}

	function getPersonRelateList() {
		$list = $this->_load_enum('person_relate',true);
		return array_flip($list);
	}
	function getCompanyRelateList() {
		$list = $this->_load_enum('company_relate',true);
		return array_flip($list);
	}

	
	/**
	 * Type is the string value of the company type enumeration
	 */
	function getCompanyList($type) {

		$types = $this->getTypeList();
		$id = (int)array_search($type,$types);
		$res = $this->_execute("select c.company_id, name from company c
					inner join company_type ct using(company_id) where company_type = $id order by name");
		$ret = array(" " => " ");
		while(!$res->EOF) {
			$ret[$res->fields['company_id']] = $res->fields['name'];
			$res->MoveNext();
		}
		return $ret;
	}

	/**
	 * Get address related to this company
	 *
	 * If you have more then 1 address 1 will be returned
	 * If you have none a new/empty Address will be returned
	 */
	function &address() {
		settype($this->id,'int');
		$res = $this->_execute("select address_id from company_address where company_id = $this->id");
		$address_id = 0;
		if (isset($res->fields['address_id'])) {
			$address_id = $res->fields['address_id'];
		}
		$addr =& ORDataObject::factory('CompanyAddress',$address_id,$this->id);
		return $addr;
	}

	function toArray() {
		$ret = array();
		$ret['name'] = $this->get('name');

		$address =& $this->address();
		$ret['address'] = $address->toArray();
		$phone = $this->get_numbers();
		//echo "Company phone# <br>".var_export($phone);
		foreach($phone as $phonearray)
		{
			$ret['phone_number'] = $phonearray["number"];	
			//got to be a better way...
		}
		return $ret;
	}
} 
?>
