<?php
/**
 * Object Relational Persistence Mapping Class for table: insurance_program
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
 * Object Relational Persistence Mapping Class for table: insurance_program
 *
 * @package	com.uversainc.clearhealth
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
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function InsuranceProgram($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'insurance_program';
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
		$company =& ORDataObject::Factory('Company',$this->get('company_id'));
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

	function programList() {
		$sql = "select insurance_program_id, concat_ws('->',c.name,ip.name) as name from $this->_table ip inner join company c using(company_id) order by c.name, ip.name";
		$res = $this->_execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['insurance_program_id']] = $res->fields['name'];
			$res->MoveNext();
		}
		return $ret;
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
}
?>
