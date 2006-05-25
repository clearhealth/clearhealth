<?php
/**
 * Object Relational Persistence Mapping Class for table: identifier
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
$loader->requireOnce('includes/Datasource_sql.class.php');
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: identifier
 *
 * @package	com.uversainc.clearhealth
 */
class Identifier extends ORDataObject {

	/**#@+
	 * Fields of table: identifier mapped to class members
	 */
	var $id			= '';
	var $person_id		= '';
	var $identifier		= '';
	var $identifier_type	= '';
	/**#@-*/

	var $_typeCache = false;

	var $_table = 'identifier';
	var $_internalName='Identifier';

	/**
	 * 
	 * Primary key.
	 * 
	 * @var string
	 * 
	 */
    var $_key = 'identifier_id';
    
	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Identifier($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Person_identifier with this
	 */
	function setup($id = 0,$person_id = false) {
		if ($person_id !== false) {
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
		parent::populate('identifier_id');
	}

	/**#@+
	 * Getters and Setters for Table: identifier
	 */

	
	/**
	 * Getter for Primary Key: identifier_id
	 */
	function get_identifier_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: identifier_id
	 */
	function set_identifier_id($id)  {
		$this->id = $id;
	}

	/**#@-*/

	/**#@+
	 * Enumeration getters
	 */
	function getIdentifierTypeList() {
		$list = $this->_load_enum('identifier_type',false);
		return array_flip($list);
	}
	/**#@-*/

	/**
	 * Get a ds with all the identifiers for a given person_id
	 */
	function &identifierList($person_id) {
		settype($person_id,'int');
		
		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "identifier_id, identifier, identifier_type",
				'from' 	=> "$this->_table",
				'where'	=> "person_id = $person_id",
			),
			array('identifier' => 'Identifier', 'identifier_type' => 'Type'));
		$ds->registerFilter('identifier_type',array(&$this,'lookupType'));
		return $ds;
	}

	/**
	 * Cached lookup for identifier_type
	 */
	function lookupType($type_id) {
		if ($this->_typeCache === false) {
			$this->_typeCache = $this->getIdentifierTypeList();
		}
		if (isset($this->_typeCache[$type_id])) {
			return $this->_typeCache[$type_id];
		}
	}
}
?>
