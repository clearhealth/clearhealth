<?php

class M_Encounter extends Manager {
	/**
	 * Setup for editing an encounter date
	 */
	function process_editEncounterDate($patient_id,$encounter_date_id) {
		$this->controller->encounter_date_id = $encounter_date_id;
	}

	/**
	 * Setup for editing an encounter value
	 */
	function process_editEncounterValue($patient_id,$encounter_value_id) {
		$this->controller->encounter_value_id = $encounter_value_id;
	}

	/**
	 * Setup for editing an encounter person
	 */
	function process_editEncounterPerson($patient_id,$encounter_person_id) {
		$this->controller->encounter_person_id = $encounter_person_id;
	}
	
	/**
	 * Setup for editing a payment
	 */
	function process_editPayment($patient_id,$payment_id) {
		$this->controller->payment_id = $payment_id;
	}

	/**
	 * Process a note
	 */
	function process_note($patient_id) {
		$note_id = $_POST['note']['note_id'];
		$note =& ORDataObject::factory('PatientNote',$note_id);
		$note->populate_array($_POST['note']);

		if ($note_id == 0) {
			$note->set('user_id',$this->controller->_me->get_user_id());
			$note->set('note_date',date('Y-m-d H:i:s'));
		}

		$note->set('patient_id',$patient_id);
		$note->persist();
		$this->controller->note_id = $note->get('id');
	}
	function process_depnote($patient_id,$note_id,$current) {
		$note =& ORDataObject::factory('PatientNote',$note_id);

		if ($current == 'Yes') {
			$note->set('deprecated',0);
		}
		else {
			$note->set('deprecated',1);
		}

		$note->persist();
	}
}
