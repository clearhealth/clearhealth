<?php
/**
 * Object Relational Persistence Mapping Class for table: person_person
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: person_person
 *
 * @package	com.uversainc.clearhealth
 */
class PersonPerson extends ORDataObject {

	/**#@+
	 * Fields of table: person_person mapped to class members
	 */
	var $person_person_id		= '';
	var $person_id			= '';
	var $related_person_id		= '';
	var $relation_type		= '';
	var $guarantor			= 0;
	var $guarantor_priority		= false;
	/**#@-*/

	var $_typeCache = false;
	var $_table = 'person_person';
	var $_internalName='PersonPerson';

	/**
	 * 
	 * Primary key.
	 * 
	 * @var string
	 * 
	 */
	var $_key = 'person_person_id';
    
	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function PersonPerson($db = null) {
		parent::ORDataObject($db);	
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

	
	/**#@-*/

	/**#@+
	 * Enumeration getters
	 */
	function getRelationTypeList() {
		$list = $this->_load_enum('person_to_person_relation_type',false);
		return array_flip($list);
	}

	function get_relatedDisplayName() {
		$p =& Celini::newOrdo('Patient',$this->get('related_person_id'));
		return $p->get('search_name');
	}

	function get_hasGuarantor() {
		$sql = "select max(guarantor) g from ".$this->tableName()." where person_id = ".$this->get('person_id');
		$res = $this->dbHelper->execute($sql);
		if (!$res->EOF && $res->fields['g'] == 1) {
			return 1;
		}
		return 0;
	}

	function get_guarantorPerson() {
		$id = $this->get('person_id');
		if ($this->get_hasGuarantor()) {
			$sql = "select related_person_id id from ".$this->tableName()." where person_id = ".$this->get('person_id')." order by guarantor_priority ASC limit 1";
			$res = $this->dbHelper->execute($sql);
			if (!$res->EOF) {
				$id = $res->fields['id'];
			}
		}
		$ret =& Celini::newOrdo('Person',$id);
		return $ret;
	}

	function get_nextGuarantorPriority() {
		$sql = "select max(guarantor_priority) g from ".$this->tableName()." where person_id = ".$this->get('person_id');
		$res = $this->dbHelper->execute($sql);
		return $res->fields['g']+1;
	}

	function get_guarantor_priority() {
		if ($this->guarantor_priority === false || $this->guarantor_priority == 9999) {
			$this->set('guarantor_priority',$this->get('nextGuarantorPriority'));
		}
		else if ($this->guarantor_priority == -1) {
			$this->set('guarantor_priority',0);
			$this->pushPrioritiesUpOne();
		}

		return $this->guarantor_priority;
	}
	/**#@-*/

	function pushPrioritiesUpOne() {
		$sql = "update ".$this->tableName()." set guarantor_priority = guarantor_priority+1 where person_id = ".$this->get('person_id');
		$this->dbHelper->execute($sql);
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
