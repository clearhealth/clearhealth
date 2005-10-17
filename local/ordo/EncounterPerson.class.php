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
require_once CELINI_ROOT.'/ordo/ORDataObject.class.php';
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

	function get_person() {
		if (!empty($this->person_id)) {
			$person =& ORDataObject::factory('Person',$this->person_id);
			return $person->get('last_name').', '.$person->get('first_name').' ('.$person->lookupType($person->get('type')).')';
		}
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


	/**#@-*/
	function encounterPersonListArray($encounter_id) {

		settype($encounter_id,'int');

		$sql = "select encounter_person.person_id, encounter_person_id, concat_ws(' ',first_name,last_name) person, person_type from $this->_table inner join person using(person_id) where encounter_id = $encounter_id";
		$res = $this->_execute($sql);
		$ret = array();
		while($res && !$res->EOF) {
			$ret[]=$res->fields['encounter_person_id'];
			$res->MoveNext();
		}
		return $ret;

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
	
	function personTypeName() {
		$sql = "SELECT person_type from person_type where person_id = " . $this->_quote($this->get("person_id"));
		$res = $this->_execute($sql);
		$ptl = $this->_load_enum("person_type",false);
		$ptl = array_flip($ptl);
		if ($res && !$res->EOF) {
			$pt_id = $res->fields['person_type'];
			$type_class_name = $ptl[$pt_id];
			return $type_class_name;
		}
		return false;
	}
}
?>
