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


}
