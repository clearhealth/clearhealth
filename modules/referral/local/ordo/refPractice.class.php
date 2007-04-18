<?php

require_once CELINI_ROOT . '/ordo/ORDataObject.class.php';
require_once dirname(__FILE__) . '/../includes/refSpecialtyMapper.class.php';

class refPractice extends ORDataObject
{
	var $refPractice_id = '';
	var $name = '';
	var $assign_by = 'Practice';
	var $default_num_of_slots = '5';
	var $status = 0;
	
	var $storage_metadata = array(
		'int' => array(
			'refprogram_id' => ''), 
		'date' => array(),
		'string' => array(
			'services'     => '',
			'restrictions' => '')
	);

	var $_specialties = array();
	var $_specialtyKeys = array();
	var $_table = 'refpractice';
	var $_key = 'refPractice_id';
	var $_internalName = 'refPractice';
	
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id',(int) $id);
			$this->populate();
		}
	}
	
	function populate() {
		parent::populate('refPractice_id');
	}
	
	function persist() {
		parent::persist();
		
		//printf('<pre>%s</pre>', var_export($this->_specialtyKeys , true));
		if (count($this->_specialtyKeys) > 0) {
			$mapper =& new refSpecialtyMapper();
			$mapper->persist($this, $this->_specialtyKeys);
		}
	}
	
	/**#@+
	 * Accessor/mutator methods
	 *
	 * @access protected
	 */
	function get_id() {
		return $this->get('refPractice_id');
	}
	
	function set_id($value) {
		$this->set('refPractice_id', $value);
	}
	
	function get_specialties() {
		$this->_initSpecialties();
		return $this->_specialties;
	}
	
	function set_specialties($value) {
		$this->_specialtyKeys = (array)$value;
	}
	
	function get_specialty_ids() {
		return array_keys($this->get('specialties'));
	}
	
	function set_name($value) {
		$this->name = str_replace("'", "", $value);
	}
	
	/**#@-*/
	
	
	/**
	 * Return an {@link ORDOCollection} of the various providers of this controller
	 *
	 * @return ORDOCollection
	 */
	function &getChildren_providers() {
		$criteria = 'refPractice_id = ' . $this->dbHelper->quote($this->get('refPractice_id'));
		$finder =& new ORDOFinder('refProvider', $criteria);
		$collection =&  $finder->find();
		return $collection;
		
	}
	
	
	/**
	 * Return an array of id => name to be used as part of a drop down
	 *
	 * @return array
	 */
	function keyNameArray() {
		$returnArray = array();
		$sql = "SELECT refpractice_id AS id, name FROM {$this->_table} WHERE assign_by = 'Practice'";
		$recordSet = $this->_db->Execute($sql);
		while (!$recordSet->EOF) {
			$returnArray[$recordSet->fields['id']] = $recordSet->fields['name'];
			$recordSet->moveNext();
		}
		
		return $returnArray;
	}
	
	/**#@+
	 * @todo remove
	 */
	function _initSpecialties() {
		$mapper =& new refSpecialtyMapper();
		$this->_specialties = $mapper->find($this);
	}
	/**#@-*/
}

