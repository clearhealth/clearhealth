<?php
/**
 * Object Relational Persistence Mapping Class for table: patient_statistics
 *
 * @package	com.uversainc.freestand
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: patient_statistics
 *
 * @package	com.uversainc.freestand
 */
class PatientStatistics extends ORDataObject {

	/**#@+
	 * Fields of table: patient_statistics mapped to class members
	 */
	var $id		= '';
	var $ethnicity		= '';
	var $race		= '';
	var $income		= '';
	var $language		= '';
	var $migrant		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function PatientStatistics($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'patient_statistics';
		$this->_sequence_name = 'sequences';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Patient_statistics with this
	 */
	function setup($id = 0) {
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
	 * Enumeration getters
	 */
	function getEthnicityList() {
		$list = $this->_load_enum('ethnicity',false);
		return array_flip($list);
	}
	function getRaceList() {
		$list = $this->_load_enum('race',false);
		return array_flip($list);
	}
	function getIncomeList() {
		$list = $this->_load_enum('income',false);
		return array_flip($list);
	}
	function getLanguageList() {
		$list = $this->_load_enum('language',false);
		return array_flip($list);
	}
	function getMigrantList() {
		$list = $this->_load_enum('migrant',false);
		return array_flip($list);
	}
	/**#@-*/

	var $_edCache = array();
	/**
	 * Cached lookup for date_type
	 */
	function lookupEthnicityType($id) {
		if (isset($this->_edCache['ethnicity'])) {
			$this->_edCache['ethnicity'] = $this->getEthnicityList();
		}
		if (isset($this->_edCache['ethnicity'][$id])) {
			return $this->_edCache['ethnicity'][$id];
		}
	}
	
	function lookupRaceType($id) {
		if (isset($this->_edCache['race'])) {
			$this->_edCache['race'] = $this->getRaceList();
		}
		if (isset($this->_edCache['race'][$id])) {
			return $this->_edCache['race'][$id];
		}
	}
	
	function lookupIncomeType($id) {
		if (isset($this->_edCache['income'])) {
			$this->_edCache['income'] = $this->getIncomeList();
		}
		if (isset($this->_edCache['income'][$id])) {
			return $this->_edCache['income'][$id];
		}
	}
	
	function lookupLanguageType($id) {
		if (isset($this->_edCache['language'])) {
			$this->_edCache['language'] = $this->getLanguageList();
		}
		if (isset($this->_edCache['language'][$id])) {
			return $this->_edCache['language'][$id];
		}
	}
	
	function lookupMigrantType($id) {
		if (isset($this->_edCache['migrant'])) {
			$this->_edCache['migrant'] = $this->getMigrantList();
		}
		if (isset($this->_edCache['migrant'][$id])) {
			return $this->_edCache['migrant'][$id];
		}
	}	


	/**#@+
	 * Getters and Setters for Table: patient_statistics
	 */

	
	/**
	 * Getter for Primary Key: person_id
	 */
	function get_person_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: person_id
	 */
	function set_person_id($id)  {
		$this->id = $id;
	}

	/**#@-*/
}
?>
