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
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
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
	var $x12sender		= '';
	var $x12reciever	= '';
	var $x12version_string	= '';
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
		$sql = "select insurance_program_id, concat_ws('->',c.name,ip.name) as name from $this->_table ip inner join company c using(company_id)";
		$res = $this->_execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[$res->fields['insurance_program_id']] = $res->fields['name'];
			$res->MoveNext();
		}
		return $ret;
	}

	function detailedProgramList($company_id) {
		settype($company_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "ip.name, payer_type, fsd.label as fee_schedule_name, insurance_program_id",
				'from' 	=> "$this->_table ip left join fee_schedule fsd using (fee_schedule_id)",
				'where' => " company_id = $company_id"
			),
			array('name' => 'Program Name','payer_type' => 'Payer Type', 'fee_schedule_name' => 'Fee Schedule'));

		$ds->registerFilter('payer_type',array(&$this,'lookupPayerType'));
		return $ds;
	}

	function toArray() {
		$ret = array();
		$ret['name'] = $this->get('name');
		$ret['payer_type'] = $this->lookupPayerType($this->get('payer_type'));
		$company =& ORDataObject::factory('Company',$this->get('company_id'));
		$ret['company'] = $company->toArray();
		return $ret;
	}
}
?>
