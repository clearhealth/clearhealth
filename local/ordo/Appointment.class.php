<?php
/**
 * Object Relational Persistence Mapping Class for table: appointment
 * 
 * Relationships:
 * Parent:	Event
 * Child (through event):	Patient
 * Parent (through event):	Room
 * Parent (through event):	Provider
 * Parent:	User (creator)
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */
class Appointment extends ORDataObject {

	/**#@+
	 * Fields of table: appointment mapped to class members
	 */
	var $appointment_id	= '';
	var $title		= '';
	var $reason		= '';
	var $walkin		= '';
	var $group_appointment	= '';
	var $has_secondary	= '';
	var $created_date	= '';
	var $last_change_id	= '';
	var $last_change_date	= '';
	var $creator_id		= '';
	var $room_id		= '';
	var $practice_id	= '';
	var $provider_id	= '';
	var $patient_id		= '';
	var $event_id		= '';
	var $appointment_code	= ''; // NS/CAN/???
	/**#@-*/

	var $_date		= '';


	/**
	 * DB Table
	 */
	var $_table = 'appointment';

	/**
	 * Primary Key
	 */
	var $_key = 'appointment_id';

	/** 
	 * Cased name
	 */
	var $_internalName = 'Appointment';

	var $_event = false;
	var $_patient = false;

	/**
	 * Internal Name
	 */
	var $_internalName='Appointment';
	
	var $_foreignKeyList = array('queue_id'=>'VisitQueue','qreason_id'=>'VisitQueueReason');

	/**
	 * Handle instantiation
	 */
	function Appointment() {
		parent::ORDataObject();
	}

	function setup($id = 0) {
		$sql = 'select event_id, a.* from '.$this->tableName().' a where appointment_id = '.enforceType::int($id);
		$this->helper->populateFromQuery($this,$sql);
	}

	function setupByEventId($id) {
		$sql = 'select * from '.$this->tableName().' where event_id = '.enforceType::int($id);
		$this->helper->populateFromQuery($this,$sql);
	}

	/**
	 * Map date from the event
	 */
	function get_date() {
		$this->populateEvent();
		return date('Y-m-d',strtotime($this->_event->get('date')));
	}
	
	function get_USAdate() {
		$this->populateEvent();
		return date('m/d/Y',strtotime($this->_event->get('date')));
	}

	function set_date($d) {
		$this->populateEvent();
		$this->_date = $d;
		if(strtotime($this->_event->get('start') != '' ? $this->_event->get('start') : 0) > 0) {
			$this->_event->set('start',$this->_date.' '.$this->_event->get('start'));
		}
		if(strtotime($this->_event->get('end') != '' ? $this->_event->get('end') : 0) > 0) {
			$this->_event->set('end',$this->_date.' '.$this->_event->get('end'));
		}
	}

	/**
	 * Map start_time from the event
	 */
	function get_start_time() {
		$this->populateEvent();
		return $this->_event->get('start_time',date('Y-m-d H:i:s'));
	}

	function set_start_time($t) {
		$this->populateEvent();
		if($this->_date != '') {
			$date = $this->_date;
		} else {
			$date = date('Y-m-d',strtotime($this->_event->get('start',date('Y-m-d'))));
		}
		$this->_event->set('start',$date.' '.$t);
	}

	/**
	 * Map end_time from the event
	 */
	function get_end_time() {
		$this->populateEvent();
		return $this->_event->get('end_time');
	}

	function set_end_time($t) {
		$this->populateEvent();
		if($this->_date != '') {
			$date = $this->_date;
		} else {
			$date = date('Y-m-d',strtotime($this->_event->get('end',date('Y-m-d'))));
		}
		$this->_event->set('end',$date.' '.$t);
	}

	function get_created_date() {
		if ($this->get('id') == 0 || $this->created_date === '') {
			$this->_setDate('created_date',date('Y-m-d H:i:s'));
		}
		return $this->_getTimestamp('created_date');
	}

	function set_created_date($d) {
		$this->_setDate('created_date',$d);
	}

	function get_last_change_date() {
		if ($this->_inPersist && $this->metadata->isModified()) {
			$this->_setDate('last_change_date',date('Y-m-d H:i:s'));
		}
		return $this->_getTimestamp('last_change_date');
	}
	function value_last_change_date() {
		return $this->_getTimestamp('last_change_date');
	}

	function set_last_change_date($d) {
		$this->_setDate('last_change_date',$d);
	}

	function get_last_change_id() {
		if ($this->_inPersist) {
			$me =& Me::getInstance();
			$this->last_change_id = $me->get_id();
		}
		return $this->last_change_id;
	}
	
	function get_last_change_nickname() {
		return $this->_getNick($this->get('last_change_id'));
	}

	function _getNick($id) {
		$sql = "select username, nickname from user where user_id = $id";
		$res = $this->dbHelper->execute($sql);
		if (!empty($res->fields['nickname'])) {
			return $res->fields['nickname'];
		}
		return $res->fields['username'];
	}

	function get_creator_id() {
		if($this->creator_id < 1) {
			$me =& Me::getInstance();
			$this->set('creator_id', $me->get_id());
		}
		return $this->creator_id;
	}
	
	function get_creator_nick() {
		return $this->_getNick($this->get('creator_id'));
	}
	
	function set_title($title) {
		$this->populateEvent();
		$this->_event->set('title',$title);
		$this->title = $title;
	}
	
	function eventGet($id) {
		$this->populateEvent();
		return $this->_event->get($id);
	}

	function eventSet($id,$value) {
		$this->populateEvent();
		$this->_event->set($id,$value);
	}
	/**
	 * Get the event that is linked to this appointment
	 */
	function populateEvent() {
		if ($this->_event === false) {
			$this->_event =& Celini::newOrdo('CalendarEvent',$this->get('event_id'));
		}
	}

	function valueList_reason() {
		return $this->_listEnum('appointment_reasons');
	}
	
	function value_reason() {
		$list = $this->_listEnum('appointment_reasons');
		return $list[$this->get('reason')];
	}

	function valueList_provider() {
		$pro =& Celini::newOrdo('Provider');
		return $pro->valueList_usernamePersonId();
	}

	function value_patient_id() {
		$pat =& Celini::newOrdo('Patient',$this->get('patient_id'));
		return $pat->value('patient');
	}

	function value_room_id() {
		$room =& Celini::newOrdo('Room',$this->get('room_id'));
		return $room->value('fullname');
	}

	function get_practice_id() {
		if (empty($this->practice_id) && $this->get('room_id') > 0) {
			$sql = "select practice_id from rooms r inner join buildings b on r.building_id = b.id where r.id = "
					.enforceType::int($this->get('room_id'));
			$this->practice_id = $this->dbHelper->getOne($sql);
		}
		return $this->practice_id;
	}

	function persist() {
		$addcreator = false;
		if($this->get('id') < 1) $addcreator = true;
		$creator =& $this->getParent('User');
		$this->populateEvent();

		$eid = $this->_event->get('id');
		$this->_event->persist();
		if ($eid < 1) {
			$this->set('event_id',$this->_event->get('id'));
		}
		parent::persist();
		if($addcreator) {
			$me =& Me::getInstance();
			$this->set('creator_id', $me->get_id());
		} else {
			$me =& Me::getInstance();
			$this->set('last_changed_id', $me->get_id());
		}
	}
	
	function drop() {
		$this->populateEvent();
		$this->_event->drop();
		parent::drop();
	}
	
	function &getQueue() {
		$q =& $this->getParent('VisitQueue');
		return $q;
	}
	
	function get_queue_id() {
		$queue =& $this->getQueue();
		return $queue->get('id');
	}
	
	function set_queue_id($id) {
		if($this->get('id') < 1) $this->persist();
		$queue =& $this->getQueue();
		if($id != $queue->get('id')) {
			$this->removeRelationship('VisitQueue',$queue->get('id'));
			$q =& Celini::newORDO('VisitQueue',$id);
			$this->setParent($q);
		}
	}
	
	function get_qreason_id() {
		$r =& $this->getParent('VisitQueueReason');
		return $r->get('id');
	}
	
	function set_qreason_id($id) {
		if($this->get('id') < 1) $this->persist();
		$r =& $this->getParent('VisitQueueReason');
		if($r->get('id') != $id) {
			$this->removeRelationship('VisitQueueReason',$r->get('id'));
			$r =& Celini::newORDO('VisitQueueReason',$id);
			$this->setParent($r);
		}
	}
	
	/**
	 * Returns results
	 *
	 * @param string $where
	 * @return unknown
	 */
	function &search($where) {
		$db =& Celini::dbInstance();
//		$db->debug = true;
		$sql = "
			SELECT DISTINCT appointment.appointment_id,appointment.last_change_id,group_appointment,
			event.event_id, event.title AS event_title, event.start AS event_start, event.end AS event_end,
			DATE_FORMAT(event.start,'%m/%d/%Y') AS apt_date,
			prov.person_id as provider_id, 
			provuser.username, 
			pat.last_name, pat.first_name,pat.person_id as patient_id, pat.date_of_birth,
			patnumber.number as phone,
			patient.record_number,
			DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(pat.date_of_birth, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(pat.date_of_birth, '00-%m-%d')) AS age, 
			appointment_code,
			enumeration_value.value AS reason,
			creator.nickname,
			appointment.created_date, appointment.last_change_date,
			last_user.nickname AS last_change_nickname,
			rooms.id AS room_id,
			balance

			FROM 
			event 
			LEFT JOIN appointment on appointment.event_id = event.event_id
			LEFT JOIN person AS prov ON appointment.provider_id = prov.person_id
			LEFT JOIN person AS pat ON appointment.patient_id = pat.person_id
			LEFT JOIN patient ON pat.person_id = patient.person_id
			LEFT JOIN user AS provuser ON prov.person_id = provuser.person_id
			JOIN enumeration_definition ON enumeration_definition.name = 'appointment_reasons'
			JOIN enumeration_value ON enumeration_value.enumeration_id = enumeration_definition.enumeration_id AND enumeration_value.key = appointment.reason AND extra1 = ''
			LEFT JOIN person_number ON pat.person_id = person_number.person_id
			LEFT JOIN number AS patnumber ON person_number.number_id = patnumber.number_id
			LEFT JOIN user AS creator ON appointment.creator_id = creator.user_id
			LEFT JOIN user AS last_user ON appointment.last_change_id = last_user.user_id
			LEFT JOIN rooms ON appointment.room_id = rooms.id
/* balance subquery */
			LEFT JOIN (
				select
					feeData.patient_id,charge,
					(ifnull(credit,0.00) + ifnull(coPay.amount,0.00)) credit,
					(charge - (ifnull(credit,0.00)+ifnull(coPay.amount,0.00))) balance
				from
				(
					select
						e.patient_id,
						sum(cd.fee) charge
					from
						encounter e
						inner join clearhealth_claim cc using(encounter_id)
						inner join coding_data cd on e.encounter_id = cd.foreign_id and cd.parent_id = 0
					group by
						e.patient_id
				) feeData
				left join
				(
					select
						e.patient_id,
						(sum(pl.paid) + sum(pl.writeoff)) credit
					from
						encounter e
						inner join clearhealth_claim cc using(encounter_id)
						inner join payment p on cc.claim_id = p.foreign_id
						inner join payment_claimline pl on p.payment_id = pl.payment_id
					group by
						e.patient_id
				) paymentData on feeData.patient_id = paymentData.patient_id
				left join
				(
					select
						p.foreign_id patient_id,
						sum(p.amount) amount
					from
						payment p
					where encounter_id = 0
					group by
						p.foreign_id
				) coPay on feeData.patient_id = coPay.patient_id
			) baldata ON baldata.patient_id = pat.person_id
			$where
			ORDER BY event.start
		";
		$res = $db->execute($sql);
		return $res;
	}

	function hasEncounter() {
		$sql = "select encounter_id from encounter where occurence_id = ".enforceType::int($this->get('id'));
		if($this->dbHelper->getOne($sql)) {
			return true;
		}
		return false;
	}

	function patientValue($key) {
		if ($this->_patient == false) {
			$this->_patient =& Celini::newOrdo('Patient',$this->get('patient_id'));
		}
		return $this->_patient->value($key);
	}

	function value_appointment_code() {
		switch($this->get('appointment_code')) {
			case 'CAN':
				return '<b>CANCELLED</b>';
			case 'NS':
				return '<b>NO SHOW</b>';
		}
	}
	
	/**
	 * Checks for no-show appointments directly before the current one
	 *
	 * @param int $number number of previous appointments to check
	 * @return int
	 */
	function checkNoShows($number=3,$returndates = false) {
		$db =& Celini::dbInstance();
		$sql = "SELECT appointment.appointment_id, appointment.appointment_code, event.start
		FROM appointment
		JOIN event ON event.event_id=appointment.event_id
		WHERE appointment.patient_id='".$this->get('patient_id')."'
		AND event.start < ".$db->quote($this->get('date').' '.$this->get('start_time'))."
		ORDER BY event.start DESC";
		$noshows = 0;
		if($returndates == true) {
			$noshows = array();
		}
		$res = $db->execute($sql);
		if($res && !$res->EOF) {
			for($i=0;$i<$res->numRows();$i++) {
				if($res->fields['appointment_code'] == 'NS') {
					if($returndates == true) {
						$noshows[] = $res->fields['start'];
					}
					$noshows++;
				} else {
					// A break in no-show appointments.
					return $noshows;
				}
				$res->MoveNext();
			}
		}
		return $noshows;
	}
	
	
}
?>