<?php
/**
 * Object Relational Persistence Mapping Class for table: encounter
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

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
	var $created_by_user_id		= '';
	/**#@-*/
	var $_erCache = false;

	var $storage_metadata = array(
		'int' => array('current_payer'=>'','payment_plan'=>''), 
		'date' => array(),
		'string' => array()
	);
	var $_table = 'encounter';
	var $_key = 'encounter_id';
	var $_internalName = 'Encounter';

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Encounter($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';
		$this->set('date_of_treatment', date("Y-m-d"));
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

	function persist() {
		$me =& Me::getInstance();
		
		if ($this->get('id') == 0) {
			$this->set('created_by_user_id',$me->get_id());
		}
		$this->set('last_change_user_id',$me->get_id());
		parent::persist();
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

	function get_current_payer() {
		if($this->get('payment_plan') > 0) {
			return 'plan'.$this->get('payment_plan');
		}
		return $this->_int_storage->get('current_payer');
	}
	
	function set_current_payer($id) {
		if(strpos($id,'plan') !== false) {
			$this->_int_storage->set('current_payer',$id);
		} else {
			(int)$id=str_replace('plan','',$id);
			$this->_int_storage->set('payment_plan',$id);
		}
	}
	
	function set_date_of_treatment($date) {
		$this->_setDate('date_of_treatment', $date);
	}
	
	/**
	 * Returns date of treatment as an English date instead of ISO
	 *
	 * @return string
	 * @access protected
	 */
	function get_date_of_treatment() {
		return $this->_getDate('date_of_treatment');
	}

	function get_encounter_reason_print() {
		return $this->lookupEncounterReason($this->get('encounter_reason'));
	}

	function get_facility_name() {
		$b =& ORDataObject::factory('Building',$this->get('building_id'));
		return $b->get('name');
	}

	function get_treating_person_print() {
		$p =& ORDataObject::factory('Person',$this->get('treating_person_id'));
		return $p->get('last_name').', '.$p->get('first_name');
	}

	function get_appointment_print() {
		if (!$this->get('occurence_id')) {
			return '';
		}
		$o =& ORDataObject::factory('Occurence',$this->get('occurence_id'));
		return $o->get('date').' '.$o->get('start_time').' '.$o->get('building_name').' -> '.$o->get('location_name') .' '.$o->get('user_display_name');

	}



	/**#@-*/

	
	function encounterList($patient_id) {
		settype($patient_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> sprintf("date_format(date_of_treatment,'%s') AS date_of_treatment, encounter_reason, b.name building, concat_ws(' ',p.first_name,p.last_name) treating_person, status, encounter_id", DateObject::getFormat()),
				'from' 	=> "$this->_table e left join buildings b on b.id = e.building_id left join person p on e.treating_person_id = p.person_id",
				'where' => " patient_id = $patient_id",
				'orderby' => 'date_of_treatment DESC'
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
		$sql = "
			SELECT 
				b.name AS building_name, 
				b.id AS building_id,
				r.name AS room_name, 
				r.id AS room_id,
				o.id AS occurence_id, 
				concat_ws(' ',psn.first_name,psn.last_name) AS provider_name,
				pvds.person_id AS provider_id, 
				o.start AS appointment_start 
			FROM 
				occurences AS o 
				LEFT JOIN rooms AS r ON (r.id = o.location_id)
				LEFT JOIN buildings AS b ON (b.id = r.building_id)
				LEFT JOIN user AS u ON (o.user_id = u.user_id)
				LEFT JOIN provider AS pvds ON (pvds.person_id = u.person_id)
				LEFT JOIN person AS psn ON (psn.person_id = pvds.person_id)
				LEFT JOIN group_occurence AS go ON (o.id = go.occurence_id)
			WHERE
				o.external_id = " . (int)$this->get("patient_id") . " OR 
				go.patient_id = ". (int)$this->get("patient_id") . "		
			ORDER BY 
				o.start DESC
			LIMIT 10";
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
		$sql = sprintf("select date_format(min(date_of_treatment),'%s') date_of_treatment from $this->_table where patient_id = $patient_id", DateObject::getFormat());

		$res = $this->_execute($sql);
		if ($res && !$res->EOF) {
			$d = $res->fields['date_of_treatment'];
			if (!empty($d)) {
				return $d;
			}
		}
		return "No Encounters";
	}

	// static
	function encounterIdFromAppointmentId($appointmentId) {
		settype($appointmentId,'int');
		$en =& ORDataObject::factory('Encounter');
		$sql = "select encounter_id from $en->_table where occurence_id = $appointmentId";
		$res = $en->_execute($sql);
		if ($res && !$res->EOF) {
			$id = $res->fields['encounter_id'];
			return $id;
		}
		return 0;
	}
}
?>
