<?php
/**
 * Object Relational Persistence Mapping Class for table: person_person
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
 * Object Relational Persistence Mapping Class for table: person_person
 *
 * @package	com.uversainc.clearhealth
 */
class PersonPerson extends ORDataObject {

	/**#@+
	 * Fields of table: person_person mapped to class members
	 */
	var $id				= '';
	var $person_id			= '';
	var $related_person_id		= '';
	var $relation_type		= '';
	/**#@-*/

	var $_typeCache = false;

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function PersonPerson($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'person_person';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Person_person with this
	 */
	function setup($id = 0,$person_id = 0) {
		if ($person_id > 0) {
			$this->set('person_id',$person_id);
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
		parent::populate('person_id');
	}

	/**#@+
	 * Getters and Setters for Table: person_person
	 */

	
	/**
	 * Getter for Primary Key: person_person_id
	 */
	function get_person_person_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: person_person_id
	 */
	function set_person_person_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	/**#@+
	 * Enumeration getters
	 */
	function getRelationTypeList() {
		$list = $this->_load_enum('person_to_person_relation_type',false);
		return array_flip($list);
	}
	/**#@-*/

	/**
	 * Get a ds with all the related people
	 *
	 * @param	int	$person_id
	 */
	function &relatedList($person_id) {
		settype($person_id,'int');
		
		$ds =& new Datasource_sql();

		$ds->setup($this->_db,array(
			'union' => 
			array(
				array(
				'cols' 	=> "t.person_person_id, concat_ws(' ',p.first_name, p.last_name) left_name, relation_type, concat_ws(' ',r.first_name, r.last_name) right_name",
				'from' 	=> "$this->_table t inner join person p on p.person_id = t.person_id inner join person r on r.person_id = t.related_person_id",
				'where'	=> "t.person_id = $person_id",
				),
				array(
				'cols' 	=> "t.person_person_id, concat_ws(' ',p.first_name, p.last_name) left_name, relation_type, concat_ws(' ',r.first_name, r.last_name) right_name",
				'from' 	=> "$this->_table t inner join person r on p.person_id = t.person_id inner join person p on r.person_id = t.related_person_id",
				'where'	=> "t.related_person_id = $person_id",
				)
			)
		),
		array('left_name' => 'Name', 'relation_type' => 'Relation Of', 'right_name' => 'Name'));

		$ds->registerFilter('relation_type',array(&$this,'lookupRelationType'));
		return $ds;
	}

	/**
	 * Cached lookup for identifier_type
	 */
	function lookupRelationType($type_id) {
		if ($this->_typeCache === false) {
			$this->_typeCache = $this->getRelationTypeList();
		}
		if (isset($this->_typeCache[$type_id])) {
			return $this->_typeCache[$type_id];
		}
	}
}
?>
