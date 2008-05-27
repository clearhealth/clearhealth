<?php
/**
 * Object Relational Persistence Mapping Class for table: visit_queue
 *
 * @package	com.clear-health.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**
 * Relationships:
 * Parent: Provider
 * Parent: VisitQueueTemplate
 * Children: Appointment
 * Children: Patient
 *
 */
class VisitQueue extends ORDataObject {

	/**#@+
	 * Fields of table: visit_queue mapped to class members
	 */
	var $visit_queue_id		= '';
	var $visit_queue_template_id		= '';
	var $provider_id		= '';
	/**#@-*/
	
	var $_appointments = null;

	var $_template = null;

	/**
	 * DB Table
	 */
	var $_table = 'visit_queue';

	/**
	 * Primary Key
	 */
	var $_key = 'visit_queue_id';
	
	/**
	 * Internal Name
	 */
	var $_internalName = 'VisitQueue';
	
	var $_foreignKeyList = array('patient_id'=>'Patient');

	/**
	 * Handle instantiation
	 */
	function VisitQueue() {
		parent::ORDataObject();
	}
	
	function get_title() {
		$temp =& $this->getTemplate();
		return $temp->get('title');
	}
	
	function get_number_of_appointments() {
		$temp =& $this->getTemplate();
		return $temp->get('number_of_appointments');
	}
	
	function getReasons() {
		$temp =& $this->getTemplate();
		return $temp->getReasons();
	}
	
	function getPatients() {
		return $this->getChildren('Patient');
	}

	function &getProvider() {
		$provider =& Celini::newORDO('Provider',$this->get('provider_id'));
		return $provider;
	}
	
	function &getTemplate() {
		//if(!is_null($this->_template)) return $this->_template;
		$temp =& Celini::newORDO('VisitQueueTemplate',$this->get('visit_queue_template_id'));
		$this->_template =& $temp;
		return $temp;
	}
	
	function getNumAppointments() {
		$template =& $this->getTemplate();
		return $template->get('number_of_appointments');
	}
	
	function populateAppointments() {
		if(is_null($this->_appointments)) {
			$finder =& $this->relationshipFinder();
			$finder->setRelatedType('Appointment');
			$finder->addParent($this);
			$finder->_joins = "LEFT JOIN relationship AE ON AE.parent_type='CalendarEvent' AND AE.child_type='Appointment' AND child_id=appointment.appointment_id LEFT JOIN event ON event.event_id = AE.parent_id";
			$finder->_orderBy = "event.start ASC";
			$this->_appointments = $finder->find();
		}
	}
	
	function getAppointmentsScheduled() {
		$this->populateAppointments();
		return $this->_appointments;
	}
	
	function isOld() {
		$this->populateAppointments();
		if($this->missingAppointments() == true) {
			return false;
		}
		$apts =& $this->_appointments;
		$old = true;
		for($apts->rewind();$apts->valid();$apts->next()) {
			$apt =& $apts->current();
			if(strtotime($apt->get('start_time')) > time()) {
				$old = false;
			}
		}
		return $old;
	}
	
	function missingAppointments() {
		$this->populateAppointments();
		$template =& $this->getTemplate();
		if($this->_appointments->count() < $template->get('number_of_appointments')) {
			return true;
		}
		return false;
	}
	
	function valueList_title() {
		$db =& Celini::dbInstance();
		$sql = "SELECT id,title FROM $this->_table ORDER BY title ASC";
		$res = $db->execute($sql);
		$out = array();
		while($res && !$res->EOF) {
			$out[$res->fields['id']] = $res->fields['title'];
		}
		return $out;
	}
	
	function set_patient_id($id){
		if($this->get('id') < 1) { $this->persist();}
		$p =& $this->getChild('Patient');
		if($id !== $p->get('id')) {
			
			$p =& Celini::newORDO('Patient',$id);
			$this->removeRelationship('Patient');
			$this->setChild($p);
		}
	}
	
	function get_patient_id() {
		$p =& $this->getChild('Patient');
		return $p->get('id');
	}
}
?>
