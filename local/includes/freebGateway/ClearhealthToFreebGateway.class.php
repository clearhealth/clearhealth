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
	
	function ClearhealthToFreebGateway(&$controller, &$encounter) {
		$this->_caller =& $controller;
		$this->_encounter =& $encounter;
	}
	
	function send() {
		$freeb2 = new C_FreeBGateway();
		
		// get the objects were going to need
		$patient =& ORDataObject::factory('Patient',$this->_encounter->get('patient_id'));

		ORDataObject::Factory_include('InsuredRelationship');
		$relationships = InsuredRelationship::fromPersonId($patient->get('id'));

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
		$claim_identifier = $claim->get('id').'-'.$patient->get('record_number').'-'.$this->_encounter->get('id');

		// open the claim
		if (!$freeb2->openClaim($claim_identifier)) {
			trigger_error("Unable to open claim: $claim_identifier - ".$freeb2->claimLastError($claim_identifier));
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
			if (!$freeb2->registerData($claim_identifier,'Claimline',$claimline)) {
				trigger_error("Unable to register claimline - ". print_r($freeb2->claimLastError($claim_identifier),true));
			}
		}

		// store id in clearhealth
		$claim->set('identifier',$claim_identifier);
		$claim->set("total_paid",$total_paid);
		$claim->set('total_billed',$total_billed);
		$claim->persist();

		$this->_caller->_registerClaimData($freeb2, $this->_encounter, $claim_identifier);
	}
}

