<?php
/**
 * A transaction that applies to a specific claim
 */
class TransactionClaim {
	var $claimId = false;
	var $type = 'debit';
	var $amount = 0.00;
	var $writeoff = 0.00;
	var $payerId = false;
	var $lines = false;
	var $paymentType = 'Insurance Payment';
	var $paymentDate;

	/**
	 * Set the claimId using the public claim identifier
	 */
	function setClaim($identifier) {
		$db = new clniDb();

		$this->claimId = $db->getOne("select claim_id from clearhealth_claim where identifier = ".$db->quote($identifier));
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
		if ($this->claimId === false) {
			Celini::raiseError('No Claim Set');
			return false;
		}
		if ($this->type === 'credit') {
			$claim =& Celini::newOrdo('ClearhealthClaim',$this->claimId);
			$payment =& Celini::newOrdo('Payment');
			$payment->set('foreign_id',$this->claimId);

			//$payment->set('user_id',$this->_me->get_id());

			$payment->set('payer_id',$this->payerId);
			$payment->set('title',$this->paymentType);
			$payment->set('payment_date',$this->paymentDate);
			$payment->persist();

			$paymentId = $payment->get('id');

			if (is_array($this->lines) && count($this->lines) > 0) {
				$total_paid = 0;
				$total_writeoff = 0;
				foreach($this->lines as $line) {
					unset($pcl);
					$pcl =& ORDataObject::factory('PaymentClaimline',0,$paymentId);
					$pcl->populate_array($line);
					$pcl->calculateCarry($claim->get('encounterId'));
					$pcl->persist();
					$total_paid += $pcl->get('paid');
					$total_writeoff += $pcl->get('writeoff');

				}
				$this->amount = $total_paid;
				$this->writeoff = $total_writeoff;
			}
			else {
				// we don't have an indivdual claimline breakdown so lets spread the payment among all the claims lines
				$codingData =& Celini::newOrdo('CodingData');
				$codeList = $codingData->getCodeList($claim->get('encounter_id'));

				$numCodes = count($codeList);
				$paid = $this->amount;

				$cl = array();
				$carry = array();
				foreach($codeList as $key => $code) {
					$cl[$key] =& Celini::newOrdo('PaymentClaimline');
					$cl[$key]->set('payment_id',$paymentId);
					$cl[$key]->set('code_id',$code['code_id']);
					$cl[$key]->calculateCarry($claim->get('encounter_id'));

					$carry[$key] = $cl[$key]->get('carry');
				}

				$totalCarry = array_sum($carry);

				foreach($carry as $key => $val) {
					if ($numCodes == 1) {
						$p = $paid;
					}
					else {
						$p = $this->amount * ($val/$totalCarry);
						$paid -= $p;
					}
					$cl[$key]->set('paid',$p);
					$cl[$key]->set('carry',$val-$p);
					$cl[$key]->persist();
					$numCodes--;
				}
			}

			$payment->set('amount',$this->amount);
			$payment->set('writeoff',$this->writeoff);
			$payment->persist();
	
			// update claim total
			$claim->set('total_paid',$claim->get('total_paid')+$this->amount);
			$claim->persist();
		}
		else {
			Celini::raiseError('debit not implmented');
		}
	}
}
?>
