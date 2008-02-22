<?php
/**
 * Object Relational Persistence Mapping Class for table: encounter_values
 *
 * @package	com.clear-health.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */


/**
 * Object Relational Persistence Mapping Class for table: encounter_value
 *
 * @package	com.clear-health.clearhealth
 */
class EncounterValue extends ORDataObject {

	/**#@+
	 * Fields of table: encounter_value mapped to class members
	 */
	var $id			= '';
	var $encounter_id	= '';
	var $value_type		= '';
	var $value		= '';
	/**#@-*/
	var $_table = 'encounter_value';
	var $_internalName='EncounterValue';


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function EncounterValue($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Encounter_value with this
	 */
	function setup($id = 0,$encounter_id = 0) {
		if ($encounter_id > 0) {
			$this->set('encounter_id',$encounter_id);
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
		parent::populate('encounter_value_id');
	}

	/**#@+
	 * Getters and Setters for Table: encounter_value
	 */

	
	/**
	 * Getter for Primary Key: encounter_value_id
	 */
	function get_encounter_value_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: encounter_value_id
	 */
	function set_encounter_value_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	function encounterValueList($encounter_id) {
		settype($encounter_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "encounter_value_id, `value`, value_type",
				'from' 	=> "$this->_table ",
				'where' => " encounter_id = $encounter_id"
			),
			array('value' => 'Value','value_type' => 'Title')
		);

		$ds->registerFilter('value_type',array(&$this,'lookupValueType'));
		return $ds;
	}


	function encounterValueListArray($encounter_id) {

		settype($encounter_id,'int');

		$sql = "select encounter_value_id, `value`, value_type from $this->_table where encounter_id = $encounter_id";
		$res = $this->_execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[]=$res->fields['encounter_value_id'];
			$res->MoveNext();
		}
		return $ret;

	}





	/**#@+
	 * Enumeration getters
	 */
	function getValueTypeList() {
		$enumManager =& EnumManager::getInstance();
		return $enumManager->enumArray('encounter_value_type');
	}
	/**#@-*/

	var $_edCache = false;
	/**
	 * Cached lookup for value_type
	 */
	function lookupValueType($id) {
		if ($this->_edCache === false) {
			$this->_edCache = $this->getValueTypeList();
		}
		if (isset($this->_edCache[$id])) {
			return $this->_edCache[$id];
		}
	}
}
?>
