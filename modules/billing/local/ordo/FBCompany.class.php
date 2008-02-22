<?php
/**
 * Object Relational Persistence Mapping Class for table: company
 *
 * @package	com.clear-health.freeb2
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
$loader->requireOnce('ordo/MergeDecorator.class.php');
$loader->requireOnce('includes/Datasource_sql.class.php');
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: company
 *
 * @package	com.clear-health.freeb2
 */
class FBCompany extends MergeDecorator {

	var $id			= '';
	var $claim_id	= '';
	var $index		= '';
	var $identifier	= '';
	var $identifier_type = "24"; //24 is EIN, 34 is SSN, XX is National Provider Pin
	var $type		= '';
	var $name		= '';
	var $phone_number  = '';

	var $_lookup = false;
	var $_table = "fbcompany";

	function FBCompany($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = "sequences";	
		$this->groups = array();
		$this->enumTable = $this->_table;

		$fbaddress =& ORDataObject::factory('FBAddress');
		$this->merge('fbaddress',$fbaddress);
		
		$this->addMetaHints("hide",array("claim_id","type","index"));
	}

	function setup($id = 0, $claim_id = 0, $index = 0) {
		$this->set('id',$id);
		if ($claim_id != false) {
			$this->set('claim_id',$claim_id);
		}
		
		if ($this->get('id') > 0) {
			$this->populate();
		}
		elseif ($claim_id > 0 && is_numeric($index)) {
			$sql = '
				SELECT
					* 
				FROM
					fbcompany 
				WHERE 
					type = ' . $this->dbHelper->quote($this->get('type')) . ' AND
					claim_id = ' . $this->dbHelper->quote($this->get('claim_id')) . ' AND
					`index` = ' . (int)$index;
			$this->helper->populateFromResults($this, $this->dbHelper->execute($sql));
			$this->helper->populateStorageValues($this);
			$this->_populateAddress();
		}
		
		if (is_numeric($index)) {
			$this->set('index',$index);
		}
	}	

	/**
	 * Return a datasource of companies of a specific type
	 *
	 * @param	string	$type
	 */
	function &companiesByType($type) {
		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> 'c.company_id,c.name',
				'from' 	=> "$this->_table c ",
				'where'	=> "c.type = ".$this->_quote($type)
			),
			array('name' => 'Name'));
		return $ds;
	}

	/**
	 * Set fbaddress  of company
	 *
	 */
	function set_fbaddress($array) {
		$this->fbaddress->populate_array($array);
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
	* Pull data for this record from the database
	*/
	function populate() {
		parent::populate('company_id');
		$this->_populateAddress();
	}
	
	
	/**
	 * Handle loading the attached address object
	 *
	 * @access private
	 */
	function _populateAddress() {
		// find the attached address object and populate it
		$address_id = 0;
		$res = $this->dbHelper->execute('SELECT address_id FROM fbaddress WHERE external_id = ' . (int)$this->get('id'));
		if ($res && !$res->EOF) {
			$address_id = $res->fields['address_id'];
		}
		$this->fbaddress->setup($address_id, $this->get('id'));
		$this->mergePopulate();
	}

	/**
	* Store data to the database
	*/
	function persist() {
		if (!is_numeric($this->get('index'))) {
			$in = 0;
			$sql = "select MAX(c.`index`)+1 as `index` from $this->_table c where c.claim_id = " . $this->get("claim_id") . " and c.type = "  . $this->_quote($this->get("type")) . " group by c.claim_id";
			$res = $this->_execute($sql);
			if ($res && !$res->EOF) {
				$in = $res->fields['index'];
			}
			$this->set('index',$in);
		}
		parent::persist();

		$this->external_id = $this->get('id');
		$this->mergePersist('external_id');
	}
	
	function &arrayFromClaimId($claim_id) {
		settype($claim_id,'int');
		
		$case_sql = $this->_case_sql();
		
		$sql = "select c.*,a.*, c.type as type, c.name as name $case_sql from $this->_prefix$this->_table c left join fbaddress a on a.external_id = c.company_id "
				." left join storage_int on storage_int.foreign_key=c.company_id"
				." left join storage_string on storage_string.foreign_key=c.company_id"
				." left join storage_date on storage_date.foreign_key=c.company_id"
				." where c.type = " . $this->_quote($this->get("type")) . " and c.claim_id = " . $this->_quote($claim_id) . " group by c.company_id";
		
		$res = $this->_execute($sql);
		$array = array();
		while($res && !$res->EOF) {
			$o =& ORDataObject::factory($this->get("type"));
			$o->populate_array($res->fields);
			$array[] = $o;
			$res->MoveNext();
		}
		
		if (count($array) > 0) {
			return $array;
		}
		$ret = false;	
		return $ret;
	}

    /**#@+
    *	Getter/Setter method used as part of object model for populate, persist, and form_poulate operations
    */
    
    function get_company_id() {
    	return $this->id;	
    }
    function set_company_id($id) {
   		$this->id = $id;
    }

	function getTypeList() {
		$list = $this->_load_enum('company_type',false);
		return array_flip($list);
	}
} 
?>
