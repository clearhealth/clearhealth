<?php
/**
 * Object Relational Persistence Mapping Class for table: encounter
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
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
		'int' => array('current_payer'=>'','payment_plan'=>'','payer_group_id'=>''), 
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
		$a =& ORDataObject::factory('Appointment',$this->get('occurence_id'));
		$room =& ORDataObject::factory('Room',$a->get('room_id'));
		$building =& ORDataObject::factory('Building',$room->get('building_id'));
		$person =& ORDataObject::factory('person',$a->get('patient_id'));
		return $a->get('date').' '.$a->get('start_time').'
'.$building->get('name').' -> '.$room->get('name') .'
'.$person->get('first_name') . ' ' . $person->get("last_name");

	}

	/**
	 * When setting the payer group, we will go ahead and set the 
	 * current_payer to the first payer of that group.
	 *
	 * @param int $payer_group_id
	 */
	function set_payer_group($payer_group_id) {
		if($this->get('payer_group_id') != $payer_group_id) {
			$this->set('payer_group_id',$payer_group_id);
			$pg =& Celini::newORDO('PayerGroup',$payer_group_id);
			$payers = $this->valueList('current_payers');
			$keys = array_keys($payers);
			if(count($keys) > 0) {
				$this->set('current_payer',$keys[0]);
			}
		}
	}
	
	function get_payer_group() {
		return $this->get('payer_group_id');
	}
	
	/**
	 * Returns the id of the next payer of the assigned PayerGroup.
	 * Returns false if at the end of the list.
	 *
	 * @return int|false
	 */
	function get_next_payer_id() {
		$db =& $this->dbHelper;
		$payers = array_keys($this->valueList('current_payers'));
		$payer_order = 0;
		for($i=0;$i<count($payers);$i++) {
			if($payers[$i] == $this->get('current_payer')) {
				$payer_order = $i+1;
			}
		}
		return $payers[$payer_order];
		$res = $db->execute($sql);
		if($res->EOF) {
			return false;
		}
		return $res->fields['insurance_program_id'];
	}

	/**
	 * Creates a list of the payer groups & specific payers available
	 * to this encounter.
	 *
	 * @return array
	 */
	function valueList_payers() {
		$db =& $this->dbHelper;
		$sql = "
		SELECT
			insurance_program_id,CONCAT(co.name,'=>',ip.name) AS program
		FROM
			payer_group AS pg
			INNER JOIN insurance_payergroup AS ipg USING(payer_group_id)
			INNER JOIN insurance_program  AS ip USING(insurance_program_id)
			INNER JOIN insured_relationship AS ir ON(ip.insurance_program_id=ir.insurance_program_id)
			INNER JOIN company AS co USING(company_id)
			INNER JOIN encounter AS e ON(e.patient_id=ir.person_id AND e.encounter_id = ".$db->quote($this->get('id')).")
		WHERE
			ir.active = 1 AND ir.person_id = ".$db->quote($this->get('id'))."
		ORDER BY 
			ipg.`order` ASC,pg.name ASC
		";
		$res = $db->execute($sql);
		$payers = array();
		for($res->MoveFirst();!$res->EOF;$res->MoveNext()) {
			$payers[$res->fields['insurance_program_id']] = $res->fields['program'];
		}
		return $payers;
	}

	/**
	 * Creates a list of the specific payers available
	 * to this encounter depending on its selected PayerGroup
	 *
	 * @return array
	 */
	function valueList_current_payers() {
		$db =& $this->dbHelper;
		$sql = "
		SELECT
			ip.insurance_program_id,CONCAT(co.name,'=>',ip.name) AS program
		FROM
			payer_group AS pg
			INNER JOIN insurance_payergroup AS ipg USING(payer_group_id)
			INNER JOIN insurance_program  AS ip USING(insurance_program_id)
			INNER JOIN insured_relationship AS ir ON(ip.insurance_program_id=ir.insurance_program_id)
			INNER JOIN company AS co USING(company_id)
			INNER JOIN encounter AS e ON(e.patient_id=ir.person_id AND e.encounter_id = ".$db->quote($this->get('id')).")
		WHERE
			ir.active = 1 AND ir.person_id = ".$db->quote($this->get('patient_id'))."
			AND pg.payer_group_id = ".$db->quote($this->get('payer_group_id'))."
		ORDER BY
			ipg.`order` ASC,pg.name ASC
		";
		$res = $db->execute($sql);
		$payers = array();
		for($res->MoveFirst();!$res->EOF;$res->MoveNext()) {
			$payers[$res->fields['insurance_program_id']] = $res->fields['program'];
		}
		return $payers;
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
				a.appointment_id AS occurence_id, 
				concat_ws(' ',pr.first_name,pr.last_name) AS provider_name,
				a.provider_id AS provider_id, 
				e.start AS appointment_start,
				ev.value reason 
			FROM 
				event e
				INNER JOIN appointment a ON e.event_id = a.event_id
				LEFT JOIN rooms as r on a.room_id = r.id
				LEFT JOIN buildings AS b ON (b.id = r.building_id)
				LEFT JOIN person pr on pr.person_id = a.patient_id
				INNER JOIN enumeration_value ev on a.reason = ev.key
				INNER JOIN enumeration_definition ed on ev.enumeration_id = ed.enumeration_id and ed.name = 'appointment_reasons'
			WHERE
				a.patient_id = " . (int)$this->get("patient_id") . "
			ORDER BY 
				e.start DESC
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

	function get_practice_id() {
		$id = EnforceType::int($this->get('building_id'));
		$sql = "select practice_id from buildings b where b.id = $id";
		return $this->dbHelper->GetOne($sql);
	}
}
?>