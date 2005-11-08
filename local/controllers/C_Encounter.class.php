<?php
$loader->requireOnce('controllers/C_Coding.class.php');
$loader->requireOnce('freeb2/local/controllers/C_FreeBGateway.class.php');
$loader->requireOnce('local/includes/freebGateway/CHToFBArrayAdapter.class.php');

/**
 * A patient Encounter
 */
class C_Encounter extends Controller {
	var $coding;
	var $coding_parent_id = 0;
	var $encounter_date_id = 0;
	var $encounter_value_id = 0;
	var $encounter_person_id = 0;
	var $payment_id = 0;

	function C_Encounter() {
		$this->controller();
		$this->coding = new C_Coding();
	}

	function actionAdd() {
		return $this->actionEdit();
	}

	/**
	 * Edit/Add an encounter
	 */
	function actionEdit($encounter_id = 0) {
		if (isset($this->encounter_id)) {
			$encounter_id = $this->encounter_id;
		}
		
		$encounter_id = $this->_enforcer->int($encounter_id);
		$appointment_id = $this->GET->getTyped('appointment_id', 'int');
		$patient_id = $this->GET->getTyped('patient_id', 'int');
		
		$valid_appointment_id = false;

		// check if an encounter_id already exists for this appointment
		if ($appointment_id > 0) {
			$valid_appointment_id = true;
			ORDataObject::factory_include('Encounter');
			$id = Encounter::encounterIdFromAppointmentId($appointment_id);
			if ($id > 0) {
				$encounter_id         = $id;
				$valid_appointment_id = false;
			} 
		}

		if ($encounter_id > 0) {
			$this->set('encounter_id',$encounter_id);
			$this->set('external_id', $this->get('encounter_id'));
		}
		if ($patient_id > 0) {
			$this->set('patient_id',$patient_id);
		}
		//if ($encounter_id == 0 && $this->get('encounter_id') > 0) {
		//	$encounter_id = $this->get('encounter_id');
		//}	
		$this->set('encounter_id',$encounter_id);
		$encounter =& ORDataObject::factory('Encounter',$encounter_id,$this->get('patient_id'));
		$person =& ORDataObject::factory('Person');
		$building =& ORDataObject::factory('Building');

		$encounterDate =& ORDataObject::factory('EncounterDate',$this->encounter_date_id,$encounter_id);
		$encounterDateGrid = new cGrid($encounterDate->encounterDateList($encounter_id));
		$encounterDateGrid->name = "encounterDateGrid";
		$encounterDateGrid->registerTemplate('date','<a href="'.Celini::Managerlink('editEncounterDate',$encounter_id).'id={$encounter_date_id}&process=true">{$date}</a>');
		$this->assign('NEW_ENCOUNTER_DATE',Celini::managerLink('editEncounterDate',$encounter_id)."id=0&process=true");

		$encounterValue =& ORDataObject::factory('EncounterValue',$this->encounter_value_id,$encounter_id);
		$encounterValueGrid = new cGrid($encounterValue->encounterValueList($encounter_id));
		$encounterValueGrid->name = "encounterValueGrid";
		$encounterValueGrid->registerTemplate('value','<a href="'.Celini::Managerlink('editEncounterValue',$encounter_id).'id={$encounter_value_id}&process=true">{$value}</a>');
		$this->assign('NEW_ENCOUNTER_VALUE',Celini::managerLink('editEncounterValue',$encounter_id)."id=0&process=true");

		$encounterPerson =& ORDataObject::factory('EncounterPerson',$this->encounter_person_id,$encounter_id);
		$encounterPersonGrid = new cGrid($encounterPerson->encounterPersonList($encounter_id));
		$encounterPersonGrid->name = "encounterPersonGrid";
		$encounterPersonGrid->registerTemplate('person','<a href="'.Celini::Managerlink('editEncounterPerson',$encounter_id).'id={$encounter_person_id}&process=true">{$person}</a>');
		$this->assign('NEW_ENCOUNTER_PERSON',Celini::managerLink('editEncounterPerson',$encounter_id)."id=0&process=true");
		
		$payment =& ORDataObject::factory('Payment',$this->payment_id);
		if ($payment->_populated == false) {
			$payment->set('title','Co-Pay');
		}
		$payment->set("encounter_id",$encounter_id);
		$paymentGrid = new cGrid($payment->paymentsFromEncounterId($encounter_id));
		$paymentGrid->name = "paymentGrid";
		$paymentGrid->registerTemplate('amount','<a href="'.Celini::Managerlink('editPayment',$encounter_id).'id={$payment_id}&process=true">{$amount}</a>');
		$paymentGrid->registerFilter('payment_date', array('DateObject', 'ISOToUSA'));
		$this->assign('NEW_ENCOUNTER_PAYMENT',Celini::managerLink('editPayment',$encounter_id)."id=0&process=true");

		
		$appointments = $encounter->appointmentList();
		$appointmentArray = array("" => " ");
		foreach($appointments as $appointment) {
			$appointmentArray[$appointment['occurence_id']] = date("m/d/Y H:i",strtotime($appointment['appointment_start'])) . " " . $appointment['building_name'] . "->" . $appointment['room_name'] . " " . $appointment['provider_name'];
		}
		
		
		// If this is a saved encounter, generate the following:
		if ($this->get('encounter_id') > 0) {
			// Load data that has been stored
			$formData =& ORDataObject::factory("FormData");
			$formDataGrid =& new cGrid($formData->dataListByExternalId($encounter_id));
			$formDataGrid->name  = "formDataGrid";
			$formDataGrid->registerTemplate('name','<a href="'.Celini::link('data','Form').'id={$form_data_id}">{$name}</a>');
		// commenting this line out fixed 3602 
		//	$formDataGrid->pageSize = 10;
		// 	
			// Generate a menu of forms that are connected to Encounters
			$menu = Menu::getInstance();
			$connectedForms = $menu->getMenuData('patient',
				$menu->getMenuIdFromTitle('patient','Encounter Forms'));
			
			$formList = array();
			if (isset($connectedForms['forms'])) {
				foreach($connectedForms['forms'] as $form) {
					$formList[$form['form_id']] = $form['title'];
				}
			}
		}
		
		//if an appointment id is supplied the request is coming from the 
		//calendar and so prepopulate the defaults
		if ($appointment_id > 0 && $valid_appointment_id) {
			$encounter->set("occurence_id",$appointment_id);
			$encounter->set("patient_id",$this->get("patient_id"));
			if (isset($appointments[$appointment_id])) {
				$encounter->set("building_id",$appointments[$appointment_id]['building_id']);
			}
			if (isset($appointments[$appointment_id])) {
				$encounter->set("treating_person_id",$appointments[$appointment_id]['provider_id']);
			}
		}

		$insuredRelationship =& ORDataObject::factory('InsuredRelationship');


		$this->assign_by_ref('insuredRelationship',$insuredRelationship);
		$this->assign_by_ref('encounter',$encounter);
		$this->assign_by_ref('person',$person);
		$this->assign_by_ref('building',$building);
		$this->assign_by_ref('encounterDate',$encounterDate);
		$this->assign_by_ref('encounterDateGrid',$encounterDateGrid);
		$this->assign_by_ref('encounterPerson',$encounterPerson);
		$this->assign_by_ref('encounterPersonGrid',$encounterPersonGrid);
		$this->assign_by_ref('encounterValue',$encounterValue);
		$this->assign_by_ref('encounterValueGrid',$encounterValueGrid);
		$this->assign_by_ref('payment',$payment);
		$this->assign_by_ref('paymentGrid',$paymentGrid);
		$this->assign_by_ref('appointmentList',$appointments);
		$this->assign_by_ref('appointmentArray',$appointmentArray);
		
		$this->assign('FORM_ACTION',Celini::link('edit',true,true,$encounter_id));
		$this->assign('FORM_FILLOUT_ACTION',Celini::link('fillout','Form'));

		if ($encounter_id > 0) {
			$this->coding->assign('FORM_ACTION',Celini::link('edit',true,true,$encounter_id));
			$this->coding->assign("encounter", $encounter);
			$codingHtml = $this->coding->update_action_edit($encounter_id,$this->coding_parent_id);
			$this->assign('codingHtml',$codingHtml);
			$this->assign_by_ref('formDataGrid',$formDataGrid);
			$this->assign_by_ref('formList',$formList);
		}

		if ($encounter->get('status') === "closed") {
			ORDataObject::factory_include('ClearhealthClaim');
			$claim =& ClearhealthClaim::fromEncounterId($encounter_id);
			//printf('<pre>%s</pre>', var_export($claim->toArray(), true));
			$this->assign('FREEB_ACTION',$GLOBALS['C_ALL']['freeb2_dir'] . substr(Celini::link('list_revisions','Claim','freeb2',$claim->get('identifier'),false,false),1));
			$this->assign('PAYMENT_ACTION',Celini::link('payment','Eob',true,$claim->get('id')));

			$this->assign('encounter_has_claim',false);
			if ($claim->_populated) {
				$this->assign('encounter_has_claim',true);
			}

			$this->assign('REOPEN_ACTION',Celini::link('reopen', true, true, $encounter->get('id')) . 'process=true');
		}
		else {
			ORdataObject::factory_include('ClearhealthClaim');
			$claim =& ClearhealthClaim::fromEncounterId($encounter_id);
			if ($claim->get('identifier') > 0) {
				$this->assign('claimSubmitValue', 'rebill');
			}
			else {
				$this->assign('claimSubmitValue', 'close');
			}
		}
		return $this->view->render("edit.html");
	}

	
	function processEdit($encounter_id=0) {
		if (isset($_POST['saveCode'])) {
			$this->coding->update_action_process();
			return;
		}


		$encounter =& ORDataObject::factory('Encounter',$encounter_id,$this->get('patient_id'));
		$encounter->populate_array($_POST['encounter']);

		if (isset($_POST['select_payer'])) {
			$encounter->persist();
			return;
		}
		
				
		$encounter->persist();
		$this->encounter_id = $encounter->get('id');

		if (isset($_POST['encounterDate']) && !empty($_POST['encounterDate']['date'])) {
			$this->encounter_date_id = $_POST['encounterDate']['encounter_date_id'];
			$encounterDate =& ORDataObject::factory('EncounterDate',$this->encounter_date_id,$this->encounter_id);
			$encounterDate->populate_array($_POST['encounterDate']);
			$encounterDate->persist();
			$this->encounter_date_id = $encounterDate->get('id');
		}
		if (isset($_POST['encounterValue']) && !empty($_POST['encounterValue']['value'])) {
			$this->encounter_value_id = $_POST['encounterValue']['encounter_value_id'];
			$encounterValue =& ORDataObject::factory('EncounterValue',$this->encounter_value_id,$this->encounter_id);
			$encounterValue->populate_array($_POST['encounterValue']);
			$encounterValue->persist();
			$this->encounter_value_id = $encounterValue->get('id');
		}
		if (isset($_POST['encounterPerson']) && !empty($_POST['encounterPerson']['person_id'])) {
			$this->encounter_person_id = $_POST['encounterPerson']['encounter_person_id'];
			$encounterPerson =& ORDataObject::factory('EncounterPerson',$this->encounter_person_id,$this->encounter_id);
			$encounterPerson->populate_array($_POST['encounterPerson']);
			$encounterPerson->persist();
			$this->encounter_person_id = $encounterPerson->get('id');
		}
		if (isset($_POST['payment']) && !empty($_POST['payment']['amount'])) {
			$this->payment_id = $_POST['payment']['payment_id'];
			$payment =& ORDataObject::factory('Payment',$this->payment_id);
			$payment->set("encounter_id", $this->encounter_id);
			$payment->populate_array($_POST['payment']);
			$payment->persist();
			$this->payment_id = $payment->get('id');
		}

		if (isset($_POST['encounter']['close'])) {
			$patient =& ORDataObject::factory('Patient',$encounter->get('patient_id'));
			ORDataObject::Factory_include('InsuredRelationship');
			$relationships = InsuredRelationship::fromPersonId($patient->get('id'));

			if ($relationships == null) { 
				$this->messages->addMessage("This Patient has no Insurance Information, please add insurance information and try again <br>");
				return;
			}else{	
				$encounter->set("status","closed");	
				$encounter->persist();
				$this->_generateClaim($encounter);
			}
		}
		
		// If this is a rebill, pass it off to the rebill method
		if (isset($_POST['encounter']['rebill'])) {
			$encounter->set('status', 'closed');
			$encounter->persist();
			$this->rebill_encounter_action_process($encounter_id);
		}
	}

	/**
	 * Re-opens a claim and redirects back to the encounter view
	 *
	 * @param int
	 */
	function processReopen_edit($encounter_id) {
		$encounter =& ORDataObject::Factory('Encounter', $encounter_id);
		$encounter->set('status', 'open');
		$encounter->persist();
		
		header('Location: '.Celini::link('edit',true,true,$encounter_id));
		exit();
	}


	/**
	 * Rebill an claim
	 */
	function processRebillEncounter_edit($encounter_id) {

		$encounter =& ORDataObject::Factory('Encounter',$encounter_id);

		// setup freeb2
		$this->_includeFreeb2();
		$freeb2 = new C_FreeBGateway();

		// get the current clearhealth claim
		ORdataObject::factory_include('ClearhealthClaim');
		$claim =& ClearhealthClaim::fromEncounterId($encounter_id);
		$claimIdentifier = $claim->get('identifier');

		// get the current revision of the freeb2 claim
		$currentRevision = $freeb2->maxClaimRevision($claimIdentifier);

		// open current claim forcing a revision, its a clean revision
		$revision = $freeb2->openClaim($claimIdentifier, $currentRevision, "P", true);
		
		// resend all the data
		// get the objects were going to need
		$patient =& ORDataObject::factory('Patient',$encounter->get('patient_id'));
		ORDataObject::Factory_include('InsuredRelationship');
		$relationships = InsuredRelationship::fromPersonId($patient->get('id'));

		if ($relationships == null) { 
			$this->messages->addMessage("This Patient has no Insurance Information to rebill, please add insurance information and try again <br>");
			return;
		}	
		
		$currentPayments = $claim->summedPaymentsByCode();
		
		$cd =& ORDataObject::Factory('CodingData');
		$codes = $cd->getCodeList($encounter->get('id'));

		$feeSchedule = ORDataObject::factory('FeeSchedule',$encounter->get('current_payer'));

		// add claimlines
		foreach($codes as $parent => $data) {

			$claimline = array();
			$claimline['date_of_treatment'] = $encounter->get('date_of_treatment');
			$claimline['procedure'] = $data['code'];
			$claimline['modifier'] = $data['modifier'];
			$claimline['units'] = $data['units'];
			$claimline['amount'] = $feeSchedule->getFeeFromCodeId($data['code_id']);
			$mapped_code=$feeSchedule->getMappedCodeFromCodeId($data['code_id']);
			//echo "<br>CPatient Code ".$data['code_id']." maps to $mapped_code<br>";
			if(strlen($mapped_code)>0){// then there is a mapped code which we should use.
				$claimline['procedure']=$mapped_code;
			}

			$claimline['diagnoses'] = array();
			if (isset($currentPayments[$data['code']])) {
				$claimline['amount_paid'] = $currentPayments[$data['code']]['paid'];
			}
			
			$childCodes = $cd->getChildCodes($data['coding_data_id']);
			foreach($childCodes as $val) {
				$claimline['diagnoses'][] = $val['code'];
			}
			if (!$freeb2->registerData($claimIdentifier,'Claimline',$claimline)) {
				trigger_error("Unable to register claimline - ". print_r($freeb2->claimLastError($claimIdentifier),true));
			}

			// rewrite ar if needed
			if (isset($currentPayments[$data['code']])) {
				$cp = $currentPayments[$data['code']];

				if ($cp['carry'] > 0 && $claimline['amount'] > $data['fee']) {
					$rcd =& ORDataObject::factory('CodingData',$data['coding_data_id']);
					$rcd->set('fee',$claimline['amount']);
					$rcd->persist();
				}
			}
		}

		$this->_registerClaimData($freeb2,$encounter,$claimIdentifier);

		header('Location: '.Celini::link('edit',true,true,$encounter_id));
		exit();
	}

	


	/**
	 * Util function to check if we can rebill
	 *
	 * rule is: EOB payment has been made, There is an outstanding Balance, there is a secondary payer
	 */
	function _canRebill($encounterId) {
		$claim =& ClearhealthClaim::fromEncounterId($encounterId);

		ORDataObject::factory_include('Payment');
		$payments = Payment::fromForeignId($claim->get('id'));

		// check for EOB payment
		if (count($payments) > 0)  {

			$encounter =& ORDataObject::factory('Encounter',$encounterId);

			// check for an outstanding balance
			$status = $claim->accountStatus($encounter->get('patient_id'),$encounterId);
			if ($status['total_balance'] > 0) {

				// check for a secondary payer

				ORDataObject::factory_include('InsuredRelationship');
				$payers = InsuredRelationship::fromPersonId($encounter->get('patient_id'));
				if (count($payers) > 1) {
					return true;
				}
			}
		}

		return false;
	}

	function update_action($foreign_id = 0, $parent_id = 0) {
		$this->coding_parent_id = $parent_id;
		return $this->encounter_action_edit($this->get('encounter_id'));
	}

	function _includeFreeb2() {
		//TODO make these respect the config.php values
		global $loader;
	}

	function _generateClaim(&$encounter,$claim = false) {
		// load gateway
		global $loader;
		$loader->requireOnce('local/includes/freebGateway/ClearhealthToFreebGateway.class.php');
		
		$gateway =& new ClearhealthToFreebGateway($this, $encounter);
		$gateway->send();
	}

function _registerClaimData(&$freeb2,&$encounter,$claim_identifier) {
		// get the objects were going to need
		$patient =& ORDataObject::factory('Patient',$encounter->get('patient_id'));
		ORDataObject::Factory_include('InsuredRelationship');
		$relationships = InsuredRelationship::fromPersonId($patient->get('id'));

		if ($relationships == null) { 
			$this->messages->addMessage("This Patient has no Insurance Information to register the claim data, please add insurance information and try again <br>");
			return;
		}	
		

		$provider =& ORDataObject::factory('Provider',$encounter->get('treating_person_id'));
		if ($provider->get('id') != $provider->get('bill_as')) {
			$bill_as = $provider->get('bill_as');
			unset($provider);
			$provider =& ORDataObject::factory('Provider',$bill_as);
		}




		$facility =& ORDataObject::factory('Building',$encounter->get('building_id'));

		// register patient data
		//Debug:
		//echo "Debug:C_Patient.class".var_export($patient->toArray());
		$patientData = $this->_cleanDataArray($patient->toArray());


		$encounter_id=$encounter->get('id');

		//This seems to be where Dates should be added..
		$EncounterDates =& ORDataObject::factory('EncounterDate');
		$encounterDatesArray=$EncounterDates->encounterDateListArray($encounter_id);
		$date_enum = $EncounterDates->_load_enum("encounter_date_type");
		$date_enum = array_flip($date_enum);
	
		foreach($encounterDatesArray as $encounter_date_id){
			$EncounterDate =& ORDataObject::factory('EncounterDate',$encounter_date_id,$encounter_id);
			$date_name = $date_enum[$EncounterDate->get('date_type')];
			$patientData[$date_name] = $EncounterDate->get('date');
		}

		if (!$freeb2->registerData($claim_identifier,'Patient',$patientData)) {
			trigger_error("Unable to register patient data - ".$freeb2->claimLastError($claim_identifier));
		}

		// register subscriber data
		foreach($relationships as $r) {
			$data = $r->toArray();
			$tmp = $this->_cleanDataArray($data['subscriber']);
			unset($data['subscriber']);
			$data = array_merge($data,$tmp);
			//echo "C_Patient subscriber data:<br>".var_export($data);
			$freeb2->registerData($claim_identifier,'Subscriber',$data);
		}

		// register payers
		$payerList = array();
		$clearingHouseData = false;

		if ((int)$encounter->get('current_payer') > 0) {

			// only change order if its not correct
			if ($relationships[0]->get('insurance_program_id') != $encounter->get('current_payer')) {
				// find the index to move to the top

				foreach($relationships as $key => $val) {
					if ($val->get('insurance_program_id') == $encounter->get('current_payer')) {
						$r = $val;
						$id = $key;
						break;
					}
				}
				if (isset($id)) {
					unset($relationships[$id]);
					array_unshift($relationships,$r);
				}

			}
		}

		$defaultProgram = false;
		foreach($relationships as $r) {
			$program =& ORDataObject::factory('InsuranceProgram',$r->get('insurance_program_id'));
			if ($defaultProgram == false) {
				$defaultProgram =& ORDataObject::factory('InsuranceProgram',$r->get('insurance_program_id'));
			}

			if (!isset($payerList[$program->get('company_id')])) {
				$payerList[$program->get('company_id')] = true;
				$data = $program->toArray();
				$data = $this->_cleanDataArray($data['company']);
				$data['identifier'] = $data['name'];
				$freeb2->registerData($claim_identifier,'Payer',$data);
				if ($clearingHouseData === false) {
					$clearingHouseData = $data;
				}
			}
		}

		// register provider
		$providerData = $this->_cleanDataArray($provider->toArray());

		// Set secondary identifier
		$providerPerson = $provider->get('person');
		
		// See if there is a program specific, or program/building specific ID 
		// for this provider.
		
		$programSpecificID =& Celini::newORDO(
			'ProviderToInsurance', 
			array($provider->get('id'), $defaultProgram->get('id'), $facility->get('id')),
			'ByProgramAndBuilding');
		
		if ($programSpecificID->isPopulated()) {
			$providerData['identifier_2']      = $programSpecificID->get('provider_number');
			$providerData['identifier_type_2'] = $programSpecificID->get('identifier_type_value'); 
		}
		else { /* */
			// Attempt to load default secondary IDs
			$extraIdentifiers =& $providerPerson->identifierList();
			if (count($extraIdentifiers->toArray()) > 0) {
				$i = 2;
				foreach ($extraIdentifiers->toArray() as $extraIdentifier) {
					$providerData["identifier_{$i}"]      = $extraIdentifier["identifier"];
					$providerData["identifier_type_{$i}"] = $extraIdentifier["identifier_type"];
					$i++;
				}
			}
		}
		
		if (!$freeb2->registerData($claim_identifier,'Provider',$providerData)) {
			trigger_error("Unable to register provider data - ".$freeb2->claimLastError($claim_identifier));
		}


		// register practice
		$practice =& ORDataObject::factory('Practice',$facility->get('practice_id'));
		$practiceData = $this->_cleanDataArray($practice->toArray());

		$practiceData['sender_id'] = $defaultProgram->get('x12_sender_id');
		$practiceData['receiver_id'] = $defaultProgram->get('x12_receiver_id');
		$practiceData['x12_version'] = $defaultProgram->get('x12_version');

		//printf('<pre>%s</pre>', var_export($practiceData , true));
		if (!$freeb2->registerData($claim_identifier,'Practice',$practiceData)) {
			trigger_error("Unable to register practice data - ".$freeb2->claimLastError($claim_identifier));
		}

		// register treating facility
		$facilityData = $this->_cleanDataArray($facility->toArray());

		// check for an overriding identifier
		$bpi =& ORDAtaObject::factory('BuildingProgramIdentifier',$facility->get('id'),$defaultProgram->get('id'));
		if ($bpi->_populated) {
			$facilityData['identifier'] = $bpi->get('identifier');
		}
		
		if (!$freeb2->registerData($claim_identifier,'TreatingFacility',$facilityData)) {
			trigger_error("Unable to register treating facility data - ".$freeb2->claimLastError($claim_identifier));
		}
	

		// Add Encounter People....

		$EncounterPeople =& ORDataObject::factory('EncounterPerson');
		$encounterPeopleArray=$EncounterPeople->encounterPersonListArray($encounter_id);
	
		foreach($encounterPeopleArray as $encounter_person_id){	
			$ep =& ORDataObject::factory('EncounterPerson',$encounter_person_id,$encounter_id);
			$eptl = $encounter->_load_enum("encounter_person_type",false);
			$eptl = array_flip($eptl);
			$encounter_person_type=$eptl[$ep->get('person_type')];
			
			$eptn = $ep->personTypeName();
			if (!$eptn) {
				trigger_error("Unable to find person type for encountner person with id: $encounter_person_id This usually happens when a patient record is choosen as a person associated with an encounter");
				continue;	
			}
			
			//person based object
			$person_type = 	$ep->get('person_type');
			$loop_person_id = $ep->get('person_id');
			$pbo =& ORDataObject::factory($eptn, $loop_person_id);
			$pbo_data = $this->_cleanDataArray($pbo->toArray());

				//remove the gaps in the enumeration to be FreeB friendly..
			$encounter_person_type = preg_replace('/\s+/', '', $encounter_person_type);

			if (!$freeb2->registerData($claim_identifier,$encounter_person_type,$pbo_data)) {
				$freeb_error = $freeb2->claimLastError($claim_identifier);
				$freeb_error_message = $freeb_error[1];
				$freeb_error_number = $freeb_error[0];
			//	var_dump($freeb_error);
				if($freeb_error_number==110){// 110 = Unknown Registration name. 
					//FreeB does not know what to do with this type of person.
				$this->messages->addMessage("FreeB did not understand person type $encounter_person_type");
				
					continue;
				}else{// lets give a fuller debugging message!!
					trigger_error("Unable to registerData person data with FreeB using person #".
					"$encounter_person_id as person type #$person_type $encounter_person_type ".
					"loaded as $eptn  - FreeB Error: $freeb_error_message");
					//no continue here, this should be a show stopper!!
				}
			}
		}
		// End Encounter People

		// Encounter Values, 
		$EncounterValues =& ORDataObject::factory('EncounterValue');
		$encounterValueArray=$EncounterValues->encounterValueListArray($encounter_id);
		$ev_enum = $EncounterValues->getValueTypeList();
		$ClaimData = array();
	
		foreach($encounterValueArray as $encounter_value_id){
			$EncounterValue =& ORDataObject::factory('EncounterValue',$encounter_value_id,$encounter_id);
			//printf('<pre>%s</pre>', var_export($ev_enum , true));exit;
			$ev_name = $ev_enum[$EncounterValue->get('value_type')];
			$ClaimData[$ev_name] = $EncounterValue->get('value');
		}
		//var_dump($ClaimData);
		if (!$freeb2->registerData($claim_identifier,'Claim',$ClaimData)) {
			trigger_error("Unable to register Claim data - ".$freeb2->claimLastError($claim_identifier));
		}

		// register responsible party - patient
		if (!$freeb2->registerData($claim_identifier,'ResponsibleParty',$patientData)) {
			trigger_error("Unable to register responsible party data - ".$freeb2->claimLastError($claim_identifier));
		}

		// register biling facility - practice
		if (!$freeb2->registerData($claim_identifier,'BillingFacility',$practiceData)) {
			trigger_error("Unable to register billing facility data - ".$freeb2->claimLastError($claim_identifier));
		}
		
		// register biling facility - practice
		if (!$freeb2->registerData($claim_identifier,'BillingContact',$practiceData)) {
			trigger_error("Unable to register billing contact data - ".$freeb2->claimLastError($claim_identifier));
		}
	}

	//add javadocs to say that this is pass through...
	function delete_claimline_action_process($parent_id,$encounter_id) {

		$encounter =& ORDataObject::factory('Encounter',$encounter_id,$this->get('patient_id'));
		if($encounter->get('status') === "open"){
			//TODO this disables the delete function on closed encounters
			//TODO the template should not even display the X on a closed claim.
			$this->coding->delete_claimline($parent_id);
		}
	
		header("Location:" . Celini::link("encounter", true, true, $encounter_id));
		$this->_state=false;

	}

	/**
	 * Handles preparing an array of data to be passed into freeb by converting
	 * names from CH name to FB name
	 *
	 * @param  array
	 * @return array
	 * @access private
	 * @deprecated
	 *
	 * @todo replace calls to this with direct calls to {@link CHToFBArrayAdapter}
	 */
	function _cleanDataArray($data) {
		$adapter =& new CHToFBArrayAdapter($data);
		return $adapter->adapted();
	}


}
?>
