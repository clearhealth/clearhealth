<?php
/**
 * Object Relational Persistence Mapping Class for table: encounter_dates
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
 * Object Relational Persistence Mapping Class for table: encounter_date
 *
 * @package	com.uversainc.clearhealth
 */
class EncounterDate extends ORDataObject {

	/**#@+
	 * Fields of table: encounter_date mapped to class members
	 */
	var $id			= '';
	var $encounter_id	= '';
	var $date_type		= '';
	var $date		= '';
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function EncounterDate($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'encounter_date';
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
		parent::populate('encounter_date_id');
	}

	/**#@+
	 * Getters and Setters for Table: encounter_date
	 */

	
	/**
	 * Getter for Primary Key: encounter_date_id
	 */
	function get_encounter_date_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: encounter_date_id
	 */
	function set_encounter_date_id($id)  {
		$this->id = $id;
	}

	function set_date($date) {
		$this->date = $this->_mysqlDate($date);
	}

	/**#@-*/

	function encounterDateList($encounter_id) {
		settype($encounter_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "encounter_date_id, date_format(`date`,'%Y-%m-%d') `date`, date_type",
				'from' 	=> "$this->_table ",
				'where' => " encounter_id = $encounter_id"
			),
			array('date' => 'Date','date_type' => 'Title')
		);

		$ds->registerFilter('date_type',array(&$this,'lookupDateType'));
		return $ds;
	}

	/**#@+
	 * Enumeration getters
	 */
	function getDateTypeList() {
		$list = $this->_load_enum('encounter_date_type',false);
		return array_flip($list);
	}
	/**#@-*/

	var $_edCache = false;
	/**
	 * Cached lookup for date_type
	 */
	function lookupDateType($id) {
		if ($this->_edCache === false) {
			$this->_edCache = $this->getDateTypeList();
		}
		if (isset($this->_edCache[$id])) {
			return $this->_edCache[$id];
		}
	}
}
?>
