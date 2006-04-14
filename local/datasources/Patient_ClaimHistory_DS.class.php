<?php
/**
 * @package com.uversainc.clearhealth
 */

/**
 * include the abstract datasource
 */
$loader->requireOnce('includes/Datasource.class.php');

/**
 * Specialized datasource for managing account history
 */
class Patient_ClaimHistory_DS extends Datasource {

	var $person_id;
	var $claims;
	var $lines = array();
	var $lineRewind = array();
	var $payments = array();
	var $paymentRewind = array();
	var $_res = false;
	var $_numRows = false;
	var $_valid = false;

	/**
	 * Set the person to get account history for and pull in the person's data
	 */
	function Patient_ClaimHistory_DS($person_id) {
		$this->person_id = EnforceType::int($person_id);
		
		$filteredGet =& Celini::filteredGet();
		$claimId = $filteredGet->getTyped('claim_id', 'int');
		
		$patient =& Celini::newORDO('Patient', $person_id);
		
		$this->claims =& $patient->loadDatasource('ClaimPayment');
		$this->_labels = array ( 
			'identifier' 	=> 'Id', 
			'current_payer' => 'Payer Name',
			'billing_date' => 'Date Billed',
			'date_of_treatment' => 'Date', 
			'total_billed' 	=> 'Billed', 
			'total_paid' 	=> 'Paid', 
			'writeoff'	=> 'Write Off',
			'balance' 	=> 'Balance',
			'facility'	=> "Facility",
			'provider'	=> "Provider",
		);

		$line =& ORDataObject::factory('PaymentClaimline');

		$this->_numRows = $this->claims->numRows();
		for($this->claims->rewind(); $this->claims->valid(); $this->claims->next()) {
			$row = $this->claims->get();
			$claim_id = $row['claim_id'];

			$this->payments[$claim_id] =& $this->_paymentList($person_id,$claim_id);
			$this->_numRows += $this->payments[$claim_id]->numRows();
		}
	}

	function numRows() {
		return $this->claims->numRows();
	}

	function _filter() {
		parent::_filter($this->res->fields);
	}

	function rewind() {
		$this->claims->rewind();
		$this->_res = $this->claims->_res;
		$this->_res->fields['type'] = 'Claim';
		$this->_valid = $this->claims->valid();
	}

	function next() {
		$nextClaim = true;
		if (isset($this->_res->fields['claim_id'])) {
			$this->claim_id = $this->_res->fields['claim_id'];
		}
		$claim_id = $this->claim_id;

		if (isset($this->payments[$claim_id]) && $nextClaim) {
			if (!isset($this->paymentRewind[$claim_id])) {
				$this->payments[$claim_id]->rewind();
				$this->paymentRewind[$claim_id] = true;
			}
			else {
				$this->payments[$claim_id]->next();
			}
			if ($this->payments[$claim_id]->valid()) {
				$nextClaim = false;
				$this->_res = $this->payments[$claim_id]->_res;
				$this->_res->fields['total_paid'] = $this->_res->fields['amount'];
			//	$this->_res->fields['balance'] = $this->_res->fields['carry'];
				//var_dump($this->_res->fields);
				$this->_res->fields['date_of_treatment'] = $this->_res->fields['payment_date'];
				if (!is_null($this->_res->fields['payment_type'])) {
					$this->_res->fields['identifier'] = 'copay';
					$this->_res->fields['writeoff'] = '';
				}
				else {
					$this->_res->fields['identifier'] = $this->_res->fields['payer_id'];
				}
				$this->_valid = $this->payments[$claim_id]->valid();
			}
		}

		if ($nextClaim) {
			$this->claims->next();
			$this->_res = $this->claims->_res;
			$this->_res->fields['type'] = 'Claim';
			$this->_valid = $this->claims->valid();
		}
	}

	function valid() {
		return $this->_valid;
	}

	function get() { 
		//var_dump($this->_res->fields);
		return $this->_res->fields;
	}

	/**
	 * Get datasource for payments from the db using the patient id
	 */
	function &_paymentList($patient_id,$claim_id) {
		settype($patient_id,'int');
		settype($claim_id,'int');
		
		$payment =& ORDataObject::factory("Payment");
		
		$ds =& new Datasource_sql();
		$ds->registerFilter('payer_id',array(&$payment,'lookupPayer'));

		$ds->setup($payment->_db,array(
				'cols' 	=> '
					payment_id,
					foreign_id,
					payment_type, 
					amount,
					writeoff,
					payer_id,
					payment_date,
					pa.timestamp',
				'from' 	=> '
					payment AS pa
					LEFT JOIN encounter AS e USING(encounter_id)
					LEFT JOIN person AS p ON (e.patient_id = p.person_id)
					LEFT JOIN clearhealth_claim AS chc ON (chc.claim_id = pa.foreign_id)
					LEFT JOIN encounter AS e2 ON(chc.encounter_id = e2.encounter_id)',
				'where' => '
					(
						e.patient_id = ' . $patient_id . ' OR
						e2.patient_id = ' . $patient_id . '
					) AND
					chc.claim_id = ' . $claim_id
			),
			array(
				'payment_type' => 'Type',
				'payment_date' => 'Payment Date',
				'amount' => 'Amount',
				'writeoff' => 'Write Off',
				'payer_id' => 'Payer'
			)
		);
		//echo $ds->preview();
		$ds->registerFilter('payment_type',array(&$payment,'lookupPaymentType'));
		return $ds;
	}
}
?>
