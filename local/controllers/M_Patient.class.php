<?php
/**
 * @package	com.uversainc.freestand
 */

require_once CELLINI_ROOT."/ordo/ORDataObject.class.php";

/**
 * Patient Manager
 */
class M_Patient extends Manager {

	/**
	 * Handle an update from an edit or an add
	 */
	function process_update($id =0) {

		$patient =& ORdataObject::factory('Patient',$id);
		$patient->populate_array($_POST['person']);

		$patient->persist();
		$this->controller->patient_id = $patient->get('id');

		/*
		ORDataObject::factory_include('User');
		$u = new User();
		$user =& $u->fromPersonId($this->controller->patient_id);
		$user->populate_array($_POST['user']);
		$user->set('person_id',$this->controller->patient_id);

		//map patient type to the matchng user group

		$user->persist();
		 */
		if ($id == 0) {
			$this->messages->addMessage('Patient Created');
		}
		else {
			$this->messages->addmessage('Patient Update');
		}

		$t_list = $patient->getTypeList();
		$types = $patient->get('types');

		/*
		if (count($types) > 0) {
			$type = array_shift($types);
			if ($type > 0) {
				$group = strtolower(str_replace(' ','_',$t_list[$type]));
				$gacl_groups = $this->controller->security->sort_groups();
				$user->groups = array();
				foreach($gacl_groups[10] as $id => $name) {
					$data = $this->controller->security->get_group_data($id);
					if ($data[2] == $group) {
						$gid = $data[0];
						$user->groups[$gid] = array('id'=>$data[0]);
						$user->persist();
						break;
					}
				}
			}
		}
		*/

		// handle sub actions that are submitted with the main one
		if (isset($_POST['number'])) {
			$this->process_phone_update($this->controller->patient_id,$_POST['number']);
		}
		if (isset($_POST['address'])) {
			$this->process_address_update($this->controller->patient_id,$_POST['address']);
		}
		if (isset($_POST['identifier'])) {
			$this->process_identifier_update($this->controller->patient_id,$_POST['identifier']);
		}
		if (isset($_POST['insuredRelationship'])) {
			$this->process_insuredRelationship_update($this->controller->patient_id,$_POST['insuredRelationship']);
		}
		if (isset($_POST['personPerson'])) {
			$this->process_personPerson_update($this->controller->patient_id,$_POST['personPerson']);
		}
		if (isset($_POST['patientStatistics'])) {
			$this->process_patientStatistics_update($this->controller->patient_statistics_id,$_POST['patientStatistics']);
		}
	}

	/**
	 * Handle updating a phone #
	 */
	function process_phone_update($patient_id,$data) {
		
		if (!empty($data['number']) || !empty($data['notes'])) {
			$id = 0;
			if (isset($data['number_id']) && !isset($data['add_as_new'])) {
				$id = $data['number_id'];
			}
			else {
				unset($data['number_id']);
			}
			$number =& ORDataObject::factory('PersonNumber',$id,$patient_id);
			$number->populate_array($data);
			$number->persist();
			$this->controller->number_id = $number->get('id');

			$this->messages->addMessage('Number Updated');
		}
	}

	/**
	 * Handle updating an identifier 
	 */
	function process_identifier_update($patient_id,$data) {
		if (!empty($data['identifier'])) {
			$id = (int)$data['identifier_id'];
			$identifier =& ORDataObject::factory('Identifier',$id,$patient_id);
			$identifier->populate_array($data);
			$identifier->persist();
			$this->controller->identifier_id = $identifier->get('id');

			$this->messages->addMessage('Secondary Identifier Updated');
		}
	}
	/**
	 * Handle updating a relationship
	 */
	function process_personPerson_update($patient_id,$data) {
		if (!empty($data['related_person_id'])) {
			$id = (int)$data['person_person_id'];
			$identifier =& ORDataObject::factory('PersonPerson',$id,$patient_id);
			$identifier->populate_array($data);
			$identifier->persist();
			$this->controller->person_person_id = $identifier->get('id');

			$this->messages->addMessage('Relationship Updated');
		}
	}
	
	/**
	 * Handle updating patient statistics
	 */
	function process_patientStatistics_update($patient_id,$data) {
		if (count($data) > 0) {
			$id = (int)$data['patient_statistics_id'];
			$patientStatistics =& ORDataObject::factory('PatientStatistics',$id,$patient_id);
			$patientStatistics->populate_array($data);
			$patientStatistics->persist();
			$this->controller->patient_statistics_id = $patientStatistics->get('id');

			$this->messages->addMessage('Statistics Updated');
		}
	}

	/**
	 * Handle updating an insurer relationship 
	 */
	function process_insuredRelationship_update($patient_id,$data) {
		if (!empty($data['group_name']) || !empty($data['group_number'])) {
			$id = (int)$data['insured_relationship_id'];
			$ir =& ORDataObject::factory('InsuredRelationship',$id,$patient_id);
			$ir->populate_array($data);
			$ir->persist();
			$this->controller->insured_relationship_id = $ir->get('id');

			$this->messages->addMessage('Insurer Updated');
		}
	}


	/**
	 * Handle updating an address
	 */
	function process_address_update($patient_id,$data) {
		$process = false;
		foreach($data as $key => $val) {
			if ($key !== 'add_as_new' && $key !== "state") {
				if (!empty($val)) {
					$process = true;
					break;
				}
			}
		}
		if ($process) {
			$id = 0;
			if (isset($data['address_id']) && !isset($data['add_as_new'])) {
				$id = $data['address_id'];
			}
			else {
				unset($data['address_id']);
			}
			$number =& ORDataObject::factory('PersonAddress',$id,$patient_id);
			$number->populate_array($data);
			$number->persist();
			$this->controller->address_id = $number->get('id');

			$this->messages->addMessage('Address Updated');
		}
	}

	/**
	 * Setup for editing a person relationship
	 */
	function process_editPersonPerson($patient_id,$person_person_id) {
		$this->controller->person_person_id = $person_person_id;
	}

	/**
	 * Setup for editing a phone number
	 */
	function process_editNumber($patient_id,$number_id) {
		$this->controller->number_id = $number_id;
	}

	/**
	 * Setup for editing an identifier
	 */
	function process_editIdentifier($patient_id,$identifier_id) {
		$this->controller->identifier_id = $identifier_id;
	}

	/**
	 * Setup for editing an encounter date
	 */
	function process_editEncounterDate($patient_id,$encounter_date_id) {
		$this->controller->encounter_date_id = $encounter_date_id;
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
	 * Approve an patient
	 */
	function process_approve($patient_id) {
		$patient =& ORDataObject::factory('Patient',$patient_id);
		$patient->approve();
		$this->messages->addmessage('Patient Approved');
		$this->process_enable($patient_id);
	}

	/**
	 * Enable an patient login
	 */
	function process_enable($patient_id) {
		ORDataObject::factory_include('User');
		$user =& User::fromPersonId($patient_id);
		$user->enable();
		$this->messages->addmessage('Patient Login Enabled');
	}

	/**
	 * Disable an patient
	 */
	function process_disable($patient_id) {
		ORDataObject::factory_include('User');
		$user =& User::fromPersonId($patient_id);
		$user->disable();
		$this->messages->addmessage('Patient Login Disabled');
	}

	/**
	 * Delete a number
	 */
	function process_deleteNumber($patient_id,$number_id) {
		$number =& ORDataObject::factory('PersonNumber',$number_id,$patient_id);
		$number->drop();
		$this->messages->addmessage('Number Deleted');
	}

	/**
	 * Setup for editing an address
	 */
	function process_editAddress($patient_id,$address_id) {
		$this->controller->address_id = $address_id;
	}

	/**
	 * Delete an address
	 */
	function process_deleteAddress($patient_id,$address_id) {
		$address =& ORDataObject::factory('PersonAddress',$address_id,$patient_id);
		$address->drop();
		$this->messages->addmessage('Address Deleted');
	}

	/**
	 * Process a complaint
	 */
	function process_complaint($patient_id) {
		$complaint =& ORDataObject::factory('complaint');
		$complaint->populate_array($_POST['complaint']);
		$complaint->persist();
	}

	
	/**
	 * Delete an identifier
	 */
	function process_deleteIdentifier($patient_id,$identifier_id) {
		$identifier =& ORDataObject::factory('Identifier',$identifier_id,$patient_id);
		$identifier->drop();
		$this->messages->addmessage('Secondary Identifier Deleted');
	}
}
?>
