<?php
/**
 * A debit only object will calculate how much a claim will cost
 */
class TransactionEstimateClaim {
	var $encounterId = false;
	var $type = 'debit';
	var $amount = 0.00;
	var $payerId = false;
	var $fees = false;

	/**
	 * Set the encounterId
	 */
	function setEncounterId($id) {
		$this->encounterId = $id;
	}

	/**
	 * Set the payer by id
	 */
	function setPayerId($payerId) {
		$this->payerId = $payerId;
	}

	/**
	 * Set the payer by name
	 */
	function setPayer($companyName,$programName) {
		$db = new clniDb();

		$coName = $db->quote($companyName);
		$pName = $db->quote($programName);
		$this->payerId = $db->getOne(
					"select 
						insurance_program_id 
					from insurance_program ip 
						inner join company c using(company_id) 
					where 
						c.name = $coName and
						ip.name = $pName");
	}

	/**
	 * 
	 */
	function processClaim() {
		if ($this->payerId === false) {
			Celini::raiseError('No Payer Set');
			return false;
		}
		if ($this->encounterId === false) {
			Celini::raiseError('No Encounter Set');
			return false;
		}
		if ($this->type === 'credit') {
			Celini::raiseError("Credit not possible: Estimating payments doesn't make sense");
		}
		else {
			$this->_populateFeesArray();
		}
		return $this->fees;
	}
	
	
	/**
	 * Handles populated the fees array from {@link $encounterId}
	 *
	 * @access protected
	 */
	function _populateFeesArray() {
		if ($this->encounterId <= 0) {
			return;
		}
		$encounter =& Celini::newOrdo('Encounter',$this->encounterId);
		$ip =& Celini::newOrdo('InsuranceProgram',$encounter->get('current_payer'));

		$fs = $ip->get('fee_schedule_id');
		if ($fs == 0) {
			Celini::newORDO('FeeSchedule');
			$feeSchedule =& FeeSchedule::defaultFeeSchedule();
		}
		else {
			$feeSchedule =& Celini::newORDO('FeeSchedule',$fs);
		}
		
		// we don't have an indivdual claimline breakdown so lets spread the payment among all the claims lines
		$codingData =& Celini::newOrdo('CodingData');
		$codeList = $codingData->getCodeList($this->encounterId);

		$numCodes = count($codeList);

		$this->fees = array();
		$total = 0;
		$i = 0;
		foreach($codeList as $key => $code) {
			$this->fees[$i]['code'] = $code['code'];
			$this->fees[$i]['fee']  = $feeSchedule->getFee($code['code']);
			$i++;
			$total += $code['fee'];
		}
		if ($total > 0) {
			$this->_addTotalRow($total);
		}
	}
	
	function _addTotalRow($total) {
		$index = count($this->fees);
		$this->fees[$index]['code'] = '<b>Total</b>';
		$this->fees[$index]['fee']  = number_format($total,2);
	}
}
?>
