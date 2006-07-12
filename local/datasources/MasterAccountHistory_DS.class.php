<?php
/**
 * @package com.uversainc.clearhealth
 */

/**
 * include the abstract datasource
 */
$loader->requireOnce('includes/Datasource.class.php');
$loader->requireOnce('datasources/MasterClaimList_DS.class.php');

/**
 * Specialized datasource for managing account history
 */
class MasterAccountHistory_DS extends Datasource {

	var $person_id;
	var $claims;
	var $lines = array();
	var $lineRewind = array();
	var $payments = array();
	var $paymentRewind = array();
	var $_res = false;
	var $_numRows = false;
	var $_valid = false;
	var $filters = false;
	var $_type = 'html';

	function MasterAccountHistory_DS($filters = false) {
		$this->filters = $filters;
		$this->setup();
	}

	function setLimit($start,$rows) {
		$this->claims->setLimit($start,$rows);
	}

	/**
	 * Set the person to get account history for and pull in the person's data
	 */
	function setup() {
		$this->claims =& new MasterClaimList_DS($this->filters);
		$this->_labels = array ( 
			'identifier' 	=> 'Id', 
			'patient_name'  => 'Patient Name',
			'current_payer' => 'Payer',
			'date_of_treatment' => 'DOS', 
			'total_billed' 	=> 'Billed', 
			'total_paid' 	=> 'Paid', 
			'writeoff'	=> 'WO',
			'balance' 	=> 'Balance',
			'provider'	=> "Provider",
			'user'		=> "Entered By",
		);

		$line =& Celini::newORDO('PaymentClaimline');

		$this->_numRows = $this->claims->numRows();
		for($this->claims->rewind(); $this->claims->valid(); $this->claims->next()) {
			$row = $this->claims->get();
			$claim_id = $row['claim_id'];
			$person_id = $row['patient_id'];

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

	function addOrderRule($column,$direction='ASC',$order=0) {

		parent::addOrderRule($column,$direction,$order);
		$this->claims->addOrderRule($column,$direction,$order);
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

		$dateFormat = DateObject::getFormat();
		$ds->setup($payment->_db,array(
				'cols' 	=> '
					payment_id,
					foreign_id,
					payment_type, 
					amount,
					writeoff,
					payer_id,
					DATE_FORMAT(payment_date, "' . $dateFormat . '") AS payment_date,
					pa.timestamp,
					u.username user
					',
				'from' 	=> '
					payment AS pa
					LEFT JOIN encounter AS e USING(encounter_id)
					LEFT JOIN person AS p ON (e.patient_id = p.person_id)
					LEFT JOIN clearhealth_claim AS chc ON (chc.claim_id = pa.foreign_id)
					LEFT JOIN encounter AS e2 ON(chc.encounter_id = e2.encounter_id)
					LEFT JOIN ordo_registry AS oreg ON(pa.payment_id = oreg.ordo_id)
					LEFT JOIN user AS u ON(oreg.creator_id = u.user_id)
					',
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
