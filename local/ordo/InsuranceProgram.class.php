<?php
/**
 * Object Relational Persistence Mapping Class for table: insurance_program
 *
 * @package	com.clear-health.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: insurance_program
 *
 * @package	com.clear-health.clearhealth
 */
class InsuranceProgram extends ORDataObject {

	/**#@+
	 * Fields of table: insurance_program mapped to class members
	 */
	var $id			= '';
	var $payer_type		= '';
	var $company_id		= '';
	var $name		= '';
	var $fee_schedule_id 	= '';
	var $x12_sender_id	= '';
	var $x12_receiver_id	= '';
	var $x12_version	= '';
	var $address_id		= '';
	var $funds_source	= '';
	var $program_type = '';
	var $payer_identifier = '';
	/**#@-*/
	var $_table = 'insurance_program';
	var $_internalName='InsuranceProgram';


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function InsuranceProgram($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Insurance_program with this
	 */
	function setup($id = 0,$company_id = 0) {
		if ($company_id > 0) {
			$this->set('company_id',$company_id);
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
		parent::populate('insurance_program_id');
	}
	
	/**
	 * When we first persist a payer, we will automatically add it to the default payer group.
	 *
	 */
	function persist() {
		if($this->get('id') < 1) {
			parent::persist();
			$pg =& Celini::newORDO('PayerGroup',1);
			$db =& new clniDB();
			$payers = $pg->valueList('payer_id');
			$count = count($payers) + 1;
			$sql = "INSERT INTO insurance_payergroup (insurance_program_id,payer_group_id,`order`)
			VALUES(".$db->quote($this->get('id')).",".$pg->get('id').",{$count})";
			$db->execute($sql);
		} else {
			parent::persist();
		}
	}

	/**#@+
	 * Getters and Setters for Table: insurance_program
	 */

	
	/**
	 * Getter for Primary Key: insurance_program_id
	 */
	function get_insurance_program_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: insurance_program_id
	 */
	function set_insurance_program_id($id)  {
		$this->id = $id;
	}

	/**
	 * Get the insurance companies name
	 */
	function get_insurance_company_name() {
		$company =& Celini::newORDO('Company',$this->get('company_id'));
		return $company->get('name');
	}
	/**#@-*/

	var $_typeCache = false;

	/**
	 * Cached lookup for payer type
	 */
	function lookupPayerType($type_id) {
		if ($this->_typeCache === false) {
			$this->_typeCache = $this->getPayerTypeList();
		}
		if (isset($this->_typeCache[$type_id])) {
			return $this->_typeCache[$type_id];
		}
	}

	function getPayerTypeList() {
		$list = $this->_load_enum('payer_type',false);
		return array_flip($list);
	}

	function getProgramTypeList() {
		$list = $this->_load_enum('insurance_program_type',false);
		return array_flip($list);
	}

	
	/**
	 * This method should no longer be used, instead use
	 * <code>$insuranceProgram->valueList('programs')</code>
	 *
	 * @deprecated
	 * @see valueList_programs()
	 */
	function programList() {
		return $this->valueList('programs');
	}

	/**
	 * This method should no longer be used.  Instead use:
	 *
	 * <code>
	 *    $company =& Celini::newORDO('Company', 1234);
	 *    $ds =& $company->loadDatasource('DetailedProgramList');
	 * </code>
	 *
	 * @deprecated
	 */
	function &detailedProgramList($company_id) {
		Celini::deprecatedWarning('Call to InsuranceProgram::detailProgramList() - should use datasource');
		
		global $loader;
		$loader->requireOnce('includes/DatasourceFileLoader.class.php');
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('Company_DetailedProgramList_DS');
		$ds =& new Company_DetailedProgramList_DS($company_id);
		return $ds;
	}

	function getCompanysProgramsList($company_id) {
		$sql = "select insurance_program_id, name from $this->_table where company_id = $company_id order by name";
		$ret = array();
		$res = $this->_execute($sql);

		while($res && !$res->EOF) {
			$ret[$res->fields['insurance_program_id']] = $res->fields['name'];
			$res->moveNext();
		}
		return $ret;
	}

	function toArray() {
		$ret = array();
		$ret['name'] = $this->get('name');
		$ret['payer_type'] = $this->lookupPayerType($this->get('payer_type'));
		$ret['program_name'] = $this->get('name');
		$company =& ORDataObject::factory('Company',$this->get('company_id'));
		$ret['company'] = $company->toArray();
		return $ret;
	}

	function checkForSimilar($input) {
		$check = array('name','x12_sender_id','x12_receiver_id');
		$where = "";
		foreach($check as $field) {
			if (isset($input[$field]) && !empty($input[$field])) {
				$where .= 
				" or ip.$field like ".$this->dbHelper->quote('%'.$input[$field].'%').
				" or soundex(ip.$field) = soundex(".$this->dbHelper->quote($input[$field]).')';
			}
		}

		$manager =& Celini::enumManagerInstance();

		$where = substr($where,3);
		$query = array('cols'=>'c.company_id, ip.insurance_program_id, c.name company, ip.name program, payer_type.value payer_type, f.label fee_schedule',
				'from'=>$this->tableName().
					' ip inner join company c using(company_id) left join fee_schedule f on ip.fee_schedule_id = f.fee_schedule_id'.
					$manager->joinSql('payer_type','ip.payer_type'),
				'where'=>$where);
		$ds = new Datasource_sql();
		$ds->setup(Celini::dbInstance(),$query,
			array('company' => 'Company','program'=>'Program','payer_type'=>'Payer Type','fee_schedule'=>'Fee Schedule'));
		$ds->addDefaultOrderRule('company','DESC',false);
		$ds->addDefaultOrderRule('program','DESC',false);

		return $ds;
	}
	
	function genericList(){
				
		$db = new clniDb();
		$sql = "select insurance_program_id as id, name from insurance_program where 1";
		$result = $db->execute($sql);
		$programList = array();
		while($result && !$result->EOF) {
			$programList [$result->fields['id']] = $result->fields['name'] ;
			$result->MoveNext();
		}


		return $programList;
	}
	
	/**
	 * Creates a list of programs including the company name.
	 *
	 * @access protected
	 */
	function valueList_programs() {
		$tableName = $this->tableName();
		$sql = "
			SELECT
				insurance_program_id,
				CONCAT_WS('->',c.name,ip.name) AS name
			FROM 
				$tableName AS ip
				INNER JOIN company AS c USING(company_id)
			ORDER BY
				c.name,
				ip.name";
		$res = $this->dbHelper->execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['insurance_program_id']] = $res->fields['name'];
			$res->MoveNext();
		}
		return $ret;
	}

	/**
	 * Create a list of programs by program type
	 */
	function getProgramListByType($type) {
		$em =& Celini::enumManagerInstance();
		$typeKey = $em->lookupKey('insurance_program_type', $type);
	
		$tableName = $this->tableName();
		$sql = "
			SELECT
				insurance_program_id,
				CONCAT_WS('->',c.name,ip.name) AS name
			FROM 
				$tableName AS ip
				INNER JOIN company AS c USING(company_id)
			WHERE
				ip.program_type = $typeKey
			ORDER BY
				c.name,
				ip.name";
		$res = $this->dbHelper->execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['insurance_program_id']] = $res->fields['name'];
			$res->MoveNext();
		}
		return($ret);
	}
	
	function value_fullname() {
		$sql = "
		SELECT CONCAT(c.name,'->',ip.name) AS name
		FROM
			insurance_program AS ip
			LEFT JOIN company AS c ON(ip.company_id=c.company_id)
		WHERE
			ip.insurance_program_id=".$this->dbHelper->quote($this->get('id'));
		$res = $this->dbHelper->execute($sql);
		return $res->fields['name'];
	}
}
?>
