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
	 * Setup that will load an ORDO based on the program/building combo of a 
	 * given user
	 *
	 * @param int
	 * @param int
	 * @param int
	 */
	function setupByProgramAndBuilding($person_id, $program_id, $building_id) {
		$sql = '
			SELECT 
				*
			FROM
				' . $this->_table . '
			WHERE
				insurance_program_id = ' . (int)$program_id . ' AND
				(building_id = ' . (int)$building_id . ' OR building_id = 0)
			ORDER BY building_id DESC
			LIMIT 1';
		$results =& $this->dbHelper->execute($sql);
		$this->helper->populateFromResults($this, $results);
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
	 * An alias of {@link get_provider_number_type()}
	 *
	 * Apparently, the result of an enum being named one thing, and the value
	 * in the DB being named another.
	 *
	 * @see get_provider_number_type()
	 */
	function get_identifier_type_value() {
		return $this->get('provider_number_type');
	}
	
	/**
	 * Virtual getter to load the provider_number_type's enum value
	 *
	 * @return string
	 * @see lookupProviderNumberType()
	 */
	function get_provider_number_type() {
		return $this->lookupProviderNumberType($this->provider_number_type);
	}
	
	/**
	 * @todo Refractor this into a DS off of the {@link Person} or 
	 *    {@link Provider} ordo
	 */
	function &providerToInsuranceList($person_id) {
		settype($person_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "pToI.provider_to_insurance_id, ip.name, provider_number, provider_number_type, group_number, b.name AS building_name, '{$person_id}' AS person_id",
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
		$ds->registerFilter('provider_number', array(&$this, '_addLink'));
		return $ds;
	}

	function getProviderNumberTypeList() {
		$em =& new EnumManager();
		return $em->enumArray('identifier_type');
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
	
	
	/**
	 * Adds a link to the given value.
	 *
	 * This is a registered filter of {@link providerToInsuranceList()}.
	 *
	 * @param  string|int
	 * @param  row
	 * @access private
	 *
	 * @todo Make this actually work.  Will need to append "&process=true" to
	 *    the end of the URL, and will need to adjust the process code to
	 *    properly handle displaying the field for editing.
	 * @todo Make this utilize {@link Celini::managerLink()} for continuity.
	 */
	function _addLink($value, $row) {
		return $value;
		return sprintf('<a href="%s">%s</a>',
			Celini::link('edit', 'user', true, $row['person_id'], 'editProviderToInsurance') . 'id=' . $row['provider_to_insurance_id'],
			$value);
	}
}
?>
