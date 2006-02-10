<?php
/**
 * Object Relational Persistence Mapping Class for table: patient_statistics
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
 * Object Relational Persistence Mapping Class for table: patient_statistics
 *
 * @package	com.uversainc.clearhealth
 */
class PatientStatistics extends ORDataObject {

	/**#@+
	 * Fields of table: patient_statistics mapped to class members
	 */
	var $id			= '';
	var $ethnicity		= '';
	var $race		= '';
	var $income		= '';
	var $language		= '';
	var $migrant_status	= '';
	var $registration_location = '';
	var $sign_in_date	= '';
	var $monthly_income	= '';
	var $family_size	= '';
	/**#@-*/

	/**#@+
	 * {@inheritdoc}
	 */
	var $_enumList = array('ethnicity', 'race', 'income', 'language', 'migrant_status');
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
	 * @todo Deprecate this
	 */
	function getEthnicityList() {
		$list = $this->_load_enum('ethnicity');
		return array_flip($list);
	}
	function getRaceList() {
		$list = $this->_load_enum('race');
		return array_flip($list);
	}
	function getIncomeList() {
		$list = $this->_load_enum('income');
		return array_flip($list);
	}
	function getLanguageList() {
		$list = $this->_load_enum('language');
		return array_flip($list);
	}
	function getMigrantStatusList() {
		$list = $this->_load_enum('migrant_status');
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
	
	function lookupMigrantStatusType($id) {
		if (isset($this->_edCache['migrant_status'])) {
			$this->_edCache['migrant_status'] = $this->getMigrantStatusList();
		}
		if (isset($this->_edCache['migrant_status'][$id])) {
			return $this->_edCache['migrant_status'][$id];
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
	
	
	/**
	 * Setter for sign_in_date.  Insures date is stored in ISO format
	 */
	function set_sign_in_date($date) {
		$this->_setDate('sign_in_date', $date);
	}
	
	
	/**
	 * Getter for sign_in_date.  Insures date is displayed in USA format
	 *
	 * @return string
	 */
	function get_sign_in_date() {
		return $this->_getDate('sign_in_date');
	}
	/**#@-*/
	
	
	/**#@+
	 * Value accessors for various properties
	 *
	 * @return string
	 */
	
	function value_registration_location() {
		$ordo =& Celini::newORDO('Building', $this->get('registration_location'));
		return $ordo->get('name');
	}
	/**#@-*/

}
?>
