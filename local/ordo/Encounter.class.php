<?php
/**
 * Object Relational Persistence Mapping Class for table: encounter
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
 * Object Relational Persistence Mapping Class for table: encounter
 *
 * @package	com.uversainc.clearhealth
 */
class Encounter extends ORDataObject {

	/**#@+
	 * Fields of table: encounter mapped to class members
	 */
	var $id				= '';
	var $encounter_reason		= '';
	var $patient_id			= '';
	var $building_id		= '';
	var $date_of_treatment		= '';
	var $treating_person_id		= '';
	//var $timestamp			= '';
	var $last_change_user_id	= '';
	var $status			= 'open';
	var $occurence_id		= '';
	var $_erCache = false;
	/**#@-*/

	var $storage_metadata = array(
		'int' => array('current_payer'=>''), 
		'date' => array(),
		'string' => array()
	);

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Encounter($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'encounter';
		$this->_sequence_name = 'sequences';
		$this->date_of_treatment = date("Y-m-d");	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Encounter with this
	 */
	function setup($id = 0,$patient_id=0) {
		if ($patient_id > 0) {
			$this->set('patient_id',$patient_id);
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
		parent::populate('encounter_id');
	}

	/**#@+
	 * Getters and Setters for Table: encounter
	 */

	
	/**
	 * Getter for Primary Key: encounter_id
	 */
	function get_encounter_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: encounter_id
	 */
	function set_encounter_id($id)  {
		$this->id = $id;
	}

	function set_date_of_treatment($date) {
		$this->date_of_treatment = $this->_mysqlDate($date);
	}

	/**#@-*/

	
	function encounterList($patient_id) {
		settype($patient_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "date_format(date_of_treatment,'%Y-%m-%d') date_of_treatment, encounter_reason, b.name building, concat_ws(' ',p.first_name,p.last_name) treating_person, status, encounter_id",
				'from' 	=> "$this->_table e left join buildings b on b.id = e.building_id left join person p on e.treating_person_id = p.person_id",
				'where' => " patient_id = $patient_id"
			),
			array('date_of_treatment' => 'Date of Treatment','encounter_reason' => 'Reason', 'building' => 'Building', 'treating_person' => 'Treated By', 'status' => 'Status'/*,'encounter_id' => "Encounter Id"*/));

		$ds->orderHints['building'] = 'b.name';
		$ds->registerFilter('encounter_reason',array(&$this,'lookupEncounterReason'));
		//echo $ds->preview();
		return $ds;
	}

	/**#@+
	 * Enumeration getters
	 */
	function getEncounterReasonList() {
		$list = $this->_load_enum('encounter_reason',false);
		return array_flip($list);
	}
	/**#@-*/

	
	/**
	 * Cached lookup for encounter_reason
	 */
	function lookupEncounterReason($id) {
		if ($this->_erCache === false) {
			$this->_erCache = $this->getEncounterReasonList();
		}
		if (isset($this->_erCache[$id])) {
			return $this->_erCache[$id];
		}
	}
	
	function appointmentList_remoting($occurence_id,$patient_id) {
		$this->set("patient_id",(int)$patient_id);
		$ar = $this->appointmentList();
		if (isset($ar[(int)$occurence_id])) return $ar[(int)$occurence_id];
		return array();
	}
	
	function appointmentList() {
		$sql = "select b.name as building_name, b.id as building_id, r.name as room_name, r.id as room_id, o.id as occurence_id, concat_ws(' ',psn.first_name,psn.last_name) as provider_name, pvds.person_id as provider_id, "
				." o.start as appointment_start "
				." from occurences o " 
				." inner join rooms r on r.id = o.location_id "
				." inner join buildings b on b.id = r.building_id"
				." left join user u on o.user_id = u.user_id"
				." left join provider as pvds on pvds.person_id = u.person_id"
				." left join person psn on psn.person_id = pvds.person_id"
				." where o.external_id = " . (int)$this->get("patient_id")
				." order by o.start DESC limit 10";
		$result = $this->_execute($sql);
		$ar = array();
		while ($result && !$result->EOF) {
			$ar[$result->fields['occurence_id']] = $result->fields;
			$result->MoveNext();
		}
		return $ar;	
	}

	function getFirstDate($patient_id) {
		settype($patient_id,'int');
		$sql = "select date_format(min(date_of_treatment),'%Y-%m-%d') date_of_treatment from $this->_table where patient_id = $patient_id";

		$res = $this->_execute($sql);
		if ($res && !$res->EOF) {
			$d = $res->fields['date_of_treatment'];
			if (!empty($d)) {
				return $d;
			}
		}
		return "No Encounters";
	}
}
?>
