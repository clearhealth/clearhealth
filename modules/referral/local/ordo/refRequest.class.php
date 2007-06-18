<?php

class refRequest extends ORDataObject
{
	/**#@+
	 * Properties / table columns of the this ordo
	 *
	 * @access private
	 * @see ORDataObject::get()
	 */
	var $refRequest_id = '';
	var $date          = '';
	
	/**#@+
	 * References to external objects
	 *
	 * @var int
	 */
	var $visit_id       = '';
	var $refPractice_id = ''; 
	var $refprogram_id  = '';
	var $patient_id = '';
	var $initiator_id = '';
	var $refappointment_id = '';
	var $referral_serivce = '';
	
	/**#@+
	 * All enum values
	 *
	 * @var int
	 */
	var $refSpecialty      = '';
	var $refRequested_day  = '';
	var $refRequested_time = '';
	var $refStatus         = 'Requested / Eligibility Pending';
	var $transportation    = '-1';
	var $translator        = '-1';
	/**#@- */
	
	/**#@+
	 * Text properties
	 */
	var $eligibility = '';
	var $history = '';
	var $reason  = '';
	var $notes   = '';
	/**#@- */
	
	/**
	 * Eligibility date
	 */
	var $eligible_thru = '';
	
	/**#@- */
	
	var $defaultValueValue = '';
	
	/**
	 * Internal configuration values
	 */
	var $_table = 'refRequest';
	var $_enumManager = null;

	var $_enumList = array(
		'refStatus' => 'refStatus',
		'refSpecialty' => 'refSpecialty',
		'eligibility'  => 'refEligibility'
	);
	
	var $_internalName = 'refRequest';
	var $_key = 'refRequest_id';
	
	function refRequest($db = null) {
		parent::ORDataObject($db);
		$this->_enumManager =& new EnumManager(); 
	}
	
	function setup($id = 0) {
		if ($id > 0) {
			$this->set('id', $id);
			$this->populate();
		}
		else {
			$this->set('date', date('Y-m-d'));
		}
	}
	
	function setupMostRecentByPatientAndSpecialty($patient_id, $refSpecialty) {
		$tableName = $this->tableName();
		$qPatientId = $this->dbHelper->quote($patient_id);
		$qRefSpecialty = $this->dbHelper->quote($refSpecialty);
		$sql = "
			SELECT 
				*
			FROM
				{$tableName}
			WHERE
				patient_id = {$qPatientId} AND
				refSpecialty = {$qRefSpecialty}
			ORDER BY 
				date DESC
			LIMIT 1";
		
		$this->helper->populateFromQuery($this, $sql);
	}
	
	/**#@+
	 * Alias accessor/mutator for refRequest_id value.
	 *
	 * @access protected
	 */
	function set_id($value) {
		$this->set('refRequest_id', $value);
	}
	
	function get_id() {
		return $this->get('refRequest_id');
	}
	
	/**#@- */
	
	function get_transportation() {
		if ($this->_inPersist) {
			return $this->transportation;
		}
		
		return $this->_enumManager->lookup('yesNo', $this->transportation);
	}
	
	function get_transportation_raw() {
		return $this->transportation;
	}
	
	function get_translator() { 
		if ($this->_inPersist) {
			return $this->translator;
		}
		return $this->_enumManager->lookup('yesNo', $this->translator);
	}
	
	function get_translator_raw() {
		return $this->translator;
	}
	
	function get_refRequested_day() {
		//if ($this->_inPersist) {
			return $this->refRequested_day;
		//}
		//return $this->_enumManager->lookup('days', $this->refRequested_day);
	}
	
	function get_refRequested_time() {
		//if ($this->_inPersist) {
			return $this->refRequested_time;
		//}
		//return $this->_enumManager->lookup('refRequested_time', $this->refRequested_time);
	}
	
	
	/**
	 * @internal This 'extra' functionality needs to be replaced with the standard value method
	 * @access protected
	 */
	function get_refSpecialty() {
		if ($this->_inPersist) {
			return $this->refSpecialty;
		}
		return $this->_enumManager->lookup('refSpecialty', $this->refSpecialty);
	}

	function get_rawSpecialty() {
			return $this->refSpecialty;
	}
	
	/**
	 * @internal Only need to be in place as long as {@link get_refSpecialty()} has the extra
	 *     lookup in it.
	 * @access protected
	 */
	 function value_refSpecialty() {
		 return $this->_enumManager->lookup('refSpecialty', $this->refSpecialty);
	 }
		
	/**
	 * Shorthand for loading a refVisit based off of visit_id
	 *
	 * DEPRECATED: Use getChild('refVisit')
	 * @deprecated
	 */
	function &get_visit() {
		
	}
	
	function get_eligible_thru() {
		return $this->_getDate('eligible_thru');
	}
	
	function set_eligible_thru($value) {
		$this->_setDate('eligible_thru', $value);
	}
	
	function get_date() {
		return $this->_getDate('date');
	}
	
	function set_date($value) {
		$this->_setDate('date', $value);
	}
	
	/**
	 * Shorthand for loading a refInitiator
	 */
	function &get_initiator() {
		if ($this->get('initiator_id') == 0) {
			$return = false;
			return $return;
		}
		
		$initiator =& Celini::newORDO('Person', $this->get('initiator_id'));
		return $initiator;
	}
	
	
	/**
	 * Returns a {@link refAppointment} associated with this.
	 *
	 * If there is no {@link refAppointment} associated with this referral request, the ORDO will be
	 * empty.
	 *
	 * @return refAppointment
	 */
	function &getChild_refAppointment() {
		$appointment =& Celini::newORDO('refAppointment', $this->get('id'), 'ByRequest');
		return $appointment;
	}
	
	/**
	 * Returns the {@link refPatient} associate with this
	 *
	 * @return refPatient
	 */
	function &getChild_refPatient() {
		$patient =& Celini::newORDO('refPatient', $this->get('patient_id'));
		return $patient;
	}
	
	/**
	 * Returns a {@link refVisit} associated with this
	 *
	 * @return refVisit
	 */
	function &getChild_refVisit() {
		$visit =& Celini::newORDO('refVisit', $this->get('visit_id'));
		return $visit;
	}
	
	
	function value($key) {
		$value = parent::value($key);
		if (empty($value) || $value == $this->_unknownMessage) {
			return $this->defaultValueValue;
		}
		return $value;
	}
}

