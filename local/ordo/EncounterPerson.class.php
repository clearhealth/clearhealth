<?php
/**
 * Object Relational Persistence Mapping Class for table: encounter_person
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
 * Object Relational Persistence Mapping Class for table: encounter_person
 *
 * @package	com.uversainc.clearhealth
 */
class EncounterPerson extends ORDataObject {

	/**#@+
	 * Fields of table: encounter_person mapped to class members
	 */
	var $id			= '';
	var $encounter_id	= '';
	var $person_type	= '';
	var $person_id		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function EncounterPerson($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'encounter_person';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Encounter_date with this
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
		parent::populate('encounter_person_id');
	}

	/**#@+
	 * Getters and Setters for Table: encounter_person
	 */

	
	/**
	 * Getter for Primary Key: encounter_person_id
	 */
	function get_encounter_person_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: encounter_person_id
	 */
	function set_encounter_person_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	function encounterPersonList($encounter_id) {
		settype($encounter_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "encounter_person_id, concat_ws(' ',first_name,last_name) person, person_type",
				'from' 	=> "$this->_table inner join person using(person_id)",
				'where' => " encounter_id = $encounter_id"
			),
			array('person' => 'Person','person_type' => 'Title')
		);

		$ds->registerFilter('person_type',array(&$this,'lookupPersonType'));
		return $ds;
	}

	/**#@+
	 * Enumeration getters
	 */
	function getPersonTypeList() {
		$list = $this->_load_enum('encounter_person_type',false);
		return array_flip($list);
	}
	/**#@-*/

	var $_edCache = false;
	/**
	 * Cached lookup for person_type
	 */
	function lookupPersonType($id) {
		if ($this->_edCache === false) {
			$this->_edCache = $this->getPersonTypeList();
		}
		if (isset($this->_edCache[$id])) {
			return $this->_edCache[$id];
		}
	}
}
?>
