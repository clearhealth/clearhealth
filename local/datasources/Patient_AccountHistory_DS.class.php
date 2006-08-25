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
class Datasource_AccountHistory extends Datasource {

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
	var $adjustments = array();
	var $adjustmentTypes = null;
	var $charges = false;

	function Datasource_AccountHistory($filters = false) {
		$this->filters = $filters;
	}

	/**
	 * Set the person to get account history for and pull in the person's data
	 */
	function setup($person_id) {

		$this->person_id = $person_id;

		$claim =& ORDataObject::factory('ClearhealthClaim');
		$this->claims = $claim->claimList($person_id,true,$this->filters);
		$this->charges = $this->_miscChargeList($person_id);

		$this->_labels = array ( 
			'identifier' 	=> 'Id', 
			'current_payer' => 'Payer Name',
			'billing_date' => 'Date Billed',
			'date_of_treatment' => 'Date', 
			'total_billed' 	=> 'Billed', 
			'total_paid' 	=> 'Paid', 
			'writeoff'	=> 'Write Off',
			'balance' 	=> 'Balance',
			'ref_num'	=> "Chk #",
			'facility'	=> "Facility",
			'provider'	=> "Provider",
			'user'		=> "Entered By",
		);

		$line =& ORDataObject::factory('PaymentClaimline');

		$this->_numRows = $this->claims->numRows();
		for($this->claims->rewind(); $this->claims->valid(); $this->claims->next()) {
			$row = $this->claims->get();
			$claim_id = $row['claim_id'];

			$this->payments[$claim_id] =& $this->_paymentList($person_id,$claim_id);
			$this->_numRows += $this->payments[$claim_id]->numRows();
			for($this->payments[$claim_id]->rewind();$this->payments[$claim_id]->valid();$this->payments[$claim_id]->next()) {
				$row = $this->payments[$claim_id]->get();
				$this->adjustments[$row['payment_id']] = $this->_getAdjustments($row['payment_id']);
			}
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

	var $_chargeIndex = 0;

	function next() {
		$nextClaim = true;
		if (isset($this->_res->fields['claim_id'])) {
			$this->claim_id = $this->_res->fields['claim_id'];
		}
		$claim_id = $this->claim_id;

		if (isset($this->charges[$claim_id][$this->_chargeIndex])) {
			$this->_res->fields = $this->charges[$claim_id][$this->_chargeIndex];
			$this->_chargeIndex++;
			$nextClaim = false;
		}
		else if (isset($this->payments[$claim_id]) && $nextClaim) {
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
				if (is_null($this->_res->fields['payer_id'])) {
					$this->_res->fields['identifier'] = 'Misc Payment';
					$this->_res->fields['current_payer'] = $this->_res->fields['title'];
					$this->_res->fields['writeoff'] = '';
				}
				else {
					$this->_res->fields['identifier'] = 'Payment';
					$this->_res->fields['current_payer'] = $this->_res->fields['payer_id'];
				}
				$this->_valid = $this->payments[$claim_id]->valid();
			}
		}

		if ($nextClaim) {
			$this->_chargeIndex = 0;
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
		$format = DateObject::getFormat();

		$ds->setup($payment->_db,array(
				'cols' 	=> "
					payment_id,
					foreign_id,
					payment_type, 
					amount,
					writeoff,
					payer_id,
					date_format(payment_date,'$format') payment_date,
					pa.timestamp,
					u.username user,
					ref_num,
					pa.title
					",
				'from' 	=> '
					payment AS pa
					LEFT JOIN encounter AS e USING(encounter_id)
					LEFT JOIN person AS p ON (e.patient_id = p.person_id)
					LEFT JOIN clearhealth_claim AS chc ON (chc.claim_id = pa.foreign_id)
					LEFT JOIN encounter AS e2 ON(chc.encounter_id = e2.encounter_id)
					LEFT JOIN ordo_registry AS oreg ON(pa.payment_id = oreg.ordo_id)
					LEFT JOIN user AS u ON(oreg.creator_id = u.user_id)',
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

	/**
	 *
	 * Get an array for misc charges from the db using the patient id
	 */
	function &_miscChargeList($patient_id) {
		$pId = EnforceType::int($patient_id);
		
		$ds =& new Datasource_sql();

		$format = DateObject::getFormat();

		$ds->setup(Celini::dbInstance(),array(
				'cols' 	=> "
					cc.claim_id,
					mc.amount total_billed,
					mc.title current_payer,
					date_format(mc.charge_date,'$format') billing_date,
					'Misc Charge' identifier,
					user.username user

					",
				'from' 	=> '
					misc_charge mc
					inner join encounter e on mc.encounter_id = e.encounter_id
					inner join clearhealth_claim cc on e.encounter_id = cc.encounter_id
					inner join ordo_registry oreg on oreg.ordo_id = mc.misc_charge_id
					inner join user on oreg.creator_id = user.user_id
					',
				'where' => "e.patient_id = $pId"
			),
			false
		);
		//echo $ds->preview();
		$ret = array();
		for($ds->rewind();$ds->valid();$ds->next()) {
			$row = $ds->get();
			$ret[$row['claim_id']][] = $row;
		}
		return $ret;
	}
	
	function _getAdjustments($payment_id) {
		$db =& Celini::dbInstance();
		$sql = "SELECT * FROM eob_adjustment WHERE payment_id = $payment_id";
		$res = $db->execute($sql);
		$em =& Celini::enumManagerInstance();
		$enum =& $em->enumList('eob_adjustment_type');
		if(is_null($this->adjustmentTypes)) {
			$this->adjustmentTypes = array();
			for($enum->rewind();$enum->valid();$enum->next()) {
				$value = $enum->current();
				$this->adjustmentTypes[$value->key] = $value->value;
			}
		}
		$adjustments = array();
		while($res && !$res->EOF) {
			$adjustments[] = $res->fields;
			$res->MoveNext();
		}
		return $adjustments;
	}
}
?>
