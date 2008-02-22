<?php
/**
 * Object Relational Persistence Mapping Class for table: enumeration_definition
 *
 * @package	com.clear-health.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Object Relational Persistence Mapping Class for table: enumeration_definition
 *
 * @package	com.clear-health.celini
 */
class EnumerationDefinition extends ORDataObject {

	/**#@+
	 * Fields of table: enumeration_definition mapped to class members
	 */
	var $enumeration_id = '';
	var $name	= '';
	var $title	= '';
	var $type	= 'Default';
	/**#@-*/


	/**
	 * Hack to allow this object to properly map to the file name since PHP 4 
	 * doesn't maintain case for classes.
	 *
	 * @var string
	 * @access protected
	 */
	var $_internalName = 'EnumerationDefinition';
	
	
	/**
	 * {@inheritdoc}
	 */
	var $_key = 'enumeration_id';
	
	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function EnumerationDefinition($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'enumeration_definition';
		$this->_sequence_name = 'seq_enum';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of EnumerationDefinition with this
	 */
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}
	
	function setupByName($name) {
		$this->set('name', $name);
		parent::populate('name');
	}

	/**
	 * Create and Populate an instance by name
	 */
	function &fromName($name) {
		$enum =& ORDataObject::factory('EnumerationDefinition');
		$res = $enum->_execute("select enumeration_id from {$enum->_prefix}{$enum->_table} where name = ".$enum->_quote($name));
		if ($res && !$res->EOF) {
			$enum->setup($res->fields['enumeration_id']);
		}
		return $enum;
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('enumeration_id');
	}
}
?>
