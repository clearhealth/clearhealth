<?php
/**
 * @package	com.uversainc.clearhealth
 */

/**
 * Patient Manager
 */
class M_Patient extends Manager {

	var $messageType = "Patient";
	var $similarPatientChecked = false; // dupe checking

	/**
	 * Handle an update from an edit or an add
	 */
	function process_update($id =0,$noPatient = false) {
		// check if the "submit duplicate record anyqay button has been clicked
		$this->similarPatientChecked = isset($_POST['DuplicateChecked']) ;
		$continue = 1 ;
		$duplicate_count = -1 ;

		// hack to test for a patient existing correctly
		$p =& Celini::newOrdo('Patient',$id);
		if ($p->isPopulated()) {
			$noPatient = true;
		}

		if ($noPatient == false) { // if we're adding a patient:
			if (!$this->similarPatientChecked) { // check for duplicates
				$duplicate_count = $this->_checkDuplicatePatient() ;
				$continue = ($duplicate_count <= 0) ;
			}
			// if our dupe count was 0 or if we're overriding the check to submit a new possible dupe, then continue
			if ($duplicate_count <= 0 || $this->similarPatientChecked) {
				$patient =& ORdataObject::factory('Patient',$id);
			}
		} else { // otherwise, edit an existing patient
			$patient =& ORdataObject::factory('Patient',$id);
		}
		if ($continue) {
			$patient->populateArray($_POST['person']);
			$patient->persist();

			$this->controller->patient_id = $patient->get('id');

			if ($id == 0) {
				$this->messages->addMessage($this->messageType.' Created');
			}
			else {
				$this->messages->addmessage($this->messageType.' Updated');
			}

			$t_list = $patient->getTypeList();
			$types = $patient->get('types');

			// handle sub actions that are submitted with the main one
			var_dump($_POST);
			if (isset($_POST['number'])) {
				$this->process_phone_update($this->controller->patient_id,$_POST['number']);
			}
			if (isset($_POST['address'])) {
				$this->process_address_update($this->controller->patient_id,$_POST['address']);
			}
			if (isset($_POST['relatedAddress'])) {
				$this->process_relatedAddress_update($this->controller->patient_id, $_POST['relatedAddress']);
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
				$this->process_patientStatistics_update($this->controller->patient_id,$_POST['patientStatistics']);
			}
			if (isset($_POST['PatientChronicCode'])) {
				$this->process_patientChronicCode_update($this->controller->patient_id,$_POST['PatientChronicCode']);
			}
		}
	}

	function process_patientChronicCode_update($patientId,$data) {
		$changeHappened = false;
		foreach($data as $key => $status) {
			if ($status == 1) {
				$pcc =& Celini::newOrdo('PatientChronicCode',array($patientId,$key));
				if (!$pcc->isPopulated()) {
					$pcc->persist();
					$changeHappened = true;
				}
			}
			else {
				$pcc =& Celini::newOrdo('PatientChronicCode',array($patientId,$key));
				if ($pcc->isPopulated()) {
					$pcc->drop();
					$changeHappened = true;
				}
			}
		}
		if ($changeHappened) {
			$this->messages->addMessage('Chronic Codes Updated');
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
			//$this->controller->person_person_id = $identifier->get('id');

			$this->messages->addMessage('Relationship Updated');
		}
	}
	
	/**
	 * Handle updating patient statistics
	 */
	function process_patientStatistics_update($patient_id,$data) {
		if (count($data) > 0) {
			$patientStatistics =& ORDataObject::factory('PatientStatistics',$patient_id);
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
		if (!empty($data['insurance_program_id']) || !empty($data['group_name']) || !empty($data['group_number'])) {
			$id = (int)$data['insured_relationship_id'];
			$ir =& ORDataObject::factory('InsuredRelationship',$id,$patient_id);
			$ir->populate_array($data);

			if (isset($_POST['searchSubscriber'])) {
				$ir->set('subscriber_id',$_POST['searchSubscriber']['person_id']);
			}
			if (isset($_POST['newSubscriber']) && !empty($_POST['newSubscriber']['last_name'])) {
				$person =& ORDataObject::factory('Person',$_POST['newSubscriber']['person_id']);
				$person->set('type',$person->idFromType('Subscriber'));
				$person->populate_array($_POST['newSubscriber']);
				$person->persist();
				$address =& $person->address();
				$address->populate_array($_POST['newSubscriber']);
				$address->persist();
				$number =& $person->numberByType('Home');
				$number->set('number',$_POST['newSubscriber']['number']);
				$number->persist();

				$ir->set('subscriber_id',$person->get('id'));
			}
			$ir->persist();
			$this->controller->insured_relationship_id = $ir->get('id');

			$this->messages->addMessage('Payer Updated');
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
			$address =& ORDataObject::factory('PersonAddress',$id,$patient_id);
			$address->helper->populateFromArray($address,$data);
			$address->persist();
			$this->controller->address_id = $address->get('id');

			$this->messages->addMessage('Address Updated');
		}
	}
	
	/**
	 * Handle adding a related person's address
	 */
	function process_relatedAddress_update($patient_id, $data) {
		//var_dump($data);
		$message = '';
		foreach ($data as $address) {
			if (!isset($address['person_id'])) {
				continue;
			}
		
			$newAddress =& Celini::newORDO('PersonAddress', array($address['address_id'], $address['person_id']));
			$newAddress->set('address_id', $address['address_id']);
			$newAddress->set('person_id', $address['person_id']);
			$newAddress->persist();
			
			$message = empty($message) ? 'Added Address' : 'Added Addresses';
		}
		
		if (!empty($message)) {
			$this->messages->addMessage($message);
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
	 * Setup for editing an insured relatioship
	 */
	function process_editInsuredRelationship($patient_id,$insured_relationship_id) {
		$this->controller->insured_relationship_id = $insured_relationship_id;
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
	 *
	 * @todo Remove this?  There is no Complaint ORDO
	 */
	function process_complaint($patient_id) {
		$complaint =& ORDataObject::factory('Complaint');
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

	function process_moveInsuredRelationshipDown($patient_id,$insured_relationship_id) {
		$ir =& ORDataObject::factory('InsuredRelationship',$insured_relationship_id);
		$ir->moveDown();
	}
	function process_moveInsuredRelationshipUp($patient_id,$insured_relationship_id) {
		$ir =& ORDataObject::factory('InsuredRelationship',$insured_relationship_id);
		$ir->moveUp();
	}

	function _checkDuplicatePatient() {
		$db =& new clniDB();
		// take $_POST array, and check for existing users in the database
		// last name, first name, ssn, address
		// build a new view template for displaying the data
		$person = $_POST['person'] ;
		$last_name = $person['last_name'] ;
		//$last_name = $db->quote("$last_name") ;
		$first_name = $person['first_name'] ;
		//$first_name = $db->quote("$first_name") ;
		$first_initial = substr($first_name,0,1) ;
		$gender = $person['gender'] ; // 1=Male,2=Female
		$dob = $person['date_of_birth'] ;
		
		$duplicates = Array() ;
		
		$sql = "
			SELECT 
				person_id,
				last_name,
				first_name,
				middle_name,
				gender,
				date_of_birth,
				identifier,
				identifier_type
			FROM 
				person
			WHERE 
				(
					(
						last_name like '$last_name' OR 
						last_name like '$last_name%'
					)
					AND 
					(
						first_name like '$first_name' OR 
						first_name like '$first_name%' OR 
						(SUBSTRING(first_name,1,1) like '$first_initial' AND gender='$gender') OR 
						date_of_birth='$dob'
					)
				)
			ORDER BY 
				last_name,
				first_name,
				middle_name,
				date_of_birth
			" ;
		$res = $db->execute($sql);
		$duplicate_count = 0 ;
		while($res && !$res->EOF) {
			$duplicates[$duplicate_count] =  $res->fields ;
			$duplicates[$duplicate_count]['edit_url'] = Celini::link('view','PatientDashboard')."id=".$res->fields['person_id'];
			$duplicate_count++ ;
			$res->MoveNext();
		}
		$this->controller->assign_by_ref('DuplicateList',$duplicates) ;

		// rebuild $_POST as person[last_name] elements again
		$newPost = Array() ;
		foreach ($_POST as $key => $value) {
			$keystr = $key ;
			$valstr = $value ;
			if (is_array($value)) {
				foreach ($value as $key2 => $value2) {
					$keystr = "$key".'['.$key2.']' ;
					$valstr = $value2 ;
					$newPost[$keystr] = $valstr ;
				}
			} else {
				$newPost[$keystr] = $valstr ;
			}
		}
		$this->controller->assign_by_ref('OriginalPost',$newPost) ;
		$this->similarPatientChecked = true ;
		$this->controller->assign_by_ref('duplicate_count',$duplicate_count) ;
		return $duplicate_count ;
	}
}
?>
