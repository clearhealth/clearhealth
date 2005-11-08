<?php

/**
 * Serves as the gateway between Clearhealth and Freeb
 *
 * {@internal See issue 1572 for description of what this should be doing}
 *
 * @todo break up send() into smaller objects
 * @todo remove dependency on _caller->messages
 * @todo move C_Patient::_registerClaim() method into this class
 */
class ClearhealthToFreebGateway
{
	/**#@+
	 * @access private
	 */
	 
	/**
	 * The Controller that originated this call
	 *
	 * @var Controller
	 * @todo Abstract this out so its an Observer instead of tying this to the 
	 *    Controller object
	 */
	var $_caller = null;
	
	/**
	 * The Encounter that this claim is being generated off of
	 *
	 * @var Encounter
	 */
	var $_encounter = null;
	
	/**
	 * An internal reference to the C_FreeBGateway object
	 *
	 * @var C_FreeBGateway
	 */
	var $_freeb2 = null;
	
	/**
	 * Holds the string that is the claim_identifier of the claim we're working
	 * with.
	 *
	 * @var string
	 */
	var $_claim_identifier = '';
	
	/**#@-*/
	
	function ClearhealthToFreebGateway(&$controller, &$encounter) {
		$this->_caller =& $controller;
		$this->_encounter =& $encounter;
		$this->_freeb2 =& new C_FreeBGateway();
	}
	
	function send($status = 'new') {
		// get the objects were going to need
		$patient =& ORDataObject::factory('Patient',$this->_encounter->get('patient_id'));
		switch ($status) {
			case 'new' :
				$this->_setupNewClaim();
				break;
			case 'rebill' :
				$this->_setupRebillClaim();
				break;
		}
		$this->_registerClaimData();
	}
	
	/**
	 * Setup an existing claim in Freeb for rebilling
	 *
	 * @access private
	 */
	function _setupRebillClaim() {
		// get the current clearhealth claim
		ORdataObject::factory_include('ClearhealthClaim');
		$claim =& ClearhealthClaim::fromEncounterId($this->_encounter->get('id'));
		$this->_claim_identifier = $claim->get('identifier');

		// get the current revision of the freeb2 claim
		$currentRevision = $this->_freeb2->maxClaimRevision($this->_claim_identifier);

		// open current claim forcing a revision, its a clean revision
		$revision = $this->_freeb2->openClaim($this->_claim_identifier, $currentRevision, "P", true);
		
		// resend all the data
		// get the objects were going to need
		$patient =& Celini::newORDO('Patient',$this->_encounter->get('patient_id'));
		ORDataObject::Factory_include('InsuredRelationship');
		$relationships = InsuredRelationship::fromPersonId($patient->get('id'));

		if ($relationships == null) { 
			$this->messages->addMessage("This Patient has no Insurance Information to rebill, please add insurance information and try again <br>");
			return;
		}	
		
		$currentPayments = $claim->summedPaymentsByCode();
		
		$cd =& Celini::newORDO('CodingData');
		$codes = $cd->getCodeList($this->_encounter->get('id'));

		$feeSchedule =& Celini::newORDO('FeeSchedule',$this->_encounter->get('current_payer'));

		// add claimlines
		foreach($codes as $parent => $data) {

			$claimline = array();
			$claimline['date_of_treatment'] = $this->_encounter->get('date_of_treatment');
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
			if (!$this->_freeb2->registerData($this->_claim_identifier,'Claimline',$claimline)) {
				trigger_error("Unable to register claimline - ". print_r($this->_freeb2->claimLastError($this->_claim_identifier),true));
			}

			// rewrite ar if needed
			if (isset($currentPayments[$data['code']])) {
				$cp = $currentPayments[$data['code']];

				if ($cp['carry'] > 0 && $claimline['amount'] > $data['fee']) {
					$rcd =& Celini::newORDO('CodingData',$data['coding_data_id']);
					$rcd->set('fee',$claimline['amount']);
					$rcd->persist();
				}
			}
		}
	}
	/**
	 * Setup a new claim in Freeb
	 *
	 * @access private
	 */
	function _setupNewClaim() {
		ORDataObject::Factory_include('InsuredRelationship');
		$relationships = InsuredRelationship::fromPersonId($this->_encounter->get('patient_id'));

		if ($relationships == null) { 
			$this->_caller->messages->addMessage("This Patient has no Insurance Information to generate the claim, please add insurance information and try again <br>");
			return;
		}	
		

		$payment =& Celini::newORDO('Payment');
		$payment_ds = $payment->paymentsFromEncounterId($this->_encounter->get('id'));
		$payment_ds->clearFilters();
		$payments = $payment_ds->toArray();

		$cd =& Celini::newORDO('CodingData');
		$codes = $cd->getCodeList($this->_encounter->get('id'));

		if (count($codes) == 0) {
			$this->_caller->messages->addMessage('This encounter had no claim lines so no claim was billed');
			return;
		}

		//create totals paid as of now and total billed
		$total_paid = 0.00;
		$total_billed = 0.00;
		
		// create claim entity on clearhealh side
		$claim =& ORDataObject::Factory('ClearhealthClaim');
		$claim->set('encounter_id',$this->_encounter->get('id'));
		$claim->persist();
		
		//set the existing payments on the encounter to be part of this claim
		foreach ($payments as $payment_info) {
			$pmnt = ORDataObject::factory("Payment");
			$pmnt->populate_array($payment_info);
			$pmnt->set("foreign_id",$claim->get("claim_id"));
			$pmnt->persist();
			$total_paid += $payment_info['amount'];

			// payments have no claimline yet so lets create one for them applying the values to codes
			$currentPayments = $claim->summedPaymentsByCode();

			$toApply = $pmnt->get('amount');
			if ($toApply > 0) {
				foreach($codes as $code) {
					$fee = $code['fee'];
					if (isset($currentPayments[$code['code']])) {
						$fee = $fee - $currentPayments[$code['code']]['paid'];
					}

					if ($fee > 0) {
						if ($fee > $toApply) {
							$paid = $toApply;
						}
						else {
							$paid = $fee;
						}
						$toApply = $toApply - $paid;

						$claimline =& ORDataObject::Factory('PaymentClaimline');
						$claimline->set('payment_id',$pmnt->get('id'));
						$claimline->set('code_id',$code['code_id']);
						$claimline->set('paid',$paid);
						$claimline->set('writeoff',0);
						$claimline->set('carry',$fee-$paid);
						$claimline->persist();
					}
				}
			}

		}

		
		// generate a claim identifier from patient and encounter info
		$patient =& Celini::newORDO('Patient', $this->_encounter->get('patient_id'));
		$this->_claim_identifier = $claim->get('id').'-'.$patient->get('record_number').'-'.$this->_encounter->get('id');

		// open the claim
		if (!$this->_freeb2->openClaim($this->_claim_identifier)) {
			trigger_error("Unable to open claim: $this->_claim_identifier - ".$this->_freeb2->claimLastError($this->_claim_identifier));
		}
		
		// add claimlines
		$currentPayments = $claim->summedPaymentsByCode();

		$feeSchedule = ORDataObject::factory('FeeSchedule',$this->_encounter->get('current_payer'));

		foreach($codes as $parent => $data) {

		//echo "Debug: C_Patient<br>";
		//var_export($data); echo "<br>";		

			$claimline = array();
			$claimline['date_of_treatment'] = $this->_encounter->get('date_of_treatment');
			$claimline['procedure'] = $data['code'];
			$claimline['modifier'] = $data['modifier'];
			$claimline['units'] = $data['units'];
			$claimline['amount'] = $data['fee'];
			$mapped_code=$feeSchedule->getMappedCodeFromCodeId($data['code_id']);
		//	echo "<br>CPatient Code ".$data['code_id']." maps to $mapped_code<br>";
			if(strlen($mapped_code)>0){// then there is a mapped code which we should use.
				$claimline['procedure']=$mapped_code;
			}
			

			$total_billed += $data['fee'];
			if (isset($currentPayments[$data['code']])) {
				$claimline['amount_paid'] = $currentPayments[$data['code']]['paid'];
			}
			$claimline['diagnoses'] = array();
			
			
			$childCodes = $cd->getChildCodes($data['coding_data_id']);
			foreach($childCodes as $val) {
				$claimline['diagnoses'][] = $val['code'];
			}
			if (!$this->_freeb2->registerData($this->_claim_identifier,'Claimline',$claimline)) {
				trigger_error("Unable to register claimline - ". print_r($this->_freeb2->claimLastError($this->_claim_identifier),true));
			}
		}

		// store id in clearhealth
		$claim->set('identifier',$this->_claim_identifier);
		$claim->set("total_paid",$total_paid);
		$claim->set('total_billed',$total_billed);
		$claim->persist();
	}
	
	
	/**
	 * Handle registering a claim
	 *
	 * @access private
	 *
	 * @todo break into multiple methods and/or objects
	 */
	function _registerClaimData() {
		// get the objects were going to need
		$patient =& Celini::newORDO('Patient',$this->_encounter->get('patient_id'));
		ORDataObject::Factory_include('InsuredRelationship');
		$relationships = InsuredRelationship::fromPersonId($patient->get('id'));

		if ($relationships == null) { 
			$this->messages->addMessage("This Patient has no Insurance Information to register the claim data, please add insurance information and try again <br>");
			return;
		}	
		

		$provider =& Celini::newORDO('Provider',$this->_encounter->get('treating_person_id'));
		if ($provider->get('id') != $provider->get('bill_as')) {
			$bill_as = $provider->get('bill_as');
			unset($provider);
			$provider =& Celini::newORDO('Provider',$bill_as);
		}




		$facility =& Celini::newORDO('Building',$this->_encounter->get('building_id'));

		// register patient data
		//Debug:
		//echo "Debug:C_Patient.class".var_export($patient->toArray());
		$patientData = $this->_cleanDataArray($patient->toArray());


		$encounter_id=$this->_encounter->get('id');

		//This seems to be where Dates should be added..
		$EncounterDates =& Celini::newORDO('EncounterDate');
		$encounterDatesArray=$EncounterDates->encounterDateListArray($encounter_id);
		$date_enum = $EncounterDates->_load_enum("encounter_date_type");
		$date_enum = array_flip($date_enum);
	
		foreach($encounterDatesArray as $encounter_date_id){
			$EncounterDate =& Celini::newORDO('EncounterDate',$encounter_date_id,$encounter_id);
			$date_name = $date_enum[$EncounterDate->get('date_type')];
			$patientData[$date_name] = $EncounterDate->get('date');
		}

		if (!$this->_freeb2->registerData($this->_claim_identifier,'Patient',$patientData)) {
			trigger_error("Unable to register patient data - ".$this->_freeb2->claimLastError($this->_claim_identifier));
		}

		// register subscriber data
		foreach($relationships as $r) {
			$data = $r->toArray();
			$tmp = $this->_cleanDataArray($data['subscriber']);
			unset($data['subscriber']);
			$data = array_merge($data,$tmp);
			//echo "C_Patient subscriber data:<br>".var_export($data);
			$this->_freeb2->registerData($this->_claim_identifier,'Subscriber',$data);
		}

		// register payers
		$payerList = array();
		$clearingHouseData = false;

		if ((int)$this->_encounter->get('current_payer') > 0) {

			// only change order if its not correct
			if ($relationships[0]->get('insurance_program_id') != $this->_encounter->get('current_payer')) {
				// find the index to move to the top

				foreach($relationships as $key => $val) {
					if ($val->get('insurance_program_id') == $this->_encounter->get('current_payer')) {
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
			$program =& Celini::newORDO('InsuranceProgram',$r->get('insurance_program_id'));
			if ($defaultProgram == false) {
				$defaultProgram =& Celini::newORDO('InsuranceProgram',$r->get('insurance_program_id'));
			}

			if (!isset($payerList[$program->get('company_id')])) {
				$payerList[$program->get('company_id')] = true;
				$data = $program->toArray();
				$data = $this->_cleanDataArray($data['company']);
				$data['identifier'] = $data['name'];
				$this->_freeb2->registerData($this->_claim_identifier,'Payer',$data);
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
		
		if (!$this->_freeb2->registerData($this->_claim_identifier,'Provider',$providerData)) {
			trigger_error("Unable to register provider data - ".$this->_freeb2->claimLastError($this->_claim_identifier));
		}


		// register practice
		$practice =& Celini::newORDO('Practice',$facility->get('practice_id'));
		$practiceData = $this->_cleanDataArray($practice->toArray());

		$practiceData['sender_id'] = $defaultProgram->get('x12_sender_id');
		$practiceData['receiver_id'] = $defaultProgram->get('x12_receiver_id');
		$practiceData['x12_version'] = $defaultProgram->get('x12_version');

		//printf('<pre>%s</pre>', var_export($practiceData , true));
		if (!$this->_freeb2->registerData($this->_claim_identifier,'Practice',$practiceData)) {
			trigger_error("Unable to register practice data - ".$this->_freeb2->claimLastError($this->_claim_identifier));
		}

		// register treating facility
		$facilityData = $this->_cleanDataArray($facility->toArray());

		// check for an overriding identifier
		$bpi =& ORDAtaObject::factory('BuildingProgramIdentifier',$facility->get('id'),$defaultProgram->get('id'));
		if ($bpi->_populated) {
			$facilityData['identifier'] = $bpi->get('identifier');
		}
		
		if (!$this->_freeb2->registerData($this->_claim_identifier,'TreatingFacility',$facilityData)) {
			trigger_error("Unable to register treating facility data - ".$this->_freeb2->claimLastError($this->_claim_identifier));
		}
	

		// Add Encounter People....

		$EncounterPeople =& Celini::newORDO('EncounterPerson');
		$encounterPeopleArray=$EncounterPeople->encounterPersonListArray($encounter_id);
	
		foreach($encounterPeopleArray as $encounter_person_id){	
			$ep =& Celini::newORDO('EncounterPerson',array($encounter_person_id,$encounter_id));
			$eptl = $this->_encounter->_load_enum("encounter_person_type",false);
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
			$pbo =& Celini::newORDO($eptn, $loop_person_id);
			$pbo_data = $this->_cleanDataArray($pbo->toArray());

				//remove the gaps in the enumeration to be FreeB friendly..
			$encounter_person_type = preg_replace('/\s+/', '', $encounter_person_type);

			if (!$this->_freeb2->registerData($this->_claim_identifier,$encounter_person_type,$pbo_data)) {
				$freeb_error = $this->_freeb2->claimLastError($this->_claim_identifier);
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
		$EncounterValues =& Celini::newORDO('EncounterValue');
		$encounterValueArray=$EncounterValues->encounterValueListArray($encounter_id);
		$ev_enum = $EncounterValues->getValueTypeList();
		$ClaimData = array();
	
		foreach($encounterValueArray as $encounter_value_id){
			$EncounterValue =& Celini::newORDO('EncounterValue',array($encounter_value_id,$encounter_id));
			//printf('<pre>%s</pre>', var_export($ev_enum , true));exit;
			$ev_name = $ev_enum[$EncounterValue->get('value_type')];
			$ClaimData[$ev_name] = $EncounterValue->get('value');
		}
		//var_dump($ClaimData);
		if (!$this->_freeb2->registerData($this->_claim_identifier,'Claim',$ClaimData)) {
			trigger_error("Unable to register Claim data - ".$this->_freeb2->claimLastError($this->_claim_identifier));
		}

		// register responsible party - patient
		if (!$this->_freeb2->registerData($this->_claim_identifier,'ResponsibleParty',$patientData)) {
			trigger_error("Unable to register responsible party data - ".$this->_freeb2->claimLastError($this->_claim_identifier));
		}

		// register biling facility - practice
		if (!$this->_freeb2->registerData($this->_claim_identifier,'BillingFacility',$practiceData)) {
			trigger_error("Unable to register billing facility data - ".$this->_freeb2->claimLastError($this->_claim_identifier));
		}
		
		// register biling facility - practice
		if (!$this->_freeb2->registerData($this->_claim_identifier,'BillingContact',$practiceData)) {
			trigger_error("Unable to register billing contact data - ".$this->_freeb2->claimLastError($this->_claim_identifier));
		}
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

