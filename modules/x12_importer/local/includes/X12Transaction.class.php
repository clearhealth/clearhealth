<?php
class X12Transaction {
	var $payee;
	var $payer;
	var $summary;
	var $details = array();

	function X12Transaction() {
		$this->payee = new X12Transaction_Payee();
		$this->payer = new X12Transaction_Payer();
		$this->summary = new X12Transaction_Summary();
	}
}

class X12Transaction_element {

	function set($key,$val) {
		$method = "set$key";
		if (method_exists($this,$method)) {
			$this->$method($val);
		}
		else {
			$this->$key = $val;
		}
	}

	function get($key) {
		$method = "get$key";
		if (method_exists($this,$method)) {
			return $this->$method();
		}
		else {
			return $this->$key;
		}
	}
}
class X12Transaction_Entity extends X12Transaction_element {
	var $type;

	var $identifier;

	/**
	 * 2U Payer ID #
	 * EO Submitter ID #
	 * HI Health industyr # (HIN)
	 * NF National Association of Insurance Commissioners (NAIC) Code
	 */
	var $identifierType;

	var $address;

	function X12Transaction_Entity() {
		$this->address = new X12Transaction_Address();
	}
}

class X12Transaction_Address extends X12Transaction_element {
	var $line1;
	var $line2;
	var $city;
	var $state;
	var $zip;
}

class X12Transaction_Person extends X12Transaction_Entity {
	var $nameFirst;
	var $nameLast;
	var $nameMiddle;

	/**
	 * 34 Social Security #
	 * HN HIC
	 * II US national idviudual identifier
	 * MI Member identification Number
	 * MR Medicad Recipient ID #
	 */
	var $idType;
	var $id;

	var $idTypeMap = array(
		34 	=> 'SSN',
		'HN'	=> 'HIC',
		);

	/**
	 * 1  Person
	 * 2  Non-Person Entity
	 * 82 Rendering Provider
	 * TT Transfer To
	 */
	var $type;

	function getidType() {
		if (isset($this->idTypeMap[$this->idType])) {
			return $this->idTypeMap[$this->idType];
		}
		return $this->idType;
	}
}

class X12Transaction_Payee extends X12Transaction_Entity {
	function setType($type) {
		if ($type !== 'PE') {
			trigger_error("Payees must have a type of PE: type provided $type");
		}
		$this->type = $type;
	}
}
class X12Transaction_Payer extends X12Transaction_Entity {
	var $name;

	var $contactName;
	var $contactInfo;
	var $contactInfoType;

	function setType($type) {
		if ($type !== 'PR') {
			trigger_error("Payers must have a type of PR: type provided $type");
		}
		$this->type = $type;
	}

	function setContactInfoType($type) {
		$map = array(
			'EM' => 'Email',
			'FX' => 'Fax',
			'TE' => 'Phone'
			);
		$this->contactInfoType = $map[$type];
	}
}
class X12Transaction_Summary extends X12Transaction_element {
	/**
	 * C Payment, Remittance Advice
	 * D Payment only
	 * H Notification only
	 * I Remittance info only
	 * P Prenotification of Fture Transfers
	 * U Split Payment and Remittance
	 * X Handling Party’s Option to Split Payment and Remittance
	 */
	var $transactionType;

	/**
	 * Amount the transaction is for
	 */
	var $amount = 0.00;

	/**
	 * Credit/Debit
	 */
	var $type = 'credit';

	/**
	 * Payment method
	 *
	 * ACH Automated Clearing House
	 * BOP Financial Institution Option
	 * CHK Check
	 * FWT Federal Reserve Funds/Write Transfer
	 * NON Non-Payment Data
	 * CDP Cash Concentration/Disbursement plus Addenda
	 * CTX Corporate Trade Exchange
	 */
	var $method;

	/**
	 * Combined format YYYYMMDD
	 */
	var $date;

	var $productionDate;

	/**
	 * Transaction Identifier
	 */
	var $identifier;

	/**
	 * Orginating company id
	 */
	var $originatingCompanyId;

	function getDate() {
		$date = DateObject::create($this->date);
		return $date->toString();
	}
	
}
class X12Transaction_Claimline extends X12Transaction_element {
	/**
	 * Format is "CodeType:Code"
	 *
	 * Code Type List:
	 * AD American Dental Association Codes
	 * HC Health Care Financing Administration Common Procedural Coding System (HCPCS) Codes (CPT)
	 * ID International Classification of Diseases Clinical Modification (ICD-9-CM) - Procedure
	 * IV Home Infusion EDI Coalition (HIEC) Product/Service Code Product/Service Code List
	 * N1 National Drug Code in 4-4-2 Format
	 * N2 National Drug Code in 5-3-2 Format
	 * N3 National Drug Code in 5-4-1 Format
	 * N4 National Drug Code in 5-4-2 Format
	 * ND National Drug Code (NDC)
	 * NU National Uniform Billing Committee (NUBC) UB92 Codes
	 * RB National Uniform Billing Committee (NUBC) UB82 Codes
	 * ZZ Mutually Defined the Health Insurance Prospective Payment System (HIPPS) Skilled Nursing Facility Rate Code.
	 */
	var $procedure;

	function getProcedureType() {
		$type = substr($this->procedure,0,2);
		return $type;
	}

	function getProcedureCode() {
		return substr($this->procedure,3);
	}

	var $chargeAmount;
	var $paymentAmount;

	var $quantity;

	var $serviceDate;

	/**
	 * 1S Ambulatory Patient Group (APG) Number
	 * 6R Provider Control Number
	 * BB Authorization Number
	 * E9 Attachment Code
	 * G1 Prior Authorization Number
	 * G3 Predetermination of Benefits Identification Number
	 * LU Location Number
	 * RB Rate code number
	 */ 
	var $adjustmentType;
	var $adjustmentReason;

	/**
	 * 1A Blue Cross Provider Number
	 * 1B Blue Shield Provider Number
	 * 1C Medicare Provider Number
	 * 1D Medicaid Provider Number
	 * 1G Provider UPIN Number
	 * 1H CHAMPUS Identification Number
	 * 1J Facility ID Number
	 * HPI Health Care Financing Administration National Provider Identifier
	 * SY Social Security Number
	 * TJ Federal Taxpayer’s Identification Number
	 */ 
	var $renderingProviderType;
	var $renderingProvider;

	var $adjustments = array();
	var $infoAmounts = array();
	var $remarks = array();

	function getPRAmount() {
		foreach($this->adjustments as $amount) {
			if ($amount->group == 'PR') {
				return $amount->get('amount');
			}
		}
		return 0.00;
	}
	function getCOAmount() {
		foreach($this->adjustments as $amount) {
			if ($amount->group == 'CO') {
				return $amount->get('amount');
			}
		}
		return 0.00;
	}
	function getOAAmount() {
		foreach($this->adjustments as $amount) {
			if ($amount->group == 'OA') {
				return $amount->get('amount');
			}
		}
		return 0.00;
	}

	function getServiceDate() {
		$date = DateObject::create($this->serviceDate);
		return $date->toString();
	}
}
class X12Transaction_AdjustmentAmount extends X12Transaction_element {
	/**
	 * B6 Allowed - Actual
	 * DY Per Day Limit
	 * KH Deduction Amount
	 * NE Net Billed
	 * T Tax
	 * T2 Total Claim Before Taxes
	 * ZK Federal Medicare or Medicaid Payment Mandate - Category 1
	 * ZL Federal Medicare or Medicaid Payment Mandate - Category 2
	 * ZM Federal Medicare or Medicaid Payment Mandate - Category 3
	 * ZN Federal Medicare or Medicaid Payment Mandate - Category 4
	 * ZO Federal Medicare or Medicaid Payment Mandate - Category 5
	 */
	var $type;
	var $amount;
}
class X12Transaction_AdjustmentRemarks extends X12Transaction_element {
	/**
	 * HE Claim Payment Remark Codes
	 * RX National Council for Prescription Drug Programs Reject/Payment Codes
	 */
	var $type;
	var $remark;
}
class X12Transaction_Adjustment extends X12Transaction_element {
	/**
	 * CO Contractual Obligations
	 * CR Correction and Reversals
	 * OA Other adjustments
	 * PI Payor Initiated Reductions
	 * PR Patient Responsibility
	 */
	var $group;

	var $reason;

	var $amount;
}
class X12Transaction_Claim extends X12Transaction_element {

	function X12Transaction_Claim() {
		$this->patient = new X12Transaction_Person();
		$this->subscriber = new X12Transaction_Person();
		$this->provider = new X12Transaction_Person();
		$this->crossOver = new X12Transaction_Person();
	}

	var $lines = array();

	var $patient;
	var $subscriber;
	var $provider;
	var $crossOver;

	var $identifier;
	var $reference;

	/**
	 * 1 Processed as primary
	 * 2 Processed as Secondary
	 * 3 Processed as Tertiary
	 * 4 Denied
	 * 19 Processed as Primary forwared to additional
	 * 20 Processed as secondary forwared to additional
	 * 21 Processed as tertiary forwared to additional
	 * 22 Reversal of previous payment
	 * 23 Not our claim forwarded
	 * 25 Predetermination pricing only - no payment
	 */
	var $status;
	var $statusMap = array(
		1 => 'Primary (1)',
		2 => 'Secondary (2)',
		3 => 'Tertiary (3)',
		4 => 'Denied (4)',
		19 => 'Primary+ (19)',
		20 => 'Secondary+ (20)',
		21 => 'Tertiary+ (21)',
		22 => 'Reversal (22)',
		23 => 'Forwarded (23)',
		25 => 'Pricing Only (25)'
		);

	var $totalChargeAmount;
	var $claimPaymentAmount;
	var $patientResponsibilityAmount;

	/**
	 * 12 Preferred Provider Organization (PPO)
	 * 13 Point of Service (POS)
	 * 14 Exclusive Provider Organization (EPO)
	 * 15 Indemnity Insurance
	 * 16 Health Maintenance Organization (HMO) Medicare
	 * AM Automobile Medical
	 * CH Champus
	 * DS Disability
	 * HM Health Maintenance Organization
	 * LM Liability Medical
	 * MA Medicare Part A
	 * MB Medicare Part B
	 * MC Medicaid
	 * OF Other Federal Program
	 * TV Title V
	 * VA Veteran Administration Plan
	 * WC Workers’ Compensation Health Claim
	 */ 
	var $planIndicator;

	var $planIndicatorMap = array(
		'12' => '12 - Preferred Provider Organization (PPO)',
		'13' => '13 - Point of Service (POS)',
	 	'14' => '14 - Exclusive Provider Organization (EPO)',
		'15' => '15 - Indemnity Insurance',
	 	'16' => '16 - Health Maintenance Organization (HMO) Medicare',
		'AM' => 'AM - Automobile Medical',
	 	'CH' => 'CH - Champus',
		'DS' => 'DS - Disability',
	 	'HM' => 'HM - Health Maintenance Organization',
		'LM' => 'LM - Liability Medical',
		'MA' => 'MA - Medicare Part A',
		'MB' => 'MB - Medicare Part B',
		'MC' => 'MC - Medicaid',
		'OF' => 'OF - Other Federal Program',
		'TV' => 'TV - Title V',
		'VA' => 'VA - Veteran Administration Plan',
		'WC' => 'WC - Workers’ Compensation Health Claim',
		);

	var $claimDate;

	function getStatus() {
		if (isset($this->statusMap[$this->status])) {
			return $this->statusMap[$this->status];
		}
		return $this->status;
	}

	function getClaimDate() {
		$date = DateObject::create($this->claimDate);
		return $date->toString();
	}

	function getPlanIndicator() {
		if (isset($this->planIndicatorMap[$this->planIndicator])) {
			return $this->planIndicatorMap[$this->planIndicator];
		}
	}
}
?>
