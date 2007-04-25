<?php
class X12TransactionBuilder {
	var $ct;
	var $transactions = array();

	function build($tree) {
		foreach($tree as $section) {
			foreach($section as $key => $element) {
				switch((string)$key) {
					case 'ISA':
					case 'GS':
					case 'GE':
					case 'IEA':
						// headers/footers ignore
						break;
					default:
						$this->_parseTransaction($element);
						break;
				}
			}
		}
	}

	function _parseTransaction($transaction) {
		foreach($transaction as $key => $element) {
			switch((string)$key) {
				case 'payer':
					$this->_parsePayer($element);
					break;
				case 'payee':
					$this->_parsePayee($element);
					break;
				case 'header':
					$this->_parseHeader($element);
					break;
				case 'header_number':
					// ignore
					break;
				case 'summary':
					$this->_parseHeader($element);
					break;
				default:
					$this->_parseElement($element);
					break;
			}
		}
	}

	function _parseHeader($elements) {
		foreach($elements as $element) {
			$this->_parseElement($element);
		}
	}

	function _parsePayer($elements) {
		foreach($elements as $element) {
			switch($element->code) {
				case 'N1':
					$this->ct->payer->set('type',$element->fields['EntityIDCode']->value);
					// this should never happen but it did in one of my sample files
					if (isset($element->fields['Name'])) {
						$this->ct->payer->set('name',$element->fields['Name']->value);
					}
					break;
				case 'N3':
					$this->ct->payer->address->set('line1',$element->fields['AddressInfo1']->value);
					break;
				case 'N4':
					$this->ct->payer->address->set('city',$element->fields['City']->value);
					$this->ct->payer->address->set('state',$element->fields['StateCode']->value);
					$this->ct->payer->address->set('zip',$element->fields['PostalCode']->value);
					break;
				case 'REF':
					$this->ct->payer->set('identifier',$element->fields['ReferenceIdent']->value);
					$this->ct->payer->set('identifierType',$element->fields['ReferenceIdentQual']->value);
					break;
				case 'PER':
					$this->ct->payer->set('contactName',$element->fields['Name']->value);
					$this->ct->payer->set('contactInfo',$element->fields['CommNum']->value);
					$this->ct->payer->set('contactInfoType',$element->fields['CommNumQual']->value);
					break;
				default:
					var_dump($element);
					die();
					break;
			}
		}
	}

	function _parsePayee($elements) {
		foreach($elements as $element) {
			switch($element->code) {
				case 'N1':
					$this->ct->payee->set('type',$element->fields['EntityIDCode']->value);
					$this->ct->payee->set('name',$element->fields['Name']->value);
					break;
				case 'N3':
					$this->ct->payee->address->set('line1',$element->fields['AddressInfo1']->value);
					break;
				case 'N4':
					$this->ct->payee->address->set('city',$element->fields['City']->value);
					$this->ct->payee->address->set('state',$element->fields['StateCode']->value);
					$this->ct->payee->address->set('zip',$element->fields['PostalCode']->value);
					break;
				case 'REF':
					$this->ct->payee->set('identifier',$element->fields['ReferenceIdent']->value);
					$this->ct->payee->set('identifierType',$element->fields['ReferenceIdentQual']->value);
					break;
				default:
					var_dump($element);
					die();
					break;
			}
		}
	}

	function _parseElement($element) {
		if (is_array($element)) {
			$this->_parseBody($element);
			return;
		}
		switch($element->code) {
			case 'ISA':
			case 'GS':
			case 'GE':
			case 'IEA':
			case 'REF': // im not sure, but i don't know how i would use it
				// i don't care about these
				break;
			case 'ST':
				$this->ct = new X12Transaction();
				break;
			case 'BPR':
				$this->ct->summary->set('transactionType',$element->fields['TransactionCode']->value);
				$this->ct->summary->set('amount',$element->fields['MonetaryAmount']->value);

				// maybe move this into a setter???
				if ($element->fields['CredDebitFlag']->value == 'D') {
					var_dump($element);
					trigger_error("Trying to run a debit transaction, not implemented");
					die();
				}

				$this->ct->summary->set('date',$element->fields['Date']->value);
				break;
			case 'TRN':
				$this->ct->summary->set('identifier',$element->fields['ReferenceIdent']->value);
				$this->ct->summary->set('originatingCompanyId',$element->fields['OrigCompID']->value);
				break;
			case 'DTM':
				$this->ct->summary->set('productionDate',$element->fields['Date']->value);
				break;
			case 'SE':
				$this->transactions[] = $this->ct;
				unset($this->ct);
				break;
			case 'PLB':
				// Provider Level Adjustment
				break;
			default:
				var_dump($element);
				die();
				break;
		}
	}

	function _parseBody($sections) {
		foreach($sections as $sub => $d) {
			foreach($d as $section => $data) {
				switch($section) {
					case 'header_number':
						break;
					case 'claim_payment_info':
						$this->_parseClaimPayment($data);
						break;
					case 'service_payment_information':
						$this->_parseServicePayment($data);
						break;
				}
			}
		}
	}

	var $claimIndex = -1;
	function _parseServicePayment($payment) {
		$line = -1;
		$cas = -1;
		$ref = -1;
		$amt = -1;
		$lq = -1;
		foreach($payment as $element) {
			switch($element->code) {
				case 'SVC':
					$this->ct->details[$this->claimIndex]->lines[++$line] = new X12Transaction_Claimline();

					$this->ct->details[$this->claimIndex]->lines[$line]->set('procedure',$element->fields['CompMedProcedID']->value);
					$this->ct->details[$this->claimIndex]->lines[$line]->set('chargeAmount',$element->fields['MonetaryAmount']->value);
					$this->ct->details[$this->claimIndex]->lines[$line]->set('paymentAmount',$element->fields['MonetaryAmount2']->value);
					$this->ct->details[$this->claimIndex]->lines[$line]->set('quantity',$element->fields['Quantity']->value);
					
					break;
				case 'DTM':
					// were only covering single day servives here
					if ($element->fields['DateTimeQualifier']->value != 472) {
						var_dump($element);
						trigger_error('Only single day service dates supports (472)');
					}
					$this->ct->details[$this->claimIndex]->lines[$line]->set('serviceDate',$element->fields['Date']->value);
					break;
				case 'CAS':
					$this->ct->details[$this->claimIndex]->lines[$line]->adjustments[++$cas] = new X12Transaction_Adjustment();
					$this->ct->details[$this->claimIndex]->lines[$line]->adjustments[$cas]->set('group',$element->fields['ClaimAdjGroupCode']->value);
					$this->ct->details[$this->claimIndex]->lines[$line]->adjustments[$cas]->set('reason',$element->fields['ClaimAdhReasonCode']->value);
					$this->ct->details[$this->claimIndex]->lines[$line]->adjustments[$cas]->set('amount',$element->fields['MonetaryAmount']->value);
					break;
				case 'REF':
					++$ref;
					if ($ref == 0) {
						$this->ct->details[$this->claimIndex]->lines[$line]->set('adjustmentType',$element->fields['ReferenceIdentQual']->value);
						$this->ct->details[$this->claimIndex]->lines[$line]->set('adjustmentReason',$element->fields['ReferenceIdent']->value);
					} else if ($ref == 1) {
						$this->ct->details[$this->claimIndex]->lines[$line]->set('renderingProviderType',$element->fields['ReferenceIdentQual']->value);
						$this->ct->details[$this->claimIndex]->lines[$line]->set('renderingProvider',$element->fields['ReferenceIdent']->value);
					}
					else {
						var_dump($element);
						trigger_error('More Service Payment REFs then expected');
					}
					break;
				case 'AMT':
					$this->ct->details[$this->claimIndex]->lines[$line]->infoAmounts[++$amt] = new X12Transaction_AdjustmentAmount();
					$this->ct->details[$this->claimIndex]->lines[$line]->infoAmounts[$amt]->set('type',$element->fields['AmtQualCode']->value);
					$this->ct->details[$this->claimIndex]->lines[$line]->infoAmounts[$amt]->set('amount',$element->fields['MonetaryAmount']->value);
					break;
				case 'LQ':
					$this->ct->details[$this->claimIndex]->lines[$line]->remarks[++$lq] = new X12Transaction_AdjustmentAmount();
					$this->ct->details[$this->claimIndex]->lines[$line]->remarks[$lq]->set('type',$element->fields['CodeListQualCode']->value);
					$this->ct->details[$this->claimIndex]->lines[$line]->remarks[$lq]->set('amount',$element->fields['IndustryCode']->value);
					break;
			default:
				var_dump($element);
				die();
				break;
			}
		}
	}

	var $pcpDtm = 0;
	var $pcpNm1 = 0;
	function _parseClaimPayment($payment) {
		foreach($payment as $element) {
			switch($element->code) {
				case 'CLP':
					$this->claimIndex++;
					$this->ct->details[$this->claimIndex] = new X12Transaction_Claim();
					$this->ct->details[$this->claimIndex]->set('identifier',$element->fields['ClaimSubmtIdentifier']->value);
					$this->ct->details[$this->claimIndex]->set('reference',$element->fields['ReferenceIdent']->value);
					$this->ct->details[$this->claimIndex]->set('status',$element->fields['ClaimStatusCode']->value);
					$this->ct->details[$this->claimIndex]->set('planIndicator',$element->fields['ClaimFileIndCode']->value);
					$this->ct->details[$this->claimIndex]->set('totalChargeAmount',$element->fields['MonetaryAmount']->value);
					$this->ct->details[$this->claimIndex]->set('claimPaymentAmount',$element->fields['MonetaryAmount2']->value);
					$this->ct->details[$this->claimIndex]->set('patientResponsibilityAmount',$element->fields['MonetaryAmount3']->value);

					$this->pcpDtm = 0;
					$this->pcpNm1 = 0;
					break;
				case 'NM1':
					$index = false;
					switch($this->pcpNm1++) {
						case 0:
							$index = 'patient';
							break;
						case 1:
							$index = 'subscriber';
							break;
						case 2:
							$index = 'provider';
							break;
						case 3:
							$index = 'provider';
							break;
						case 4:
							$index = 'serviceProvider'; // im not sure that i care about this
							break;
						case 5:
							$index = 'payerName';
							break;
					}
					if(isset($element->fields['NameFirst']))
						$this->ct->details[$this->claimIndex]->$index->set('nameFirst',$element->fields['NameFirst']->value);
					if(isset($element->fields['NameLast']))
						$this->ct->details[$this->claimIndex]->$index->set('nameLast',$element->fields['NameLast']->value);
					if(isset($element->fields['NameMiddle']))
						$this->ct->details[$this->claimIndex]->$index->set('nameMiddle',$element->fields['NameMiddle']->value);

					if (isset($element->fields['IDCode'])) {
						$this->ct->details[$this->claimIndex]->$index->set('idType',$element->fields['IDCodeQualifier']->value);
						$this->ct->details[$this->claimIndex]->$index->set('id',$element->fields['IDCode']->value);
					}
					$this->ct->details[$this->claimIndex]->$index->set('type',$element->fields['EntityTypeQualifier']->value);
					break;
				case 'MOA':
				case 'MOI':
					// Medicare stuff, no clue if we actuall care
					break;
				case 'DTM':
					if ($this->pcpDtm++ == 0) {
						$this->ct->details[$this->claimIndex]->set('claimDate',$element->fields['Date']->value);
					}
					else {
						var_dump($element);
						die();
					}
					break;
				default:
					var_dump($element);
					die();
					break;
			}
		}
	}
}
