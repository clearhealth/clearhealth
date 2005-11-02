<?php
/**
 * Object Relational Persistence Mapping Class for table: provider_to_insurance
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
 * Object Relational Persistence Mapping Class for table: provider_to_insurance
 *
 * @package	com.uversainc.clearhealth
 */
class ProviderToInsurance extends ORDataObject {

	/**#@+
	 * Fields of table: provider_to_insurance mapped to class members
	 */
	var $id				= '';
	var $person_id			= '';
	var $insurance_program_id	= '';
	var $provider_number		= '';
	var $provider_number_type	= '';
	var $group_number		= '';
	var $building_id = '';
	/**#@-*/

	var $_typeCache = false;


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function ProviderToInsurance($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'provider_to_insurance';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Provider_to_insurance with this
	 */
	function setup($id = 0,$person_id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
		if ($person_id > 0) {
			$this->set('person_id',$person_id);
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('provider_to_insurance_id');
	}

	/**#@+
	 * Getters and Setters for Table: provider_to_insurance
	 */

	
	/**
	 * Getter for Primary Key: provider_to_insurance_id
	 */
	function get_provider_to_insurance_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: provider_to_insurance_id
	 */
	function set_provider_to_insurance_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	
	/**
	 * @todo Refractor this into a DS off of the {@link Person} ordo
	 */
	function providerToInsuranceList($person_id) {
		settype($person_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "ip.name, provider_number, provider_number_type, group_number, b.name AS building_name",
				'from' 	=> "
					$this->_table AS pToI
					INNER JOIN insurance_program AS ip USING(insurance_program_id)
					LEFT JOIN buildings AS b ON(pToI.building_id = b.id)",
				'where' => " person_id = $person_id"
			),
			array(
				'name' => 'Name',
				'provider_number' => 'Provider Number', 
				'group_number' => 'Group Number',
				'building_name' => 'Building Name'
			)
		);

		$ds->registerFilter('provider_number_type',array(&$this,'lookupProviderNumberType'));
		return $ds;
	}

	function getProviderNumberTypeList() {
		$list = $this->_load_enum('identifier_type',false);
		return array_flip($list);
	}

	/**
	 * Cached lookup for identifier_type
	 */
	function lookupProviderNumberType($type_id) {
		if ($this->_typeCache === false) {
			$this->_typeCache = $this->getProviderNumberTypeList();
		}
		if (isset($this->_typeCache[$type_id])) {
			return $this->_typeCache[$type_id];
		}
	}
}
?>
