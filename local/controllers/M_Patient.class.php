<?php
/**
 * @package	com.uversainc.clearhealth
 */
$loader->requireOnce('includes/LockManager.class.php');
$loader->requireOnce('includes/QuickSave.class.php');

/**
 * Patient Manager
 */
class M_Patient extends Manager {

	var $messageType = "Patient";
	var $similarPatientChecked = false; // dupe checking

	function _updateChangesSection(&$section,$name) {
	//var_dump($section);
		foreach($section as $field => $d) {
			if (isset($_POST[$name][$field])) {
				$section[$field]['your_value'] = $_POST[$name][$field];
			}
			else {
				$section[$field]['your_value'] = '';
			}
			//var_dump($_POST[$name][$field],$section[$field]['new_value']);
			if (strcmp($_POST[$name][$field],$section[$field]['new_value']) == 0) {
				unset($section[$field]);
			}
		}
	}

	function lockCheck($id,$noPatient) {
	// lock check
		$lockTimestamp = $this->controller->POST->get('lockTimestamp');

	if (!empty($lockTimestamp)) {

		$changes = array();
		if ($noPatient) {
			$changes['person'] = LockManager::hasOrdoChanged('Person',$id,$lockTimestamp);
		}
		else {
			$changes['person'] = LockManager::hasOrdoChanged('Patient',$id,$lockTimestamp);

			$tmp = LockManager::hasOrdoChanged('Person',$id,$lockTimestamp);
			$changes['person'] = array_merge($changes['person'],$tmp);
		}
		$this->_updateChangesSection($changes['person'],'person');
			//var_dump($changes['person']);

		// custom code needed
		//'relatedAddresss' => array('PersonAddress','address_id'),
		//$_POST['PatientChronicCode']
		//
		//var_dump($_POST);

		// rest of this changes processing is generic and should be movable
		$subOrdos = array(
			'number' => array('Number','number_id'),
			'address' => array('Address','address_id'),
			'identifier' => array('Identifier','identifier_id'),
			'insuredRelationship' => array('InsuredRelationship','insured_relationship_id'),
			'personPerson' => array('PersonPerson','person_person_id'),
			'patientStatistics' => array('PatientStatistics','patient_statics_id'),
			);

		foreach($subOrdos as $key => $info) {
			$ordoName = $info[0];
			$fieldName = $info[1];
			if (isset($_POST[$key][$fieldName]) && !empty($_POST[$key][$fieldName])) {
				$changes[$key] = LockManager::hasOrdoChanged($ordoName,$_POST[$key][$fieldName],$lockTimestamp);
				$this->_updateChangesSection($changes[$key],$key);
			}
		}

		$overlappingChanges = false;
		foreach($changes as $name => $change) {
			if (count($change) > 0) {
				$overlappingChanges = true;
			}
		}
		if ($overlappingChanges) {
			$changes['_POST'] = $_POST;
			LockManager::prepareChangesAlert($changes,$this->controller,$lockTimestamp);
			return true;
		}
		return false;
	}
	}
	/**
	 * Handle an update from an edit or an add
	 * @todo move as much as possible to some place shared
	 */
	function process_update($id =0,$noPatient = false) {
		// Clear quicksaved form
		$qs =& new QuickSave();
		$qs->clearForm('patientGeneralEditForm',$id);

		if ($this->lockCheck($id,$noPatient)) {
			return;
		}
	
		
		// check if the "submit duplicate record anyqay button has been clicked
		$this->similarPatientChecked = isset($_POST['DuplicateChecked']) ;
		$continue = 1 ;
		$duplicate_count = -1 ;

		if ($noPatient) {
			$patient =& Celini::newORDO('Person', $id);
		}
		else {
			// hack to test for a patient existing correctly
			$p =& Celini::newOrdo('Patient',$id);
			if (!$p->isPopulated()) {
				if (!$this->similarPatientChecked) { // check for duplicates
					$duplicate_count = $this->_checkDuplicatePatient() ;
					$continue = ($duplicate_count <= 0) ;
				}
				// if our dupe count was 0 or if we're overriding the check to submit a new possible dupe, then continue
				if ($duplicate_count <= 0 || $this->similarPatientChecked) {
					$patient =& Celini::newORDO('Patient',$id);
				}
			}
			else { // otherwise, edit an existing patient
				$patient =& $p;
			}
		}
		if ($continue) {
			$recNum = $patient->get('record_number');
			$patient->populateArray($_POST['person']);
			if ($recNum != $patient->get('record_number')) {
				if (!$patient->isRecordNumUnique()) {
					$patient->set('record_number','');
					$this->messages->addMessage('Non unique record number, auto generating record a new record number');
				}
			}
			$isChanged = $patient->persist();
			if($isChanged) {
				if ($id == 0) {
					$this->messages->addMessage($this->messageType.' Created');
				}
				else {
					$this->messages->addmessage($this->messageType.' Updated');
				}
			}

			$this->controller->patient_id = $patient->get('id');


			$t_list = $patient->getTypeList();
			$types = $patient->get('types');

			// handle sub actions that are submitted with the main one
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
			
			if (isset($_POST['SecondaryPractice']) && $_POST['SecondaryPractice']['practice_id'] != '') {
				$cSecondaryPractice =& new C_SecondaryPractice();
				$cSecondaryPractice->process($_POST['SecondaryPractice']);
				$action = empty($_POST['SecondaryPractice']['id']) ? 'Added' : 'Updated';
				$this->messages->addMessage("Secondary Practice {$action}");
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
			if ($number->persist()) {
				$this->messages->addMessage('Number Updated');
			}
			$this->controller->number_id = $number->get('id');
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
			if ($identifier->persist()) {
				$this->messages->addMessage('Secondary Identifier Updated');
			}
			$this->controller->identifier_id = $identifier->get('id');
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
			if ($identifier->persist()) {
				$this->messages->addMessage('Relationship Updated');
			}
			//$this->controller->person_person_id = $identifier->get('id');
		}
	}
	
	/**
	 * Handle updating patient statistics
	 */
	function process_patientStatistics_update($patient_id,$data) {
		if (count($data) > 0) {
			$patientStatistics =& ORDataObject::factory('PatientStatistics',$patient_id);
			$patientStatistics->populate_array($data);
			
			if ($patientStatistics->persist()) {
				$this->messages->addMessage('Statistics Updated');
			}
			$this->controller->patient_statistics_id = $patientStatistics->get('id');
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
			$this->messages->addMessage('Payer Updated');
			$this->controller->insured_relationship_id = $ir->get('id');
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
			$address->populateArray($data);
			if ($address->persist()) {
				$this->messages->addMessage('Address Updated');
			}
			$this->controller->address_id = $address->get('id');
		}
	}
	
	/**
	 * Handle adding a related person's address
	 */
	function process_relatedAddress_update($patient_id, $data) {
		$message = '';
		foreach ($data as $address) {
			$newAddress =& Celini::newORDO('PersonAddress', array($address['address_id'], $patient_id));
			$newAddress->set('address_id', $address['address_id']);
			$newAddress->set('person_id', $patient_id);
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
	 * Remove a duplicate mark
	 */
	function process_unmarkDuplicate($patient_id) {
		$dq =& Celini::newOrdo('DuplicateQueue',$patient_id,'ByPatientId');
		$dq->drop();
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
		$first_name = $person['first_name'] ;
		$first_initial = substr($first_name,0,1) ;
		$gender = $person['gender'] ; // 1=Male,2=Female
		$dob = $db->quote($person['date_of_birth']) ;
		
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
						last_name like ". $db->quote($last_name) . " OR 
						last_name like " . $db->quote($last_name.'%') . "
					)
					AND 
					(
						first_name like " . $db->quote($first_name) . " OR 
						first_name like ". $db->quote($first_name.'%') . "  OR 
						(SUBSTRING(first_name,1,1) like " .$db->quote($first_initial) . " AND gender=$gender) OR 
						date_of_birth=$dob
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
